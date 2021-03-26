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
    public $payment_subject_type='payment';
	public $payment_method_type='full_payment';
	
	// API
	public $api_default_config='ecommerce_order';
	public $api_shop_id;
	public $api_secret_key;
	public $api_test_shop_id;
	public $api_test_secret_key;

	// параметры для страниц
	public $page_payment_skip=true;
    public $page_payment_title='Оплата заказа';
	public $page_payment_text='Сейчас вы будете перенаправлены на страницу оплаты. Если этого не произошло нажимте на кнопку "Оплатить"';	
	public $page_success_title='Заказ оплачен';
	public $page_success_text='Ваш платеж принят.';
	public $page_fail_title='Ошибка оплаты';
	public $page_fail_text='При проведении платежа произошла ошибка.';
    
    // дополнительные параметры
	public $btn_pay_label='Оплатить';    
	public $btn_pay_styles='
	cursor: pointer;
	background: #FFCC33;
	border: 0;
	outline: 0;
	padding: 10px 20px !important;
	';
	
	// дополнительные системные параметры
	public $enable_debug_mode=0;
	public $online_payment_types='';
    
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
			['payment_subject_type, payment_method_type', 'safe'],
			['api_shop_id, api_secret_key, api_test_shop_id, api_test_secret_key', 'safe'],
			['api_default_config', 'safe'],
			['page_payment_skip', 'boolean'],
			['page_payment_title, page_payment_text', 'safe'],
			['page_success_title, page_success_text', 'safe'],
			['page_fail_title, page_fail_text', 'safe'],
			['btn_pay_label, btn_pay_styles', 'safe'],
			['enable_debug_mode', 'boolean'],
			['online_payment_types', 'safe'],
			
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
		    'payment_subject_type'=>'Признак предмета расчета — категория товара для налоговой.',
			'payment_method_type'=>'Признак способа расчета  — категория способа товара оплаты для налоговой.',
		    'page_payment_skip'=>'Сразу перенаправлять на оплату, пропуская данную страницу',
		    'page_payment_title'=>'Заголовок страницы оплаты',
		    'page_payment_text'=>'Текст на странице оплаты',
		    'page_success_title'=>'Заголовок страницы успешной оплаты',
		    'page_success_text'=>'Текст на странице успешной оплаты',
		    'page_fail_title'=>'Заголовок страницы неуспешной оплаты',
		    'page_fail_text'=>'Текст на странице неуспешной оплаты',
			'api_shop_id'=>'Идентификатор магазина',
			'api_secret_key'=>'Секретный ключ',
			'api_test_shop_id'=>'Идентификатор магазина (тестовый)',
			'api_test_secret_key'=>'Секретный ключ (тестовый)',
			'api_default_config'=>'Конфигурация оплаты по умолчанию',
			'btn_pay_label'=>'Подпись кнопки перехода на страницу оплаты',
			'btn_pay_styles'=>'CSS стили кнопки перехода на страницу оплаты',
			'enable_debug_mode'=>'Включить режим отладки',
			'online_payment_types'=>'Типы оплаты, которые считать ОНЛАЙН для метода проверки HYKassa::isOnlinePaymentType()'
		]);
	}
}
