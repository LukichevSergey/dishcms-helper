<?php
/**
 * This is the base model class for "feedback" tables.
 *
 * Use \AdvancedActiveRecord
 *
 * The followings are the available columns in 'feedback' tables:
 * @property integer $id
 * @property string $created
 * @property integer $completed
 */
namespace feedback\models;

class FeedbackModel extends \AdvancedActiveRecord
{
	/**
	 * States for "complited" attribute.
	 * @var int
	 */
	const COMPLETED = 1;
	const UNCOMPLETED = 0;
	
	/**
	 * Attribute "completed"
	 * @var integer
	 */
	public $completed;
	
	/**
	 * Attribute "created"
	 * @var datetime|string
	 */
	public $created;
	
	/**
	 * Verify code for Captcha 
	 * @var string
	 */
	public $verifyCode;
	
	/**
	 * Using or not CAPTCHA.
	 * @var unknown
	 */
	public $useCaptcha = false;
	
	/**
	 * Table name.
	 * @var string
	 */
	protected $_tableName = 'feedback';
	
	/**
	 * Дополнительные правила валидации
	 * @var array
	 */
	protected $_rules = array();
	
	/**
	 * Дополнительные метки атрибутов
	 * @var array
	 */
	protected $_attributeLabels = array();
	
	/**
	 * (non-PHPdoc)
	 * @see CActiveRecord::__set()
	 */
	public function __set($name, $value)
	{
		if(!property_exists(__CLASS__, $name)) {
			$this->$name = $value;
		}
		else parent::__set($name, $value);
	}
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName() 
	{
		return $this->_tableName;
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		$rules = array(
			array('completed', 'numerical', 'integerOnly'=>true),
			array('created', 'safe', 'on'=>'insert, active'),
			array('completed', 'safe', 'on'=>'insert, active, update_completed'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, created, completed', 'safe', 'on'=>'search'),
		);
		if($this->useCaptcha) {
			$rules[] = array('verifyCode', 'captcha', 'on'=>'insert');
			$rules[] = array('verifyCode', 'safe', 'on'=>'active, insert');
		}
		
		return \CMap::mergeArray($rules, $this->_rules);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}
	
	public function scopes()
	{
		return array(
			'completed' => array('condition' => 'completed = ' . self::COMPLETED),
			'uncompleted' => array('condition' => 'completed = ' . self::UNCOMPLETED),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return \CMap::mergeArray(array(
			'id' => 'ID',
			'created' => 'Дата создания',
			'completed' => 'Обработан',
			'verifyCode' => 'Код проверки',				
		), $this->_attributeLabels);
	}

	/**
	 * Get table name
	 */
	public function getTableName() 
	{
		return $this->_tableName;
	}
	
	/**
	 * Set table name.
	 * @param string $tableName table name.
	 * @throws FeedbackModelException
	 */
	public function setTableName($tableName)
	{
		if(!preg_match('/^[a-z][a-z_0-9]+$/i', $tableName))
			throw new FeedbackModelException("Invalid feedback \"{$tableName}\" table name.");
	
		$this->_tableName = $tableName;
	}
	
	/**
	 * Добавление правил валидации
	 * 
	 * @param array $rules validation rule
	 */
	public function addRules($rules)
	{
		if(!is_array($rules)) return false;
		
		$this->_rules = \CMap::mergeArray($this->_rules, $rules);
	}
	
	/**
	 * Добавление метки атрибута
	 * @param string $name attribute name.
	 * @param string $label attribute label.
	 */
	public function addAttributeLabel($name, $label)
	{
		$this->_attributeLabels[$name] = $label;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CModel::beforeValidate()
	 */
	public function beforeValidate() 
	{
		if(in_array($this->scenario, array('insert', 'active'))) {
			$this->created = new \CDbExpression('NOW()');
			$this->complited = self::UNCOMPLETED;
		}	
		return true;	
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CActiveRecord::beforeSave()
	 */
	public function beforeSave()
	{
		$this->refreshMetaData();
		return true;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CActiveRecord::beforeFind()
	 */
	public function beforeFind()
	{
		$this->refreshMetaData();
		return true;
	}
	
	/**
	 * Update completed state by primary key
	 * @see \CDbCommand::update()
	 * @param int $pk primary key value
	 * @param boolean $completed TRUE(completed)/FALSE(uncompleted)
	 * @return number integer number of rows affected by the execution.
	 */
	public function updateCompletedByPk($pk, $completed=false)
	{
		$columns = array('completed' => ($completed ? self::COMPLETED : self::UNCOMPLETED));
		
		return $this->getDbConnection()->createCommand()
			->update($this->tableName(), $columns, 'id=:id', array(':id'=>$pk));
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

		$criteria=new \CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('completed',$this->completed);

		return new \CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Feedback the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}

/**
 * Feedback model exception class.
 * @see \Exception
 */

class FeedbackModelException extends \Exception
{
}