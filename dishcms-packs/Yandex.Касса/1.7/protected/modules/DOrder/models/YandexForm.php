<?php
/**
 * Модель формы покупателя для Яндекс.Деньги + Яндекс.Касса.
 *
 * @see https://money.yandex.ru/doc.xml?id=526537
 *
 * @property string $scid
 * @property string $ShopID
 * @property string $CustomerNumber
 * @property string $Sum
 * @property string $CustName
 * @property string $CustAddr
 * @property string $CustEMail
 * @property string $OrderDetails
 * @property string $paymentType
 */
namespace DOrder\models;

use \AttributeHelper as A;

class YandexForm extends BaseForm
{
	/**
	 * Yandex form attributes.
	 * @var string
	 */
	public $scid = '';
	public $ShopID = '';
	public $CustomerNumber = '';
	public $Sum = '';
	public $CustName = '';
	public $CustAddr = '';
	public $CustEMail = '';
	public $OrderDetails= '';
	public $paymentType = '';
	
	public $phone = '';
	
	/**
	 * Уникальный номер заказа в ИС Контрагента. Уникальность контролируется Оператором в сочетании с 
	 * параметром shopId. Если платеж с таким номером заказа уже был успешно проведен, 
	 * то повторные попытки оплаты будут отвергнуты Оператором.
	 * @var string
	 */
	public $orderNumber;
	
	public $shopSuccessURL = '/shop/paymentSuccess';
	public $shopFailURL = '/shop/paymentFailed';
	
	/**
	 * @param string $className form model class name.
	 * @return CustomerForm the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	public function getEmail()
	{
		return $this->CustEMail;
	}
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('CustName, CustAddr, phone', 'required'),
			array('paymentType', 'required', 'message'=>'Необходимо выбрать способ оплаты'),					
			array('paymentType', 'in', 'range'=>array_keys($this->paymentTypes)),
			array('CustName, phone', 'length', 'max'=>50),
			array('CustEMail, CustAddr', 'length', 'max'=>255),
			array('CustEMail', 'email'),
			array('phone', 'match', 'pattern'=>'/^\+7 \( \d{3} \) \d{3} - \d{2} - \d{2}$/'),
			array('OrderDetails', 'length', 'max'=>1000),
			array('Sum', 'numerical'),
			array('scid, ShopID', 'safe')
		);
	}
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'CustName' => 'Ваше имя',
			'CustEMail' => 'Email',
			'phone' => 'Телефон',
			'CustAddr' => 'Адрес доставки',
			'OrderDetails' => 'Комментарий к заказу',
			'paymentType'=>'Способы оплаты',
			'create_time' => 'Время создания'
		);
	}
	
	/**
	 * Get payment types 
	 * @return array (type=>title)
	 */
	public function getPaymentTypes()
	{
		return array(
			'PC'=>'Со счета в Яндекс.Деньгах',
			'AC'=>'С банковской карты',
			// 'WM' => 'Оплата cо счета WebMoney',
			// 'GP' => 'Оплата по коду через терминал',
			// 'AB' => 'Оплата через Альфа-Клик',
			// 'PB' => 'Оплата через Промсвязьбанк',
			// 'MA' => 'Оплата через MasterPass',
			'Handsup'=>'Оплата наличными'
		);
	}
	
	/**
	 * Get payment type label
	 * @param string $type payment type.
	 * @return string
	 */
	public function getPaymentTypeLabel($type)
	{
		return A::get($this->getPaymentTypes(), $type, '');
	}
}