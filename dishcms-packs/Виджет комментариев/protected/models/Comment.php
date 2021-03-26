<?php

/**
 * This is the model class for table "comment".
 *
 * The followings are the available columns in table 'comment':
 * @property integer $id
 * @property string $title
 * @property string $description
 * @property string $date
 * @property integer $moderated
 */
class Comment extends CActiveRecord
{
	public $model_name = 'Page';
	public $model_id;

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'comment';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('title, description', 'required'),
			array('moderated', 'numerical', 'integerOnly'=>true),
			array('title', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('title, description, date, moderated', 'safe'),
			array('id, title, description, date, moderated', 'safe', 'on'=>'search'),
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
			'comment_model' => array(self::HAS_ONE, 'CommentModel', 'comment_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'title' => 'Имя',
			'description' => 'Комментарий',
			'date' => 'Дата',
			'moderated' => 'Опубликовать',
		);
	}

	protected function afterSave() {
        parent::afterSave();
        
        if($this->model_id && $this->model_name) {
        	$commentModel = new CommentModel;
        	$commentModel->model_id = $this->model_id;
        	$commentModel->model_name = $this->model_name;
        	$commentModel->comment_id = $this->id;
        	$commentModel->save();
        }
    }

	protected function afterDelete()
	{
		parent::afterDelete();

		$this->comment_model->delete();
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
		$criteria->compare('title',$this->title,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('date',$this->date,true);
		$criteria->compare('moderated',$this->moderated);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'sort'=>array(
			    'defaultOrder' => 'id DESC',
			),
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Comment the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
