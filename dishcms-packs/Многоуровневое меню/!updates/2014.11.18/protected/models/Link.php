<?php

/**
 * This is the model class for table "link".
 *
 * The followings are the available columns in table 'link':
 * @property integer $id
 * @property string $title
 * @property string $url
 */
class Link extends CActiveRecord
{
	/**
	 * (non-PHPdoc)
	 * @see CModel::behaviors()
	 */
	public function behaviors()
	{
		return array(
			'activeMenuBehavior' => array(
				'class' => '\menu\components\behaviors\ActiveMenuBehavior',
			)
		);
	}
	
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
		return 'link';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('title, url', 'required'),
			array('title, url', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, title, url', 'safe', 'on'=>'search'),
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
			'id' => 'ID',
			'title' => 'Заголовок',
			'url' => 'Ссылка',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('url',$this->url,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    protected function afterSave()
    {
    	$this->activeMenuBehavior->afterSave();
    	
        // Update site menu
        /*if ($this->isNewRecord)
            CmsMenu::getInstance()->addItem($this);
        else
            CmsMenu::getInstance()->updateItem($this);
        */
        return true;
    }

    protected function afterDelete()
    {
    	$this->activeMenuBehavior->afterDelete();

        // CmsMenu::getInstance()->removeItem($this);
        return true;
    }
}
