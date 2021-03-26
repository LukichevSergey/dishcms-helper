<?php
/**
 * Модель значений атрибутов для регионов
 */
namespace extend\modules\regions\models;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HHash;
use extend\modules\regions\components\helpers\HRegion;

class Data extends \common\components\base\ActiveRecord
{
	/**
	 * Генерация уникального хэша.
	 * 
	 * Для прямого SQL запроса можно использовать выражение:
	 * hash=CRC32(CONCAT(region_id, "|", model_name, "|", model_attribute, "|", model_id)))
	 * 
	 * @param object|string $model объект или имя класса модели.
	 * @param string $attribute имя аттрибута.
	 * @param integer|false $id идентификатор модели. 
	 * По умолчанию (false) не задан.
	 * @param integer|false $regionId идетификатор региона. 
	 * По умолчанию (false) текущий.
	 * @return string
	 */
	public static function generateUHash($model, $attribute, $id=false, $regionId=false)
	{
		if(!$id) {
			$id='0';
		}
		
		if(is_object($model)) {
			$model=get_class($model);
		}
		
		if(!$regionId) {
			$regionId=HRegion::i()->getId();
		}
		
		return HHash::ucrc32($regionId . '|' . $model . '|' . $attribute . '|' . $id);
	}
	
	/**
	 * Генерация общего хэша.
	 * 
	 * Для прямого SQL запроса можно использовать выражение:
	 * hash=CRC32(CONCAT(model_name, "|", model_attribute, "|", model_id)))
	 * 
	 * @param object|string $model объект или имя класса модели.
	 * @param string $attribute имя аттрибута.
	 * @param integer|false $id идентификатор модели. 
	 * По умолчанию (false) не задан.
	 * @return string
	 */
	public static function generateHash($model, $attribute, $id=false)
	{
		if(!$id) {
			$id='0';
		}
		
		if(is_object($model)) {
			$model=get_class($model);
		}
		
		return HHash::ucrc32($model . '|' . $attribute . '|' . $id);
	}
	
	/**
	 * Краткий псевдоним для generateUHash()
	 * @see Data::generateUHash()
	 */
	public static function guh($model, $attribute, $id=false, $regionId=false) 
	{
		return self::generateUHash($model, $attribute, $id, $regionId);
	}
	
	/**
	 * Краткий псевдоним для generateHash()
	 * @see Data::generateHash()
	 */
	public static function gh($model, $attribute, $id=false) 
	{
		return self::generateHash($model, $attribute, $id);
	}
	 
