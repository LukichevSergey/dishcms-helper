<?php
/**
 * Модель
 */
namespace extend\modules\buildings\models;

use common\components\helpers\HArray as A;

class Floor extends \common\components\base\ActiveRecord
{
	/**
	 * (non-PHPdoc)
	 * @see \CActiveRecord::tableName()
	 */
	public function tableName()
	{
		return 'buildings_floors';
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \CModel::behaviors()
	 */
	public function behaviors()
	{
	    return A::m(parent::behaviors(), [
            'publishedBehavior'=>'\common\ext\active\behaviors\PublishedBehavior',
	        'sortBehavior'=>'\common\ext\sort\behaviors\SortBehavior',
	        'imageBehavior'=>[
	            'class'=>'\common\ext\file\behaviors\FileBehavior',
	            'attribute'=>'image',
	            'attributeLabel'=>'Карта квартир',
	            'attributeAlt'=>'image_alt',
	            'attributeAltEmpty'=>'title',
	            'enableValue'=>true,
	            'defaultSrc'=>'/images/shop/product_no_image.png',
	            'imageMode'=>true
	        ],
	        'svgBehavior'=>[
	            'class'=>'\common\ext\file\behaviors\FileBehavior',
	            'attribute'=>'svg',
	            'attributeLabel'=>'SVG для карты квартир',
	            'types'=>'svg'
	        ],	 
            'updateTimeBehavior'=>[
                'class'=>'\common\ext\updateTime\behaviors\UpdateTimeBehavior',
                'addColumn'=>false
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
			'porch'=>[\CActiveRecord::BELONGS_TO, '\extend\modules\buildings\models\Porch', 'porch_id'],
		    'apartments'=>[\CActiveRecord::HAS_MANY, '\extend\modules\buildings\models\Apartment', 'floor_id'],
		    'apartmentsCount'=>[\CActiveRecord::STAT, '\extend\modules\buildings\models\Apartment', 'floor_id'],
		]);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \CModel::rules()
	 */
	public function rules()
	{
		return $this->getRules([
		    ['porch_id, number', 'required'],
		    ['porch_id, number', 'numerical', 'integerOnly'=>true],
            ['title, map_hash, text', 'safe'],
		]);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \CModel::attributeLabels()
	 */
	public function attributeLabels()
	{
		return $this->getAttributeLabels([
		    'porch_id'=>'Подъезд',
		    'number'=>'Номер этажа',
            'title'=>'Наименование',
		    'text'=>'Описание'	  
		]);
	}
	
	public function getNumberTitle($returnTitle=false)
	{
	    if($returnTitle && $this->title) {
	        return $this->title;
	    }
	    return "Этаж № {$this->number}";
	}
}
