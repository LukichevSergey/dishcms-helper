<?php
/**
 * Модель формы покупателя.
 *
 * @property string $name
 * @property string $email
 * @property string $phone
 * @property text $address
 * @property text $comment
 * @property integer $payment
 */
namespace DOrder\models;

use \AttributeHelper as A;

class CustomerForm extends \CFormModel
{
	public $name;
	public $email;
	public $phone;
	public $address;
	public $comment;
	public $payment;
	
	/**
	 * @param string $className form model class name.
	 * @return CustomerForm the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, email, phone', 'required'),
			array('address', 'required'),
			array('name', 'length', 'max'=>50),
			array('email, address', 'length', 'max'=>255),
			array('email', 'email'),
			array('phone', 'match', 'pattern'=>'/^\+7 \( \d{3} \) \d{3} - \d{2} - \d{2}$/'),
			array('comment', 'length', 'max'=>1000),
			array('payment', 'numerical', 'integerOnly'=>true, 'on'=>'payment')
		);
	}
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'name' => 'Ваше имя',
			'email' => 'Email',
			'phone' => 'Телефон',
			'address' => 'Адрес доставки',
			'comment' => 'Комментарий к заказу',
			'create_time' => 'Время создания',
			'payment'=>'Способы оплаты'
		);
	}
	
	/**
	 * Получить аттрибуты
	 * @see CModel::getAttributes()
	 * @param mixed $names
	 * @param boolean $returnALV Возвращать результат в виде массива 
	 * array(attribute=>array('label' => label, 'value' => value)), или 
	 * в виде простого array(name=>value)
	 * @param boolean $serialize Сериализовать результат или нет.
	 * @return array|string возвращается строка, если параметр $serialize установлен в true.
	 */
	public function getAttributes($names=null, $returnALV=false, $serialize=false) 
	{
		$attributes = parent::getAttributes($names); 
		
		if($returnALV) {
			$labels = $this->attributeLabels();
			foreach($attributes as $name=>$value) {
				$attributes[$name] = array('label' => A::get($labels, $name, $name), 'value' => $value);
			}	
		}

		return $serialize ? serialize($attributes) : $attributes;
	}
}