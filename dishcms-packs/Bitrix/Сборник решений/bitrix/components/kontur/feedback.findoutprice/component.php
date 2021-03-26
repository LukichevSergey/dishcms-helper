<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(!empty($_REQUEST['id']) && !empty($_REQUEST['validate'])) {
	$this->Validate($_REQUEST['id'], $_REQUEST['data']);
}
elseif(!empty($_REQUEST['id']) && !empty($_REQUEST['send'])) {
	$APPLICATION->RestartBuffer();
	if($this->Validate($_REQUEST['id'], $_REQUEST['data'], true)) {
		if($this->Send($_REQUEST['id'], $_REQUEST['data'])) {
			echo json_encode(array('success'=>true));
		}
		else {
			echo json_encode(array('success'=>false, 'error'=>'Заявка не была отправлена'));
		}
	}
	else {
		echo json_encode(array('success'=>false, 'error'=>'Переданные данные не прошли валидацию'));
	}
	die;
}
elseif(!empty($_REQUEST['id'])) {
	$arResult=$this->GetItem($_REQUEST['id']);
}

$this->IncludeComponentTemplate();
?>
