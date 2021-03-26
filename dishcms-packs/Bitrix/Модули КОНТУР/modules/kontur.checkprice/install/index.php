<?php
use Bitrix\Main\Application;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\IO;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class kontur_checkprice extends \CModule
{
	public $MODULE_ID = 'kontur.checkprice';
	
	public function __construct()
	{
		@include __DIR__ . '/version.php';
		
		$this->MODULE_VERSION = $arModuleVersion['VERSION'];
		$this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
		
		$this->PARTNER_NAME = Loc::getMessage('KONTUR_CHECK_PRICE_PARTNER_NAME');
		// $this->PARTNER_URI = "http://kontur-lite.ru";
		
		$this->MODULE_NAME = Loc::getMessage('KONTUR_CHECK_PRICE_MODULE_NAME');
		$this->MODULE_DESCRIPTION = Loc::getMessage('KONTUR_CHECK_PRICE_MODULE_DESCRIPTION');
	}
	
	public function DoInstall()
	{
		global $DB, $APPLICATION;
		
		if (!IsModuleInstalled($this->MODULE_ID)) {
			$this->installDB();
			$this->installFiles();
			$this->registerAgents();
			$this->createEvents();

			ModuleManager::registerModule($this->MODULE_ID);
		
			$APPLICATION->IncludeAdminFile(Loc::getMessage('KONTUR_CHECK_PRICE_INSTALL_TITLE'), __DIR__ . '/step.php');
		}
	}

	public function DoUninstall()
	{
		global $DB, $APPLICATION;
		
		$this->unInstallDB();
		$this->unInstallFiles();
		$this->unregisterAgents();
		$this->removeEvents();

		ModuleManager::unRegisterModule($this->MODULE_ID);
		
		$APPLICATION->IncludeAdminFile(Loc::getMessage('KONTUR_CHECK_PRICE_INSTALL_TITLE'), __DIR__ . '/unstep.php');
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
		
		IO\Directory::deleteDirectory(Application::getDocumentRoot() . '/local/components/kontur/checkprice');
		IO\Directory::deleteDirectory(Application::getDocumentRoot() . '/local/components/kontur/checkprice.btn');
		IO\Directory::deleteDirectory(Application::getDocumentRoot() . '/local/components/kontur/checkprice.pricetag');
        IO\Directory::deleteDirectory(Application::getDocumentRoot() . '/checkprice-pricetag');
	}
	
	public function registerAgents()
	{
		\CAgent::AddAgent(
			'\Kontur\CheckPrice\Agent\CreateSnap::run();',
			'kontur.checkprice',
			'Y',
			86400,
			date('d.m.Y 05:00:00'),
			'Y',
			date('d.m.Y 05:00:00'),
			7100
		);

		\CAgent::AddAgent(
			'\Kontur\CheckPrice\Agent\CreateSnapNext::run();',
			'kontur.checkprice',
			'N',
			900,
			date('d.m.Y 05:05:00'),
			'Y',
			date('d.m.Y 05:05:00'),
			7200
		);

		\CAgent::AddAgent(
			'\Kontur\CheckPrice\Agent\DeleteOldSnaps::run();',
			'kontur.checkprice',
			'Y',
			86400,
			date('d.m.Y 06:00:00'),
			'Y',
			date('d.m.Y 06:00:00'),
			7200
		);
	}

	public function unregisterAgents()
	{
		\CAgent::RemoveModuleAgents('kontur.checkprice');
	}

	public function createEvents()
	{
		$eventType=new \CEventType;
		$eventTypeFields=[
			'EVENT_NAME'=>'KONTUR_CHECKPRICE_PRICELIST',
			'NAME'=>Loc::getMessage('KONTUR_CHECK_PRICE_EVENT_TYPE_PRICELIST_NAME'),
			'LID'=>'ru',
			'DESCRIPTION'=>Loc::getMessage('KONTUR_CHECK_PRICE_EVENT_TYPE_PRICELIST_DESCRIPTION')
		];
		;

		if($eventTypeId=$eventType->Add($eventTypeFields)) {
			$eventTemplate=new \CEventMessage;
			$eventTemplate->Add([
				'ACTIVE'=>'Y',
				'EVENT_NAME'=>'KONTUR_CHECKPRICE_PRICELIST',
				'LID'=>['s1'],
				'EMAIL_FROM'=>'#DEFAULT_EMAIL_FROM#',
				'EMAIL_TO'=>'#EMAIL_TO#',
				'BCC'=>'#BCC#',
				'SUBJECT'=>Loc::getMessage('KONTUR_CHECK_PRICE_EVENT_TEMPLATE_PRICELIST_SUBJECT'),
				'BODY_TYPE'=>'html',
				'MESSAGE'=>Loc::getMessage('KONTUR_CHECK_PRICE_EVENT_TEMPLATE_PRICELIST_MESSAGE')
			]);
		}
	}

	public function removeEvents()
	{
		$rs=\CEventMessage::GetList($by='id', $order='asc', ['EVENT_NAME'=>'KONTUR_CHECKPRICE_PRICELIST']);
		while($template=$rs->Fetch()) {
			$emessage=new \CEventMessage;
			$emessage->Delete(intval($template['ID']));
		}

		$et=new \CEventType;
		$et->Delete('KONTUR_CHECKPRICE_PRICELIST');
	}
}
?>