<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->arResult['ELEMENT']=$this->getElement();
$this->arResult['GROUPS']=$this->getGroups();

$this->IncludeComponentTemplate();

return $this->arResult['GROUPS'] && (count($this->arResult['GROUPS']) > 0);
?>
