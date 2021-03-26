<?php
/**
 * 
 * 
 */
namespace ecommerce\modules\robokassa\models;

use common\components\helpers\HArray as A;

class RobokassaSettings extends \settings\components\base\SettingsModel
{
	
	public $shp_item;
	
	public $merchant_login;
	public $password1;
	public $password2;
	
	public $enable_test_mode=1;
	public $test_merchant_login;
	public $test_password1;
	public $test_password2;
	
	public $title_payment_form='Оплата заказа';
    public $text_payment_form;	
    public $title_success='Заказ оплачен';
    public $text_success='Ваш платеж принят.';
    public $title_fail='Ошибка оплаты';
	public $text_fail='При проведении платежа произошла ошибка.';
	
    public $enable_debug_mode=0;
        
    public $isNewRecord=false;
    public $id=1;
    
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
			['merchant_login, password1, password2', 'safe'],
		    ['enable_test_mode', 'boolean'],
			['test_merchant_login, test_password1, test_password2', 'safe'],			
		    ['title_payment_form, title_success, title_fail', 'safe'],
		    ['text_payment_form, text_success, text_fail', 'safe'],		    
		    ['enable_debug_mode', 'boolean'],
		]);
	}
	
	public function tableName()
	{
	    return 'robokassa_settings';
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \settings\components\base\SettingsModel::attributeLabels()
	 */
	public function attributeLabels()
	{
		return $this->getAttributeLabels([
			'merchant_login'=>'Идентификатор магазина в ROBOKASSA',
			'password1'=>'Пароль #1',
			'password2'=>'Пароль #2',
			
		    'enable_test_mode'=>'Тестовый режим',
			'test_merchant_login'=>'Идентификатор магазина в ROBOKASSA',
			'test_password1'=>'Пароль #1',
			'test_password2'=>'Пароль #2',

		    'text_payment_form'=>'Текст на странице оплаты',
		    'text_success'=>'Текст на странице успешной оплаты',
		    'text_fail'=>'Текст на странице неуспешной оплаты',
		    'title_payment_form'=>'Заголовок страницы оплаты',
		    'title_success'=>'Заголовок страницы успешной оплаты',
		    'title_fail'=>'Заголовок страницы неуспешной оплаты',
		    'enable_debug_mode'=>'Включить режим отладки',		    
		]);
	}
}
