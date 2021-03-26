<?php
/**
 * 
 * 
 */
namespace ykassa\models;

use common\components\helpers\HArray as A;

class YKassaSettings extends \settings\components\base\SettingsModel
{
	public $mode;
    public $tax;
    public $tax_system;
    public $payment_type=['AC'];
    public $shop_id;
    public $scid;
    public $shop_password;
    public $test_shop_id;
    public $test_scid;
    public $test_shop_password;
	// public $enable_test_mode=1;
    public $text_payment_form;
    public $text_success='Ваш платеж принят.';
    public $text_fail='При проведении платежа произошла ошибка.';
    public $title_payment_form='Оплата заказа';
    public $title_success='Заказ оплачен';
    public $title_fail='Ошибка оплаты';
    public $custom_product_title='Платеж';
    public $order_form_payment_type='Онлайн-оплата';
	public $is_custom_form=0;
	
	// API
	public $api_shop_id;
	public $api_secret_key;
	public $api_test_shop_id;
	public $api_test_secret_key;
    
    public $payment_subject_type='payment';
    public $payment_method_type='full_payment';
    
    public $enable_debug_mode=0;
    public $template='ykassa.views.httpPayment.index';
    public $template_custom='ykassa.views.httpPaymentCustom.index';
    
    
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
			['mode', 'required', 'message'=>'Необходимо выбрать режим подключения (вкладка "Подключение")'],
		    ['tax, tax_system', 'numerical', 'integerOnly'=>true],
		    ['payment_type', 'safe'],
		    ['enable_debug_mode, is_custom_form', 'boolean'],
		    // ['enable_test_mode', 'boolean'],
		    ['title_payment_form, title_success, title_fail', 'safe'],
		    ['text_payment_form, text_success, text_fail', 'safe'],
		    ['shop_id, scid, shop_password, ', 'safe'],
		    ['test_shop_id, test_scid, test_shop_password, ', 'safe'],
		    ['template, template_custom', 'safe'],
		    ['custom_product_title, order_form_payment_type', 'safe'],
			['payment_subject_type, payment_method_type', 'safe'],
			['api_shop_id, api_secret_key, api_test_shop_id, api_test_secret_key', 'safe']
		]);
	}
	
	public function tableName()
	{
	    return 'ykassa_settings';
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \settings\components\base\SettingsModel::attributeLabels()
	 */
	public function attributeLabels()
	{
		return $this->getAttributeLabels([
			'mode'=>'Режим подключения',
		    'payment_type'=>'Способ оплаты',
		    'tax'=>'Ставка НДС',
		    'tax_system'=>'Система налогообложения магазина (СНО)',
		    // 'enable_test_mode'=>'Тестовый режим',
		    'shop_id'=>'Идентификатор магазина',
		    'scid'=>'scid',
		    'shop_password'=>'ShopPassword',
		    'test_shop_id'=>'Идентификатор магазина (тестовый)',
		    'test_scid'=>'scid (тестовый)',
		    'test_shop_password'=>'ShopPassword (тестовый)',
		    'text_payment_form'=>'Текст на странице оплаты',
		    'text_success'=>'Текст на странице успешной оплаты',
		    'text_fail'=>'Текст на странице неуспешной оплаты',
		    'title_payment_form'=>'Заголовок страницы оплаты',
		    'title_success'=>'Заголовок страницы успешной оплаты',
		    'title_fail'=>'Заголовок страницы неуспешной оплаты',
		    'enable_debug_mode'=>'Включить режим отладки',
		    'custom_product_title'=>'Наименование товара для формы произвольной оплаты',
		    'is_custom_form'=>'Включить режим настройки для произвольной формы оплаты',
		    'order_form_payment_type'=>'Значение онлайн-оплаты для поля "Способ оплаты"',
		    'payment_subject_type'=>'Признак предмета расчета — категория товара для налоговой.',
			'payment_method_type'=>'Признак способа расчета  — категория способа товара оплаты для налоговой.',
			'api_shop_id'=>'Идентификатор магазина',
			'api_secret_key'=>'Секретный ключ',
			'api_test_shop_id'=>'Идентификатор магазина (тестовый)',
			'api_test_secret_key'=>'Секретный ключ (тестовый)',
		]);
	}
}
