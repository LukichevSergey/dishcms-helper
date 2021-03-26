<?php
/**
 * Модель Регион
 */
namespace extend\modules\regions\models;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HEvent;

class Region extends \common\components\base\ActiveRecord
{
	/**
	 * (non-PHPdoc)
	 * @see \CActiveRecord::tableName()
	 */
	public function tableName()
	{
		return 'regions_regions';
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CModel::behaviors()
	 */
	public function behaviors()
	{
		$t=Y::ct('\extend\modules\regions\RegionsModule.models/region', 'extend.regions');
		return A::m(parent::behaviors(), [
			'sortBehavior'=>'\common\ext\sort\behaviors\SortBehavior',
			'activeBehavior'=>[
				'class'=>'\common\ext\active\behaviors\ActiveBehavior',
				'addColumn'=>false
			],
			'updateTimeBehavior'=>[
				'class'=>'\common\ext\updateTime\behaviors\UpdateTimeBehavior',
				'attributeLabel'=>$t('label.update_time'),
				'addColumn'=>false
			],
		]);
	}
	
	/**
	 * Получить основной регион.
	 * @param boolean $forced принудительно возвращать модель. 
	 * Если будет передано FALSE и основной регион не задан, то 
	 * будет возвращено NULL.  
	 * @return Region
	 */
	public static function getDefaultRegion($forced=true)
	{
		$model = self::model()->default()->find();
		
		if(!$model && $forced) {
			return new self;
		}
		
		return $model;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CActiveRecord::scopes()
	 */
	public function scopes()
	{
		return $this->getScopes([
			'default'=>[
				'condition'=>'is_default=1'
			]	
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
			['code, domain, title', 'required'],
			['code, domain', 'unique'],
			['code', 'length', 'max'=>8],
			['is_default', 'boolean'],
		]);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CModel::attributeLabels()
	 */
	public function attributeLabels()
	{
		$t=Y::ct('\extend\modules\regions\RegionsModule.models/region', 'extend.regions');
		return $this->getAttributeLabels([
			'code' => $t('label.code'),
			'domain' => $t('label.domain'),
			'title' => $t('label.title'),
			'is_default' => $t('label.is_default')
		]);
	}
	
	/**
	 * Получить постфикс региона
	 * @return string
	 */
	public function getPostfix()
	{
		return '_' . $this->code;
	}
	
	/**
	 * {@inheritDoc}
	 * @see CActiveRecord::beforeSave()
	 */
	public function beforeSave()
	{
		if((int)$this->is_default) {
			if($model=self::model()->default()->find()) {
				$model->is_default=0;
				$model->save();
			}
		}
		
		return true;
	}
	
	/**
	 * {@inheritDoc}
	 * @see CActiveRecord::afterSave()
	 */
	public function afterSave()
	{
	    HEvent::raise('onAfterSaveRegion', ['model'=>$this]);
	    
	    return true;
	}
}