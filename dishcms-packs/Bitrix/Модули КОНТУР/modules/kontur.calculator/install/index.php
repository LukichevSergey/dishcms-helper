<?php
use Bitrix\Main\Application;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\IO;
use Bitrix\Main\Localization\Loc;

require_once dirname(__FILE__) . '/../lib/helper.php';

Loc::loadMessages(__FILE__);

class kontur_calculator extends \CModule
{
	public $MODULE_ID = 'kontur.calculator';
	
	public function __construct()
	{
		@include __DIR__ . '/version.php';
		
		$this->MODULE_VERSION = $arModuleVersion['VERSION'];
		$this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
		
		$this->PARTNER_NAME = Loc::getMessage('KONTUR_CALCULATOR_PARTNER_NAME');
		// $this->PARTNER_URI = "http://kontur-lite.ru";
		
		$this->MODULE_NAME = Loc::getMessage('KONTUR_CALCULATOR_MODULE_NAME');
		$this->MODULE_DESCRIPTION = Loc::getMessage('KONTUR_CALCULATOR_MODULE_DESCRIPTION');
	}
	
	public function DoInstall()
	{
		global $DB, $APPLICATION;
		
		if (!IsModuleInstalled($this->MODULE_ID)) {
			$this->installFiles();
			$this->installData();
			$this->createEvents();

			ModuleManager::registerModule($this->MODULE_ID);
		
			$APPLICATION->IncludeAdminFile(Loc::getMessage('KONTUR_CALCULATOR_INSTALL_TITLE'), __DIR__ . '/step.php');
		}
	}

	public function DoUninstall()
	{
		global $DB, $APPLICATION;
		
		$this->uninstallFiles();
		$this->uninstallData();
		$this->removeEvents();

		ModuleManager::unRegisterModule($this->MODULE_ID);
		
		$APPLICATION->IncludeAdminFile(Loc::getMessage('KONTUR_CALCULATOR_INSTALL_TITLE'), __DIR__ . '/unstep.php');
	}

	public function installFiles()
    {
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
	
	public function uninstallFiles()
    {
		IO\Directory::deleteDirectory(Application::getDocumentRoot() . '/local/components/kontur/calculator');
        IO\Directory::deleteDirectory(Application::getDocumentRoot() . '/calculator');
	}
	
	public function installData()
	{
		include dirname(__FILE__) . '/services/.services.php';
		if(isset($arServices['iblock']['STAGES'])) {
			foreach($arServices['iblock']['STAGES'] as $stage) {
				include dirname(__FILE__) . '/services/iblock/' . $stage;
			}
		}
	}

	public function uninstallData()
	{

	}
	
	public function createEvents()
	{
		$eventType=new \CEventType;
		$eventTypeFields=[
			'EVENT_NAME'=>'KONTUR_CALCULATOR_FORM',
			'NAME'=>Loc::getMessage('KONTUR_CALCULATOR_EVENT_TYPE_CALCULATOR_FORM_NAME'),
			'LID'=>'ru',
			'DESCRIPTION'=>Loc::getMessage('KONTUR_CALCULATOR_EVENT_TYPE_CALCULATOR_FORM_DESCRIPTION')
		];
		;

		if($eventTypeId=$eventType->Add($eventTypeFields)) {
			$eventTemplate=new \CEventMessage;
			$eventTemplate->Add([
				'ACTIVE'=>'Y',
				'EVENT_NAME'=>'KONTUR_CALCULATOR_FORM',
				'LID'=>['s1'],
				'EMAIL_FROM'=>'#DEFAULT_EMAIL_FROM#',
				'EMAIL_TO'=>'#DEFAULT_EMAIL_FROM#',
				'BCC'=>'#BCC#',
				'SUBJECT'=>Loc::getMessage('KONTUR_CALCULATOR_EVENT_TEMPLATE_CALCULATOR_FORM_SUBJECT'),
				'BODY_TYPE'=>'html',
				'MESSAGE'=>Loc::getMessage('KONTUR_CALCULATOR_EVENT_TEMPLATE_CALCULATOR_FORM_MESSAGE')
			]);
		}
	}

	public function removeEvents()
	{
		$rs=\CEventMessage::GetList($by='id', $order='asc', ['EVENT_NAME'=>'KONTUR_CALCULATOR_FORM']);
		while($template=$rs->Fetch()) {
			$emessage=new \CEventMessage;
			$emessage->Delete(intval($template['ID']));
		}

		$et=new \CEventType;
		$et->Delete('KONTUR_CALCULATOR_FORM');
	}
}
?>