<?
/**
 * @see http://dev.1c-bitrix.ru/support/forum/forum6/topic69864/
 * PHP >= 5.4
 */
$_SERVER['DOCUMENT_ROOT']=dirname(__FILE__);
require($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/prolog_before.php");

$IBLOCK_ID=15;
$LOGFILE='/home/bitrix/www/d2a.log';

if (!CModule::IncludeModule("iblock")) {
  die('Модуль "Инфоблоки" не найден!');
}

function kontur_log($msg) use ($LOGFILE) 
{
    file_put_contents($LOGFILE, $msg, FILE_APPEND);
}

set_time_limit(0);

$offset=0;
$h = 500;
$w = 500;
do {
    $rs=\Bitrix\Iblock\ElementTable::getList([
        'select'=>['ID', 'DETAIL_PICTURE', 'PREVIEW_PICTURE'],
        'filter'=>['=IBLOCK_ID'=>$IBLOCK_ID],
        'limit'=>500,
        'offset'=>$offset
   	]);
    
    while($elm=$rs->fetch()) {
        if (($elm['DETAIL_PICTURE'] != '') && ($elm['PREVIEW_PICTURE'] == '')) {
            $preview = CFile::ResizeImageGet($elm['DETAIL_PICTURE'], ['width'=>$w, 'height'=>$h], BX_RESIZE_IMAGE_PROPORTIONAL, false);
            $data = ['PREVIEW_PICTURE' => CFile::MakeFileArray($preview['src'])];
       
            if(\Bitrix\Iblock\ElementTable::update($elm['ID'], $data)) {
                kontur_log("Элемент {$elm['ID']} обновлён (d2a).<br />\n");
            }
        } elseif (($elm['PREVIEW_PICTURE'] != '') && ($elm['DETAIL_PICTURE'] == '')) {
            $old = CFile::GetFileArray($elm['PREVIEW_PICTURE']);
    
            $data = ['DETAIL_PICTURE' => CFile::MakeFileArray($old['SRC'])];
            if (($old['WIDTH'] > $w) || ($old['HEIGHT'] > $h)) {
                if(\Bitrix\Iblock\ElementTable::update($elm['ID'], $data)) {
                    $new = CFile::ResizeImageGet($elm['PREVIEW_PICTURE'], ['width'=>$w, 'height'=>$h], BX_RESIZE_IMAGE_PROPORTIONAL, false);
                    $data = ['PREVIEW_PICTURE' => CFile::MakeFileArray($new['src'])];
                }
            }
            if(\Bitrix\Iblock\ElementTable::update($elm['ID'], $data)) {
                kontur_log("Элемент {$elm['ID']} обновлён (a2d).<br />\n");
            }
        }
    }
    $offset+=500;
} while($rs->getSelectedRowsCount() > 0);
