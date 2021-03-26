<?php
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class kontur_references_csc extends CModule
{
	var $MODULE_ID = "kontur.references.csc";
	
	public function __construct()
	{
		include( dirname(__FILE__) . DIRECTORY_SEPARATOR . "version.php" );
	
		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
	
		$this->PARTNER_NAME = GetMessage("KONTUR_REF_CSC_PARTNER_NAME");
		$this->PARTNER_URI = "http://kontur-lite.ru";
	
		$this->MODULE_NAME = GetMessage("KONTUR_REF_CSC_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("KONTUR_REF_CSC_MODULE_DESCRIPTION");
	}
	
	public function DoInstall()
	{
		global $DB, $APPLICATION;
	
		RegisterModule($this->MODULE_ID);
	
		$APPLICATION->IncludeAdminFile(GetMessage("KONTUR_REF_CSC_INSTALL_TITLE"), dirname(__FILE__) . DIRECTORY_SEPARATOR . "step.php");
	}
	
	public function DoUninstall()
	{
		global $DB, $APPLICATION;
	
		UnRegisterModule($this->MODULE_ID);
	
		$APPLICATION->IncludeAdminFile(GetMessage("KONTUR_REF_CSC_INSTALL_TITLE"), dirname(__FILE__) . DIRECTORY_SEPARATOR . "unstep.php");
	}
}
?>