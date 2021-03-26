<?php
/**
 * Модель заказа 
 * 
 * @property integer $id
 * @property text $customer_data
 * @property text $order_data
 * @property text $comment
 * @property timestamp $create_time
 * @property boolean $completed
 * @property boolean $paid
 */
namespace DOrder\models;

use common\components\helpers\HArray as A;
use common\components\helpers\HHash;

class DOrder extends \common\components\base\ActiveRecord
{	
	/**
	 * (non-PHPdoc)
	 * @see \CActiveRecord::tableName()
	 */
	public function tableName($withPrefix=true)
	{	
		$tableName = \Yii::app()->getModule('DOrder')->tableName;
		return $withPrefix ? ('{{' . $tableName . '}}') : $tableName;
	}
    
	/**
	 * (non-PHPdoc)
	 * @see CActiveRecord::defaultScope()
	 */
	public function defaultScope()
	{
		return array(
			'order' => 'create_time DESC'
		);
	}
	
    public function relations()
    {
        return $this->getRelations([
        ]);
    }
    
	/**
	 * (non-PHPdoc)
	 * @see CActiveRecord::scopes()
	 */
	public function scopes()
	{
		return $this->getScopes(array(
			'uncompleted' => array('condition' => 'completed<>1'),
			'payed'=>['condition'=>'paid=1']
		));
	}
    
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return $this->getRules(array(
			array('create_time', 'required', 'on'=>'insert'),
			array('completed, paid', 'numerical', 'integerOnly'=>true),
			array('customer_data, order_data, comment', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, customer_data, order_data, comment, create_time, completed, paid', 'safe', 'on'=>'search'),
			['paid', 'safe', 'on'=>'updatePaid']
		));
	}
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return $this->getAttributeLabels(array(
			'id' => 'ID',
			'customer_data' => 'Customer Data',
			'order_data' => 'Order Data',
			'comment' => 'Comment',
			'create_time' => 'Create Time',
			'completed' => 'Сompleted',
			'paid' => 'Оплачен',
		));
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \CActiveRecord::init()
	 */
	public function init()
	{
	}
	
	/**
	 * Get attribute "customer_data" value.
	 * @param string $unserialize return unserialize or not.
	 * @return array|string
	 */
	public function getCustomerData($unserialize=true)
	{
		return $unserialize ? unserialize($this->customer_data) : $this->customer_data; 
	}
	
	/**
	 * Get attribute "order_data" value.
	 * @param string $unserialize return unserialize or not.
	 * @return array|string
	 */
	public function getOrderData($unserialize=true)
	{
		return $unserialize ? unserialize($this->order_data) : $this->order_data; 
	}
	
	public function getTotalPrice()
	{
		$total = 0;
		$orderData = $this->getOrderData();
		
		foreach($orderData as $hash=>$data) {
			$total += $data['price']['value'] * $data['count']['value'] ;
		}
		
		return $total;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CModel::beforeValidate()
	 */
	public function beforeValidate()
	{
		if($this->isNewRecord) {
			$this->create_time = new \CDbExpression('NOW()');
		}
		return parent::beforeValidate();
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CActiveRecord::beforeSave()
	 */
	public function beforeSave()
	{
		if($this->isNewRecord) {
			$this->hash=HHash::u('o');
		}
		return parent::beforeSave();
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.
	
		$criteria=new CDbCriteria;
	
		$criteria->compare('id',$this->id);
		$criteria->compare('customer_data',$this->customer_data,true);
		$criteria->compare('order_data',$this->order_data,true);
		$criteria->compare('comment',$this->comment,true);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('completed',$this->completed);
		$criteria->compare('paid',$this->paid);
	
		return new CActiveDataProvider($this, array(
				'criteria'=>$criteria,
		));
	}
	
	/**
	 * Инсталляция
	 * @todo Обработка ошибок, сейчас возвращает всегда true.
	 * @param string $tableName имя таблицы
	 */
	public static function install($tableName)
	{
		self::createTable($tableName);
		return true;
	}
	
	/**
	 * Создание таблицы
	 * @param string $tableName имя таблицы
	 */
	protected static function createTable($tableName)
	{
		$query = 'CREATE TABLE IF NOT EXISTS `' . $tableName . '` (
			`id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT \'Id\',
			`customer_data` TEXT COMMENT \'Customer data\',
			`order_data` LONGTEXT COMMENT \'Order data\',
			`comment` TEXT COMMENT \'Comment\',
			`create_time` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT \'Create time\',
			`completed` TINYINT(1) DEFAULT 0 COMMENT \'Is completed\',
			`paid` TINYINT(1) DEFAULT 0 COMMENT \'Is paid\',
			`hash` VARCHAR(255)
		)';
		
		return (bool)\Yii::app()->db->createCommand($query)->execute();
	}
}
