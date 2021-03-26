<?php

/**
 * This is the model class for table "blog".
 *
 * The followings are the available columns in table 'blog':
 * @property integer $id
 * @property string $alias
 * @property string $title
 * @property integer $ordering
 */
class Blog extends CActiveRecord
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
	 * @param string $className active record class name.
	 * @return Blog the static model class
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
		return 'blog';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('alias, title', 'required'),
			array('ordering', 'numerical', 'integerOnly'=>true),
			array('alias, title', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, alias, title, ordering', 'safe', 'on'=>'search'),
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
            'posts'=>array(self::HAS_MANY, 'Page', 'blog_id')
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'alias' => 'Url',
			'title' => 'Название',
			'ordering' => 'Порядок',
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
		$criteria->compare('alias',$this->alias,true);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('ordering',$this->ordering);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    protected function beforeValidate()
    {
        $this->alias = trim($this->alias);
        return true;
    }

    protected function afterSave()
    {
    	$this->activeMenuBehavior->afterSave();

        // Update site menu
        /*if ($this->isNewRecord)
            CmsMenu::getInstance()->addItem($this);
        else
            CmsMenu::getInstance()->updateItem($this);*/

        return true;
    }

    protected function afterDelete()
    {
    	$this->activeMenuBehavior->afterDelete();

        // CmsMenu::getInstance()->removeItem($this);
        return true;
    }
}
