<?php
/**
 * 
 * 
 */
namespace amocrm\models;

use common\components\helpers\HArray as A;

class AmoCrmSettings extends \settings\components\base\SettingsModel
{
    public $account;
    public $redirect_uri;
    public $client_id;
    public $client_secret;
    public $auth_code;
    
    public $phone_field_id; 
    public $email_field_id; 
    
	/**
	 * (non-PHPdoc)
	 * @see \common\components\base\FormModel::behaviors()
	 */
	public function behaviors()
	{
		return A::m(parent::behaviors(), [
		]);
	}

	/**
	 * (non-PHPdoc)
	 * @see \settings\components\base\SettingsModel::rules()
	 */
	public function rules()
	{
		return $this->getRules([
		    ['account, client_id, client_secret, auth_code, redirect_uri', 'safe'],
		    ['phone_field_id, email_field_id', 'numerical', 'integerOnly'=>true]
		]);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \settings\components\base\SettingsModel::attributeLabels()
	 */
	public function attributeLabels()
	{
		return $this->getAttributeLabels([
		    'account'=>'Аккаунт',
		    'redirect_uri'=>'URL возврата',
		    'client_id'=>'Идентификатор интеграции',
		    'client_secret'=>'Секретный ключ',
		    'auth_code'=>'Код авторизации',
		    'phone_field_id'=>'Контактный телефон',
		    'email_field_id'=>'Контактный E-Mail',
		]);
	}
}