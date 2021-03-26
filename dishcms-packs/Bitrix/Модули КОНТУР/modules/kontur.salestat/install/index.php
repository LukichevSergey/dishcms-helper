<?php
use Bitrix\Main\Application;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\IO;
use Bitrix\Main\Localization\Loc;

require_once dirname(__FILE__) . '/../lib/helper.php';

Loc::loadMessages(__FILE__);

class kontur_salestat extends \CModule
{
	public $MODULE_ID = 'kontur.salestat';
	
	public function __construct()
	{
		@include __DIR__ . '/version.php';
		
		$this->MODULE_VERSION = $arModuleVersion['VERSION'];
		$this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
		
		$this->PARTNER_NAME = Loc::getMessage('KONTUR_SALESTAT_PARTNER_NAME');
		$this->PARTNER_URI = "http://kontur-lite.ru";
		
		$this->MODULE_NAME = Loc::getMessage('KONTUR_SALESTAT_MODULE_NAME');
		$this->MODULE_DESCRIPTION = Loc::getMessage('KONTUR_SALESTAT_MODULE_DESCRIPTION');
	}
	
	public function DoInstall()
	{
		global $DB, $APPLICATION;
		
		if (!IsModuleInstalled($this->MODULE_ID)) {
			$this->installFiles();

			ModuleManager::registerModule($this->MODULE_ID);
		
			$APPLICATION->IncludeAdminFile(Loc::getMessage('KONTUR_SALESTAT_INSTALL_TITLE'), __DIR__ . '/step.php');
		}
	}

	public function DoUninstall()
	{
		global $DB, $APPLICATION;
		
		$this->uninstallFiles();

		ModuleManager::unRegisterModule($this->MODULE_ID);
		
		$APPLICATION->IncludeAdminFile(Loc::getMessage('KONTUR_SALESTAT_INSTALL_TITLE'), __DIR__ . '/unstep.php');
	}

	public function installFiles()
    {
        copyDirFiles(
            __DIR__ . '/admin',
            Application::getDocumentRoot() . '/bitrix/admin',
            true, true
        );
	}
	
	public function uninstallFiles()
    {
		DeleteDirFiles(
            __DIR__ . '/admin',
            Application::getDocumentRoot() . '/bitrix/admin'
		);
	}
}
?>