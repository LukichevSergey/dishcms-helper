<?php
/**
 * Поведение настроек модуля MailChimp
 * 
 */
namespace mailchimp\behaviors;

class ShopSettingsBehavior extends \CBehavior
{
    public $mailchimp_key;
    public $mailchimp_default_list_id;
	
	/**
	 * (non-PHPdoc)
	 * @see \settings\components\base\SettingsModel::rules()
	 */
	public function rules()
	{
		return [
            ['mailchimp_default_list_id, mailchimp_key', 'safe'],
		];
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \settings\components\base\SettingsModel::attributeLabels()
	 */
	public function attributeLabels()
	{
		return [
            'mailchimp_key'=>'API ключ',
			'mailchimp_default_list_id'=>'Идентификатор списка клиентов',
		];
	}
}