	/**
	 * (non-PHPdoc)
	 * @see \CActiveRecord::tableName()
	 */
	public function tableName()
	{
		return 'regions_data';
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CModel::behaviors()
	 */
	public function behaviors()
	{
		$t=Y::ct('\extend\modules\regions\RegionsModule.models/data', 'extend.regions');
		return A::m(parent::behaviors(), [
			'updateTimeBehavior'=>[
				'class'=>'\common\ext\updateTime\behaviors\UpdateTimeBehavior',
				'attributeLabel'=>$t('label.update_time'),
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
	 * Scope: Выборка по уникальному хэшу.
	 * @param integer $uhash
	 * @return \extend\modules\regions\models\Data
	 */
	public function byUHash($uhash)
	{
		$criteria=new \CDbCriteria();
		$criteria->addColumnCondition(['uhash'=>new \CDbExpression($uhash)]);
		$this->getDbCriteria()->mergeWith($criteria);
		
		return $this;
	}
	
	/**
	 * Scope: Выборка по общему кэшу
	 * @param integer $hash общий кэш
	 * @return \extend\modules\regions\models\Data
	 */
	public function byHash($hash)
	{
		$criteria=new \CDbCriteria();
		$criteria->addColumnCondition(['hash'=>new \CDbExpression($hash)]);
		$this->getDbCriteria()->mergeWith($criteria);
		
		return $this;
	}
	
	/**
	 * Scope: Выборка по идентификатору региона.
	 * @param integer|false $id идентификатор региона.
	 * По умолчанию (false) текущий регион. 
	 * @return \regions\models\RegionData
	 */
	public function byRegion($id=false)
	{
		if(!$id) {
			$id=HRegion::i()->getId();
		}
		
		$criteria=new \CDbCriteria();
		$criteria->addColumnCondition(['region_id'=>$id]);
		$this->getDbCriteria()->mergeWith($criteria);
		
		return $this;
	}	
	
	/**
	 * (non-PHPdoc)
	 * @see CActiveRecord::relations()
	 */
	public function relations()
	{
		return $this->getRelations([
			'region'=>[\CActiveRecord::BELONGS_TO, '\extend\modules\regions\models\Region', 'region_id']	
		]);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CModel::rules()
	 */
	public function rules()
	{
		return $this->getRules([
			['region_id, model_name, model_attribute', 'required'],
			['hash, uhash, model_id, region_id', 'numerical', 'integerOnly'=>true],
			['model_name, model_attribute, use_default, is_forced, value', 'safe']
		]);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CModel::attributeLabels()
	 */
	public function attributeLabels()
	{
		$t=Y::ct('\SeoModule.models/seo', 'extend.seo');
		return $this->getAttributeLabels([
			'id' => $t('label.id'),
			'region_id' => $t('label.region_id'),
			'hash' => $t('label.hash'),
			'uhash' => $t('label.uhash'),
			'model_name' => $t('label.model_name'),
			'model_id' => $t('label.model_id'),
			'model_attribute' => $t('label.model_attribute'),
			'use_default' => $t('label.use_default'),
			'is_forced' => $t('label.is_forced'),
			'value' => $t('label.value')
		]);
	}
	
	/**
	 * Получить перегенерированный уникальный хэш модели.
	 * @param integer|false $regionId идентификатор региона. 
	 * По умолчанию (false) текущий.
	 * @return string
	 */
	public function resolveUHash($regionId=false)
	{
		return self::generateUHash($this->model_name, $this->model_attribute, $this->model_id, $regionId);
	}
	
	/**
	 * Получить перегенерированный общий хэш модели.
	 * @return string
	 */
	public function resolveHash()
	{
		return self::generateHash($this->model_name, $this->model_attribute, $this->model_id);
	}
	
	/**
	 * Обновить значение атрибута уникального хэша модели.
	 * @param integer|false $regionId идентификатор региона. 
	 * По умолчанию (false) текущий.
	 * @return void
	 */
	public function updateUHash($regionId=false)
	{
		$this->uhash=$this->resolveUHash($regionId);
	}
	
	/**
	 * Обновить значение атрибута общего хэша модели.
	 * @return void
	 */
	public function updateHash()
	{
		$this->hash=$this->resolveHash();
	}
	
	/**
	 * Обновить значение атрибутов хэша модели.
	 * @param integer|false $regionId идентификатор региона. 
	 * По умолчанию (false) текущий.
	 * @return void
	 */
	public function updateHashes($regionId=false)
	{
		$this->updateHash();
		$this->updateUHash($regionId);
	}
	
	/**
	 * Найти модель по уникальному хэшу.
	 * @param string $uhash уникальный хэш модели.
	 * @param string|array|\CDbCriteria $condition условие выборки.
	 * @param array $params параметры выборки.
	 * @return \extend\modules\regions\models\Data
	 */
	public function findByUHash($uhash, $condition='', $params=[])
	{
		return $this->byUHash($uhash)->find($condition, $params);
	}
	
	/**
	 * Найти модели по общему хэшу.
	 * @param string $hash общий хэш модели.
	 * @param string|array|\CDbCriteria $condition условие выборки.
	 * @param array $params параметры выборки.
	 * @return \extend\modules\regions\models\Data
	 */
	public function findAllByHash($hash, $condition='', $params=[])
	{
		return $this->byHash($hash)->findAll($condition, $params);
	}	
}