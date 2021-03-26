<?php
/**
 * Модель Баннер
 */
use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;

class Banner extends \common\components\base\ActiveRecord
{
	/**
	 * (non-PHPdoc)
	 * @see \CActiveRecord::tableName()
	 */
	public function tableName()
	{
		return 'banners';
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CModel::behaviors()
	 */
	public function behaviors()
	{
		return A::m(parent::behaviors(), [
			'sortBehavior'=>'\common\ext\sort\behaviors\SortBehavior',
			'updateTimeBehavior'=>[
				'class'=>'\common\ext\updateTime\behaviors\UpdateTimeBehavior',
				'addColumn'=>false
			],
			'activeBehavior'=>[
				'class'=>'\common\ext\active\behaviors\ActiveBehavior',
			],
			'imageBehavior'=>[
				'class'=>'\common\ext\file\behaviors\FileBehavior',
				'attribute'=>'image',
				'attributeLabel'=>'Изображение',
				'imageMode'=>true
			]
		]);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CActiveRecord::scopes()
	 */
	public function scopes()
	{
		return $this->getScopes([
				
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
			['title, link', 'length', 'max'=>255]
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
			'link'=>'Ссылка'
		]);
	}
}
