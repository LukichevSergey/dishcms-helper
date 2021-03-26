<?php

/**
 * This is the model class for table "question".
 *
 * The followings are the available columns in table 'question':
 * @property integer $id
 * @property string $username
 * @property string $question
 * @property string $answer
 * @property string $published
 * @property string $created
 */
class Question extends CActiveRecord
{
	public $privacy_policy;
	public $verify_code;

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Question the static model class
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
		return 'question';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('username, question', 'required'),
			array('username', 'length', 'max'=>255),
            array('answer, published', 'safe'),
            array('created', 'unsafe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, username, question, answer, created', 'safe', 'on'=>'search'),
			['privacy_policy', 'required', 'except'=>'update_qa'],
			['verify_code', 'captcha', 'except'=>'update_qa'],
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
			'username' => 'Ваше имя',
			'question' => 'Вопрос',
			'answer' => 'Ответ',
			'created' => 'Создан',
			'published' => 'Опубликовано',
			'privacy_policy'=>'Подтверждаю свое согласие с<br/>' . \CHtml::link('Политикой обработки данных', '/privacy-policy', ['target'=>'_blank']),
		);
	}
	
	public static function getCount() {
		return Question::model()->unanswered()->unpublished()->count();
	}
	/**
	 * (non-PHPdoc)
	 * @see CActiveRecord::scopes()
	 */
	public function scopes() {
		return array(
			'published' => array(
				'condition' => 'published=1',
			),
			'unpublished' => array(
				'condition' => 'published<>1',
			),
			'unanswered' => array(
				'condition' => "answer = ''",
			),
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
		$criteria->compare('username',$this->username,true);
		$criteria->compare('question',$this->question,true);
		$criteria->compare('answer',$this->answer,true);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('published',$this->published);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * (non-PHPdoc)
	 * @see CActiveRecord::beforeSave()
	 */
    protected function beforeSave()
    {
    	parent::beforeSave();
    	
        if ($this->isNewRecord) {
            $this->created = new CDbExpression('NOW()');
            $this->published = 0;
	        $this->username = \CHtml::encode($this->username);
	        $this->question = \CHtml::encode($this->question);
        }
        return true;
    }
}
