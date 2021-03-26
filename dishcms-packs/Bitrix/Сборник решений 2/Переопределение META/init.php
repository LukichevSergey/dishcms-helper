Инфоблок где 
NAME - ссылка на страницу, 
свойство META_TITLE типа Строка является META заголовком, 
свойство META_DESC типа TEXT является META описанием, 
свойство H1 типа Строка является заголовком H1, если вывод заголовка производиться методом $APPLICATION->ShowTitle(false);
PREVIEW_TEXT - переопределение текста
В шаблоне напр. catalog.section <? if($meta=kontur_get_remeta()) { echo $meta['PREVIEW_TEXT']; } else { echo $arResult['~DESCRIPTION']; }?>
<?php
if(!function_exists('kontur_get_remeta')) {
	function kontur_get_remeta() {
		$meta=null;
		$uri=trim(preg_replace('/\?.*$/', '', $_SERVER['REQUEST_URI']), '/');
		$rs=\CIBlockElement::GetList(array('SORT'=>'ASC'), array('IBLOCK_ID'=>34, 'NAME'=>'%'.$uri.'%'), false, false, array('ID', 'NAME', 'PROPERTY_META_DESC', 'PROPERTY_META_TITLE', 'PROPERTY_H1', 'PREVIEW_TEXT'));
    	while($elm=$rs->Fetch()) { 
    		$url=trim(preg_replace('/\?.*$/', '', $elm['NAME']), '/');
    		if(($url == $uri) || ($url == 'http://' . $_SERVER['SERVER_NAME'] . '/' . $uri) || ($url == 'https://' . $_SERVER['SERVER_NAME'] . '/' . $uri)) {
    			$meta=$elm;
    			break;
    		}
    	}
    	return $meta;
	}
}

AddEventHandler('main', 'OnEpilog', 'onEpilog', 1);
function onEpilog(){
    global $APPLICATION;
	if($meta=kontur_get_remeta()) {
		if(!empty($meta['PROPERTY_META_TITLE_VALUE'])) {
			$APPLICATION->SetPageProperty("title", $meta['PROPERTY_META_TITLE_VALUE']);
		}
		if(!empty($meta['PROPERTY_META_DESC_VALUE']['TEXT'])) {
			$APPLICATION->SetPageProperty("description", $meta['PROPERTY_META_DESC_VALUE']['TEXT']);
		}
		if(!empty($meta['PROPERTY_H1_VALUE'])) {
			$APPLICATION->SetTitle($meta['PROPERTY_H1_VALUE']);
		}
	}
}
