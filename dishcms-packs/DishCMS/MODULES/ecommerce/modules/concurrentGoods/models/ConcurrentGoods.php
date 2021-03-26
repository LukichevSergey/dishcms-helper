<?php
/**
 * Модель
 */
namespace ecommerce\modules\concurrentGoods\models;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;

class ConcurrentGoods extends \common\components\base\ActiveRecord
{
	public $id;
	/**
	 * (non-PHPdoc)
	 * @see \CActiveRecord::tableName()
	 */
	public function tableName()
	{
		return 'ecommerce_concurent_goods';
	}
	
	/**
	 * Получить имя атрибута ЧПУ модели.
	 * По умолчанию "sef".
	 * @see \extend\modules\seo\ext\sef\behaviors\SefBehavior::$attribute
	 * @return string
	 */
	public function getSefAttribute()
	{
		return 'sef';
	}
	
	/**
	 * Получить значение проверки уникальности ЧПУ модели.
	 * По умолчанию TRUE.
	 * @see \extend\modules\seo\ext\sef\behaviors\SefBehavior::$unique
	 * @return boolean
	 */
	public function getSefUnique()
	{
		return true;
	}
	
	/**
	 * Получить массив конфигурации проверки уникальности, ЧПУ модели.
	 * По умолчанию пустой массив.
	 * @see \extend\modules\seo\ext\sef\behaviors\SefBehavior::$uniqueWith
	 * @return array
	 */
	public function getSefUniqueWith()
	{
		return [];
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CModel::behaviors()
	 */
	public function behaviors()
	{
		$t=Y::ct('\ecommerce\modules\concurrentGoods\ConcurrentGoodsModule.models/concurrent_goods', 'ecommerce.concurrentGoods');
		return A::m(parent::behaviors(), [
			//'cacheBehavior'=>'\common\ext\cache\behaviors\ARUpdate',
			'sortBehavior'=>'\common\ext\sort\behaviors\SortBehavior',
			'seoBehavior'=>'\extend\modules\seo\behaviors\SeoBehavior',
			'updateTimeBehavior'=>[
				'class'=>'\common\ext\updateTime\behaviors\UpdateTimeBehavior',
				'attributeLabel'=>$t('label.update_time'),
				'addColumn'=>false
			],
			'sefBehavior'=>[
				'class'=>'\extend\modules\seo\ext\sef\behaviors\SefBehavior',
				'attribute'=>$this->getSefAttribute(),
				'attributeLabel'=>$t('label.sef'),
				'unique'=>$this->getSefUnique(),
				'uniqueWith'=>$this->getSefUniqueWith(),
				'addColumn'=>false
			],
			'activeBehavior'=>[
				'class'=>'\common\ext\active\behaviors\ActiveBehavior',
				'attributeLabel'=>$t('label.active'),
				'addColumn'=>false
			],
			'previewImageBehavior'=>[
				'class'=>'\common\ext\file\behaviors\FileBehavior',
				'attribute'=>'preview_image',
				'attributeLabel'=>$t('label.preview_image'),
				'attributeAlt'=>'preview_image_alt',
				'attributeAltLabel'=>$t('label.preview_image_alt'),
				'attributeEnable'=>'preview_image_enable',
				'attributeEnableLabel'=>$t('label.preview_image_enable'),
				'enableValue'=>true,
				'imageMode'=>true
			],
			'propertiesBehavior'=>[
				'class'=>'\common\ext\dataAttribute\behaviors\DataAttributeBehavior',
				'attribute'=>'properties',
				'attributeLabel'=>$t('label.properties'),
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
			['price', 'numerical'],
			['currency, preview_text, detail_text, create_time', 'safe']	
		]);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CModel::attributeLabels()
	 */
	public function attributeLabels()
	{
		$t=Y::ct('\ecommerce\modules\concurrentGoods\ConcurrentGoodsModule.models/concurrent_goods', 'ecommerce.concurrentGoods');
		return $this->getAttributeLabels([
			'title'=>$t('label.title'),
			'price'=>$t('label.price'),
			'currency'=>$t('label.currency'),
			'create_time'=>$t('label.create_time'),
			'preview_text'=>$t('label.preview_text'),
			'detail_text'=>$t('label.detail_text')
		]);
	}
}