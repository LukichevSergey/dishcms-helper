<?php
/**
 * Модель Слайдер
 */
namespace extend\modules\slider\models;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;

class Slider extends \common\components\base\ActiveRecord
{
	/**
	 * @const ширина слайдера по умолчанию
	 */
	const WIDTH=1140;
	
	/**
	 * @const высота слайдера по умолчанию
	 */
	const HEIGHT=350;
	
	/**
	 * (non-PHPdoc)
	 * @see \CActiveRecord::tableName()
	 */
	public function tableName()
	{
		return 'slider_sliders';
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \CModel::behaviors()
	 */
	public function behaviors()
	{
		$t=Y::ct('\extend\modules\slider\SliderModule.models/slider', 'extend.slider');
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
			'optionsBehavior'=>[
				'class'=>'\common\ext\dataAttribute\behaviors\DataAttributeBehavior',
				'attribute'=>'options',
				'attributeLabel'=>$t('label.options')
			],
			'slidePropertiesBehavior'=>[
				'class'=>'\common\ext\dataAttribute\behaviors\DataAttributeBehavior',
				'attribute'=>'slide_properties',
				'attributeLabel'=>$t('label.slide_properties')
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
			'slides'=>[\CActiveRecord::HAS_MANY, '\extend\modules\slider\models\Slide', 'slider_id', 'scopes'=>'activly'],
			'slidesAll'=>[\CActiveRecord::HAS_MANY, '\extend\modules\slider\models\Slide', 'slider_id']
		]);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \CModel::rules()
	 */
	public function rules()
	{
		return $this->getRules([
			['title, code, description', 'safe']
		]);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \CModel::attributeLabels()
	 */
	public function attributeLabels()
	{
		$t=Y::ct('\extend\modules\slider\SliderModule.models/slider', 'extend.slider');
		return $this->getAttributeLabels([
			'title'=>$t('label.title'),
			'code'=>$t('label.code'),
			'description'=>$t('label.description')
		]);
	}

	/**
     * Получить значение дополнительного параметра слайдера
     * @param string $name наименование дополнительного параметра
     * @return string
     */
    public function getOption($name)
    {
        return $this->optionsBehavior->find('code', $name, ['v'=>'value']);
    }

    /**
     * Значение дополнительного параметра является "yes"
     * @param string $name наименование дополнительного параметра
     * @param boolean $default по умолчанию для данного параметра установлено "yes"
     * @return bool
     */
    public function isOptionYes($name, $default=false)
    {
        $check=['yes', 1];
        
        if($yesIsDefault) {
            $check[]=null;
        }
        
        return in_array($this->getOption($name), $check);
    }
    
    /**
     * Значение дополнительного параметра является "no"
     * @param string $name наименование дополнительного параметра
     * @param boolean $default по умолчанию для данного параметра установлено "no"
     * @return bool
     */
    public function isOptionNo($name, $default=false)
    {
        $check=['yes', 1];
        
        if(!$noIsDefault) {
            $check[]=null;
        }
        
        return !in_array($this->getOption($name), $check);
    }
}
