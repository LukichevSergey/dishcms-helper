<?php
/**
 * Модель Слайд
 */
namespace extend\modules\slider\models;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;

class Slide extends \common\components\base\ActiveRecord
{
	/**
	 * (non-PHPdoc)
	 * @see \CActiveRecord::tableName()
	 */
	public function tableName()
	{
		return 'slider_slides';
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \CModel::behaviors()
	 */
	public function behaviors()
	{
		$t=Y::ct('\extend\modules\slider\SliderModule.models/slide', 'extend.slider');
		return A::m(parent::behaviors(), [
			'sortBehavior'=>'\common\ext\sort\behaviors\SortBehavior',
			'updateTimeBehavior'=>[
				'class'=>'\common\ext\updateTime\behaviors\UpdateTimeBehavior',
				'attributeLabel'=>$t('label.update_time'),
				'addColumn'=>false
			],
			'activeBehavior'=>[
				'class'=>'\common\ext\active\behaviors\ActiveBehavior',
				'attributeLabel'=>$t('label.active')
			],
			'imageBehavior'=>[
				'class'=>'\common\ext\file\behaviors\FileBehavior',
				'attribute'=>'image',
				'attributeLabel'=>$t('label.image'),
				'attributeAlt'=>'image_alt',
				'attributeAltLabel'=>$t('label.image_alt'),
				'imageMode'=>true
			],
			'optionsBehavior'=>[
				'class'=>'\common\ext\dataAttribute\behaviors\DataAttributeBehavior',
				'attribute'=>'options',
				'attributeLabel'=>$t('label.options')
			],
		]);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \CActiveRecord::scopes()
	 */
	public function scopes()
	{
		return $this->getScopes([
				
		]);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \CActiveRecord::relations()
	 */
	public function relations()
	{
		return $this->getRelations([
				
		]);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \CModel::rules()
	 */
	public function rules()
	{
		return $this->getRules([
			['slider_id', 'required'],
			['slider_id', 'numerical', 'integerOnly'=>true],
			['title, url', 'length', 'max'=>255],
			['description', 'safe']
		]);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \CModel::attributeLabels()
	 */
	public function attributeLabels()
	{
		$t=Y::ct('\extend\modules\slider\SliderModule.models/slide', 'extend.slider');
		return $this->getAttributeLabels([
			'slider_id'=>$t('label.slider_id'),
			'title'=>$t('label.title'),
			'url'=>$t('label.url'),
			'description'=>$t('label.description'),
		]);
	}

	/**
	 * Получить значение дополнительного параметра слайда
	 * @param string $name наименование дополнительного параметра
	 * @return string
	 */
	public function getOption($name)
	{
		return $this->optionsBehavior->find('code', $name, ['v'=>'value']);
	}
}
