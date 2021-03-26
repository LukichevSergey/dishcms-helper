<?php
/**
 * Модуль интеграции с MailChimp (mailchimp.com)
 */
use common\components\helpers\HYii as Y;
use common\components\base\WebModule;

class MailchimpModule extends WebModule
{
	/**
	 * (non-PHPdoc)
	 * @see CModule::init()
	 */
	public function init()
	{
		parent::init();
		
		// $this->assetsBaseUrl=Y::publish($this->getAssetsBasePath());

		$this->setImport(array(
			'mailchimp.models.*',
			'mailchimp.behaviors.*',
			'mailchimp.components.*',
		));		
	}
}
