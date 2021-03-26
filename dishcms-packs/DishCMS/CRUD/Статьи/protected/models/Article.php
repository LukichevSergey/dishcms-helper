<?php
/**
 * Модель Статьи
 */
use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HHtml;

class Article extends \common\components\base\ActiveRecord
{
	/**
	 * (non-PHPdoc)
	 * @see \CActiveRecord::tableName()
	 */
	public function tableName()
	{
		return 'articles';
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CModel::behaviors()
	 */
	public function behaviors()
	{
		return A::m(parent::behaviors(), [
            'aliasBehavior'=>'\DAliasBehavior',
            'activeBehavior'=>'\common\ext\active\behaviors\ActiveBehavior',
            'metaBehavior'=>'\MetadataBehavior',
            'previewImageBehavior'=>[
                'class'=>'\common\ext\file\behaviors\FileBehavior',
                'attribute'=>'preview',
                'attributeLabel'=>'Изображение для анонса',
                'attributeEnable'=>'enable_preview',
                'attributeEnableLabel'=>'Отображать на сайте',
                'attributeAltEmpty'=>'title',
                'enableValue'=>true,
                'defaultSrc'=>HHtml::phSrc(['w'=>240,'h'=>240,'t'=>'']),
                'imageMode'=>true
            ],
			'updateTimeBehavior'=>[
                'class'=>'\common\ext\updateTime\behaviors\UpdateTimeBehavior',
                'addColumn'=>false
            ],
		]);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CActiveRecord::scopes()
	 */
	public function scopes()
	{
		return $this->getScopes([
			'previewColumns'=>array('select'=>'id, title, IF(enable_preview=1, preview, NULL) as preview, preview_text, create_time'),	
		]);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CActiveRecord::relations()
	 */
	public function relations()
	{
		return $this->getRelations([
				
		]);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CModel::rules()
	 */
	public function rules()
	{
		return $this->getRules([
            ['title', 'required'],
            ['title', 'length', 'max'=>255],
            ['id, title, preview_text, text, create_time', 'safe'],
		]);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CModel::attributeLabels()
	 */
	public function attributeLabels()
	{
		return $this->getAttributeLabels([
            'title'=>'Наименование',
            'preview_text' => 'Анонс',
            'text' => 'Текст',
            'create_time' => 'Дата создания'
		]);
	}
	
	/**
     * Получить дату создания
     * @return string отформатированная дата создания
     */
    protected function getDate()
    {
        return Yii::app()->params['month']
            ? Y::formatDateVsRusMonth($this->create_time)
            : Y::formatDate($this->create_time, 'dd.MM.yyyy');
    }
}
