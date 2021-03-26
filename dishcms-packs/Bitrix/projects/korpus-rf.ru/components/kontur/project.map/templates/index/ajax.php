<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
global $APPLICATION;
if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) || ($_REQUEST['AJAX_CALL']=='Y')) {
	if(isset($_POST['id'])) {
		\CModule::includeModule('iblock');
		$rs=\CIBlockElement::GetList([], ['IBLOCK_ID'=>31, '=PROPERTY_MAP_AREA_ID'=>(int)$_POST['id']], false, false, ['ID', 'NAME', 'PREVIEW_TEXT', 'DETAIL_PAGE_URL', 'PROPERTY_REGIONS']);
		if($el=$rs->GetNext()) {
			$APPLICATION->RestartBuffer();
			echo json_encode([
				'success'=>true,
				'data'=>[
					'NAME'=>$el['NAME'],
					'PREVIEW_TEXT'=>$el['PREVIEW_TEXT'],
					'DETAIL_PAGE_URL'=>$el['DETAIL_PAGE_URL'],
					'PROPERTY_REGIONS_VALUE'=>$el['PROPERTY_REGIONS_VALUE'],
					'PROPERTY_REGIONS_DESCRIPTION'=>$el['PROPERTY_REGIONS_DESCRIPTION']
				]
			]);
			exit;
		}
	}
	
	$APPLICATION->RestartBuffer();
	echo json_encode(['success'=>false]);
	exit;
}
else {
	\CHTTP::SetStatus("404 Not Found");
	$APPLICATION->RestartBuffer();
	exit;
}