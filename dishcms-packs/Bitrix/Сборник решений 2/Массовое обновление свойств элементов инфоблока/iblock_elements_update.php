<?
/**
 * @see http://dev.1c-bitrix.ru/support/forum/forum6/topic69864/
 * PHP >= 5.4
 */
$_SERVER['DOCUMENT_ROOT']=dirname(__FILE__);
require($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/prolog_before.php");

$IBLOCK_ID=<IBLOCK_ID>;

if (!CModule::IncludeModule("iblock")) {
  die('Модуль "Инфоблоки" не найден!');
}

function kontur_log($msg) 
{
    echo $msg;
    // file_put_contents(__DIR__ . '/update'.date('_d_m_Y').'.log', $msg, FILE_APPEND);
}

set_time_limit(0);

//$offset=0;
//do {
    $rs=CIblockElement::getList(Array("SORT"=>"ASC"),['IBLOCK_ID'=>$IBLOCK_ID],false,false,['ID','IBLOCK_ID','ACTIVE']);
    
    while($elm=$rs->GetNext()) {
        $data=[];
        // $data['PROPERTY_VALUES']['MY_PROPERTY']='MY_VALUE';
        // пустое обвноление активности элементов. 
        // при переносе элементов в другой раздел в версии 16.5 они перестают отображаться.
        // в более свежих версиях наличие ошибки не проверялось.
        $data['ACTIVE']=$elm['ACTIVE'];
        $el = new CIBlockElement;
        if($el->Update($elm['ID'], $data)) { 
            kontur_log("Элемент {$elm['ID']} обновлён<br />\n");
        }
    }
//    $offset+=500;
//} while($rs->getSelectedRowsCount() > 0);
