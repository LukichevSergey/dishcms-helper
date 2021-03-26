<?
/**
 * @see http://dev.1c-bitrix.ru/support/forum/forum6/topic69864/
 * PHP >= 5.4
 */
$_SERVER['DOCUMENT_ROOT']=dirname(__FILE__);
require($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/prolog_before.php");

$IBLOCK_ID=5;

if (!CModule::IncludeModule("iblock")) {
  die('Модуль "Инфоблоки" не найден!');
}

$offset=0;
$h = 500;
$w = 500;
//do {
    $rs=\Bitrix\Iblock\ElementTable::getList(array(
        'select'=>['ID'],
        'filter'=>['=IBLOCK_ID'=>$IBLOCK_ID],
//        'limit'=>500,
//        'offset'=>$offset
   	));
    $el = new CIBlockElement;
    while($elm=$rs->fetch()) {
    	var_dump($el->Update($elm['ID'], array('PREVIEW_TEXT_TYPE'=>'html', 'DETAIL_TEXT_TYPE'=>'html')));
    }
//    $offset+=500;
//} while($rs->getSelectedRowsCount() > 0);
