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

class Messages extends \CActiveRecord
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
		return 'subscribe_messages';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{

		// will receive user inputs.
		// id message date send_time from from_name
		return array(

			array('send_time', 'length', 'max'=>200),
			array('message, theme','required'),
			array('from, from_name','length', 'max'=>200),
			array('from', 'email'),
				
/*			array('file','length', 'max'=>500),
			array('file', 'default', 'value'=>null),*/


		);
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

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(

			'send_time'=>'Время отправления',
			'message'=>'Сообщение',
			'theme'=>'Тема письма',
			'from'=>'почта отправителя',
			'from_name'=>'имя отправителя',
			'file'=>'Файл',

		);
	}
}
