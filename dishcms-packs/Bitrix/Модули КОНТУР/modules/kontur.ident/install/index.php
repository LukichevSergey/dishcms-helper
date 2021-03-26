<?php
use Bitrix\Main\Application;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\IO;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class kontur_ident extends \CModule
{
	public $MODULE_ID = 'kontur.ident';
	
	public function __construct()
	{
		@include __DIR__ . '/version.php';
		
		$this->MODULE_VERSION = $arModuleVersion['VERSION'];
		$this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
		
		$this->PARTNER_NAME = Loc::getMessage('KONTUR_IDENT_PARTNER_NAME');
		$this->PARTNER_URI = "https://kontur-lite.ru";
		
		$this->MODULE_NAME = Loc::getMessage('KONTUR_IDENT_MODULE_NAME');
		$this->MODULE_DESCRIPTION = Loc::getMessage('KONTUR_IDENT_MODULE_DESCRIPTION');
	}
	
	public function DoInstall()
	{
		global $DB, $APPLICATION;
		
		if (!IsModuleInstalled($this->MODULE_ID)) {
			$this->installDB();
			$this->installFiles();
			
			ModuleManager::registerModule($this->MODULE_ID);
			
			$this->registerAgents();
			
			$APPLICATION->IncludeAdminFile(Loc::getMessage('KONTUR_IDENT_INSTALL_TITLE'), __DIR__ . '/step.php');
		}
	}

	public function DoUninstall()
	{
		global $DB, $APPLICATION;
		
		$this->unInstallDB();
		$this->unInstallFiles();
		$this->unregisterAgents();

		ModuleManager::unRegisterModule($this->MODULE_ID);
		
		$APPLICATION->IncludeAdminFile(Loc::getMessage('KONTUR_IDENT_INSTALL_TITLE'), __DIR__ . '/unstep.php');
	}

	public function installDB()
    {
        global $DB;
        $this->errors = false;
        $this->errors = $DB->RunSQLBatch(Application::getDocumentRoot() . "/local/modules/{$this->MODULE_ID}/install/db/install.sql");
        if (!$this->errors) { 
            return true;
        } else {
			return $this->errors;
		}
    }
 
    public function unInstallDB()
    {
        global $DB;
        $this->errors = false;
        $this->errors = $DB->RunSQLBatch(Application::getDocumentRoot() . "/local/modules/{$this->MODULE_ID}/install/db/uninstall.sql");
        if (!$this->errors) {
            return true;
        } else {
			return $this->errors;
		}
    }

	public function installFiles()
    {
        copyDirFiles(
            __DIR__ . '/admin',
            Application::getDocumentRoot() . '/bitrix/admin',
            true, true
		);
		copyDirFiles(
            __DIR__ . '/components',
            Application::getDocumentRoot() . '/local/components',
            true, true
		);
        copyDirFiles(
            __DIR__ . '/public',
            Application::getDocumentRoot() . '/',
            true, true
        );
	}
	
	public function unInstallFiles()
    {
		DeleteDirFiles(
            __DIR__ . '/admin',
            Application::getDocumentRoot() . '/bitrix/admin'
		);
		
		IO\Directory::deleteDirectory(Application::getDocumentRoot() . '/local/components/kontur/ident.admin');		
        IO\Directory::deleteDirectory(Application::getDocumentRoot() . '/ident');
	}
	
	public function registerAgents()
	{
		$r=\CAgent::AddAgent(
			'\Kontur\Ident\Agent\DeleteOldTickets::run();',
			'kontur.ident',
			'Y',
			86400,
			date('d.m.Y 04:00:00'),
			'Y',
			date('d.m.Y 04:00:00'),
			8100
		);
		$a=1;
	}

	public function unregisterAgents()
	{
		\CAgent::RemoveModuleAgents('kontur.ident');
	}	
}
?>