<?php
/**
 * Модель
 */
namespace extend\modules\buildings\models;

use common\components\helpers\HArray as A;
use common\components\helpers\HHtml;

class Apartment extends \common\components\base\ActiveRecord
{
	/**
	 * (non-PHPdoc)
	 * @see \CActiveRecord::tableName()
	 */
	public function tableName()
	{
		return 'buildings_apartments';
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
	        'soldBehavior'=>[
	            'class'=>'\common\ext\active\behaviors\ActiveBehavior',
	            'attribute'=>'sold'
	        ],
	        'imageBehavior'=>[
	            'class'=>'\common\ext\file\behaviors\FileBehavior',
	            'attribute'=>'image',
	            'attributeLabel'=>'Изображение',
	            'attributeAlt'=>'image_alt',
	            'attributeAltEmpty'=>'title',
	            'enableValue'=>true,
	            'defaultSrc'=>HHtml::phSrc(['w'=>120, 'h'=>120, 't'=>'Нет фото']),
	            'imageMode'=>true
	        ],
	        'propsBehavior'=>[
	            'class'=>'\common\ext\dataAttribute\behaviors\DataAttributeBehavior',
	            'attribute'=>'props',
	            'attributeLabel'=>'Дополнительные характеристики'
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
			'available'=>['condition'=>'sold<>1']
		]);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \CActiveRecord::relations()
	 */
	public function relations()
	{
		return $this->getRelations([
			'floor'=>[\CActiveRecord::BELONGS_TO, '\extend\modules\buildings\models\Floor', 'floor_id']
		]);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \CModel::rules()
	 */
	public function rules()
	{
		return $this->getRules([
		    ['title, floor_id', 'required'],
		    ['title, map_hash, sold, price, sale_price, area, rooms, text', 'safe'],
		]);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \CModel::attributeLabels()
	 */
	public function attributeLabels()
	{
		return $this->getAttributeLabels([
		    'floor_id'=>'Этаж',
            'title'=>'Наименование',
		    'sold'=>'Продано',
		    'price'=>'Цена',
		    'sale_price'=>'Цена по акции',
		    'area'=>'Общая площадь',
		    'rooms'=>'Кол-во комнат',
		    'text'=>'Описание'	  
		]);
	}
	
	public function getPropsList($active=false)
	{
	    $data = [];
	    
	    foreach($this->propsBehavior->get($active) as $item) {
	        if(!empty($item['value']) && !empty($item['title'])) {
	            $data[$item['title']] = trim($item['value'] . ' ' . $item['unit']);
	        }
	    }
	    
	    return $data;
	}
	
	public function beforeSave()
	{
	    if(strpos($this->map_hash, '.') !== false) {
	        $this->map_hash = md5($this->map_hash);
	    }
	    
	    return parent::beforeSave();
	}
}
