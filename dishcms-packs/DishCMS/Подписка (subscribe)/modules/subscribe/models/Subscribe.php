<?php

/**
 * This is the model class for table "link".
 *
 * The followings are the available columns in table 'link':
 * @property integer $id
 * @property string $title
 * @property string $url
 */

namespace subscribe\models;

class Subscribe extends \CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Link the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'subscribe';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{	
		
		
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('email', 'required'),
			#array('email','unique', 'caseSensitive'=>false, 'message'=>'Вы уже подписаны.'),
			#array('phone','unique', 'caseSensitive'=>false, 'message'=>'Номер уже подписан.'), 

			array('email','email'),
			
			array('email, hash', 'length', 'max'=>500),
			array('active', 'boolean'),

			array('email, phone', 'length', 'max'=>200),
			#array('email, phone', 'CheckSubscribeRules')
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			//array('id, title, url', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function CheckSubscribeRules()
	{	
		if ((strlen($this->phone) == 0) && ((strlen($this->email) == 0))) $this->addError('email', 'error email.');
	}
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'email' => 'Почта',
			'phone' => 'Телефон',
			'hash' => 'ХЭШ',
			//'url' => 'Ссылка',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function FindEmail()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		
		$criteria->addNotInCondition('email', array(''));
	#	$criteria->compare('email',$this->phone,true);
	#	$criteria->compare('phone',$this->phone,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public function FindPhone()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->addNotInCondition('phone', array(''));
		
	#	$criteria->compare('email',$this->phone,true);
	#	$criteria->compare('phone',$this->phone,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

/*    protected function afterSave()
    {
        // Update site menu
        if ($this->isNewRecord)
            CmsMenu::getInstance()->addItem($this);
        else
            CmsMenu::getInstance()->updateItem($this);

        return true;
    }

    protected function afterDelete()
    {
        CmsMenu::getInstance()->removeItem($this);
        return true;
    }*/
}
