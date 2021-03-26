<?php
/**
 * Поведение атрибута модели для региона. 
 * Может содержать различные значения для каждого из заданных регионов.
 * 
 * Только для моделей \CActiveRecord и \CFormModel
 * 
 * Модель должна быть наслеюдуема от \common\components\base\ActiveRecord, либо
 * использовать в модели трейт \common\traits\Model
 *  
 * ВНИМАНИЕ! При подключении поведения, значения атрибута для основного региона, при сохранении,
 * сохраняются отдельно. Таким образом, при отключении поведения, будет использованы изначальные данные.
 * 
 * ВНИМАНИЕ! Значение параметра "Использовать значения региона по умолчанию" игнорируется, если модель 
 * используется контроллером с поведением \extend\modules\regions\behaviors\AdminControllerBehavior. 
 * 
 * @todo Обновить основные данные атрибутов можно вручную, используя инструмент "Обновить данные" 
 * в разделе "Настройки модуля Регионы".
 *  
 */
namespace extend\modules\regions\behaviors;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HRequest as R;
use common\components\helpers\HCache;
use common\components\helpers\HHash;
use common\components\helpers\HHtml;
use extend\modules\regions\components\helpers\HRegion;
use extend\modules\regions\models\Data;

class RegionAttributeBehavior extends \CBehavior
{
	/**
	 * @var string префикс поля формы региона.
	 */
	public static $regionHashFieldName = 'region_id_hash_field';
	
	/**
	 * @var string имя атрибута, значение которого будет индивидуальным для каждого из регионов.
	 */
	public $attribute;
	
	/**
	 * @var bool атрибут, переданный в параметре RegionAttributeBehavior::$attribute, является обязательным.
	 * По умолчанию (FALSE) нет.
	 */
	public $required = false;
	
	/**
	 * @var string подпись атрибута, переданного в параметре RegionAttributeBehavior::$attribute.
	 * По умолчанию (NULL) брать из основного.
	 */
	public $attributeLabel = false;
	
	/**
	 * @var array|string|false имена атрибутов, значение которых будет индивидуальным для каждого из регионов.
	 * Может передана строка с атрибутам, разделенными запятой.
	 * Может быть передан простым списком, либо атрибут может содержать
	 * attribute=>["required"=>required, "label"=>label],
	 * где "required" (bool) является ли атрибут обязательным для заполнения. По умолчанию FALSE.
	 * "label" (string) текстовое название атрибута.
	 * "origin" (string) имя оригинального атрибута. По умолчанию равен attribute.
	 * Может быть передан FALSE, в таком случае, оригинальный атрибут будет проигнорирован,
	 * но в таком случае, значение атрибута в оригинальной модели будет перезаписываться
	 * новыми значениями.
	 * Все значения не являются обязательными, таким образом, может быть передано только имя
	 * атрибута [attribute, ...]
	 */
	public $attributes = false;
	
	/**
	 * @var string имя атрибута ID модели. По умолчанию "id".
	 * Должен быть задано FALSE-значение, если атрибута id нет.
	 */
	public $attributeId='id';
	
	/**
	 * @var string|NULL имя оригинального атрибута модели, в котором хранились
	 * данные до подключения поведения для регионов.
	 * По умолчанию (FALSE) будет произведена попытка получения по значению
	 * RegionAttributeBehavior:$attribute.
	 * Если задано NULL, оригинальный атрибут будет проигнорирован. Полезно,
	 * если атрибуты поведения не существуют в оригинальной модели.
	 */
	public $originAttribute = false;
	
	/**
	 * @var string префикс переменных для получения оригинального значения атрибута.
	 * По умолчанию "__roa__", таким образом для получения оригинального значения
	 * нужно обратиться к $model->__roa__{attribute}, напр., $model->__roa__price; 
	 */
	public $originPrefix = '__roa__';
	
	/**
	 * @var string префикс переменных для получения значения атрибута региона.
	 * По умолчанию "__regionAttribute__", таким образом для получения значения региона
	 * нужно обратиться к $model->__regionAttribute__{region_id}{attribute}, 
	 * напр., $model->__regionAttribute__2price, где 2(два) - это идентификатор региона. 
	 */
	public $regionAttributePrefix = '__regionAttribute__';
	
	/**
	 * @var array массив значения атрибута(ов) вида 
	 * array(regionId=>array(attribute=>value)).
	 */
	protected $values=[];
	
	/**
	 * @var array массив значения оригинального атрибута(ов) вида
	 * array(attribute=>value).
	 */
	protected $originValues=[];
	
	/**
	 * @var array|false массив правил валидации.
	 */
	protected $rules=false;
	
	/**
	 * @var array|false массив меток атрибутов.
	 */
	protected $attributeLabels=false;
	
	/**
	 * @var array кэш моделей для сохранения.
	 */
	protected $cacheBeforeSaveModels = [];
	
	/**
	 * @var array кэш значений модели для сохранения
	 */
	protected $cacheBeforeSaveValues = [];
	
	/**
	 * @var array имена аттрибутов
	 */
	private $_attributes = false;
	
	private static $getOriginAttributeMode = false;
	
	/**
	 * Получить имя поля хэша региона для формы.
	 * @return string
	 */
	public static function getRegionHashFieldName()
	{
		return md5(static::$regionHashFieldName);
	}
	
	/**
	 * Получить код поля формы хэша региона.
	 * Данное поле требуется для сохранения целостности данных,
	 * напр., при смене региона в другой вкладке бразуера.
	 * @param integer|false $regionId идентификатор региона.
	 * По умолчанию (false) текущий.
	 * @return string
	 */
	public static function getRegionHashField($regionId=false)
	{
		if(!$regionId) {
			$regionId=HRegion::i()->getId();
		}
		
		return \CHtml::hiddenField(static::getRegionHashFieldName(), $regionId);
	}
	
	/**
	 * Проверка поля хэша региона. 
	 */
	public static function checkRegionHashField()
	{
		if($regionHashFieldValue=R::rget(static::getRegionHashFieldName())) {
			if((int)$regionHashFieldValue !== (int)HRegion::i()->getId()) {
				return false;
// 				if(HRegion::i()->region((int)$regionHashFieldValue, true)) {
// 					HRegion::i()->setCookie($regionHashFieldValue);
// 				}
// 				else {
// 					R::e400();
// 				}
			}
		}
		
		return true;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \CBehavior::events()
	 */
	public function events()
	{
		return array(
			'onBeforeSave'=>'beforeSave',
			'onAfterSave'=>'afterSave',
			'onAfterDelete'=>'afterDelete'
		);
	}
	
	/**
	 * Обработчик для магического метода __get().
	 * @param string $name имя атрибута
	 * @return NULL|mixed
	 */
	public function __handlerGet($name)
	{
		return function($name) {
			try {
				if(strpos($name, $this->originPrefix) === 0) {
				    $name=substr($name, strlen($this->originPrefix));
				    $origin=$this->attributes[$name]['origin'];
					if($origin === false) {
					    $origin=$name;
					}
					
					if(isset($this->originValues[$origin])) {
					    return $this->originValues[$origin];
					}
					
					if(static::$getOriginAttributeMode) {
					    throw new \common\components\exceptions\PropertyNotFound();
					}
					
					return $this->getOriginAttributeValue($origin);
				}
				elseif(preg_match("/^{$this->regionAttributePrefix}(\d+)(.*?)$/", $name, $matches)) {
					$regionId=(int)$matches[1];
					$name=$matches[2];
					if(A::existsKey($this->attributes, $name)) {
						if(!isset($this->values[$regionId]) || !A::existsKey($this->values[$regionId], $name)) {
							if($model=Data::model()->utcache(HCache::YEAR)->findByUHash($this->getUHash($name, $regionId))) {
								$this->values[$regionId][$name]=$model->value;
							}
							else {
								$this->values[$regionId][$name]=null;
							}
						}
						return $this->values[$regionId][$name];
					}				
					return null;
				} 
				elseif(A::existsKey($this->attributes, $name)) {
				    $regionId=(int)HRegion::i()->getId();
				    if(isset($this->values[$regionId]) && array_key_exists($name, $this->values[$regionId])) {
				        return $this->values[$regionId][$name];
				    }
				    elseif($model=Data::model()->utcache(HCache::YEAR)->findByUHash($this->getUHash($name))) {
				        if($model->value !== '') {
				            return $model->value;
				        }
				    }
				    return $this->getOriginAttributeValue($name);
				}
				throw new \common\components\exceptions\PropertyNotFound();
			}
			catch(\Exception $e) {
				if(($this->owner instanceof \CFormModel) && ($e instanceof \common\components\exceptions\PropertyNotFound)) {
					throw $e;
//					return null;
				}
				else {
					throw $e;
				}
			}
		};
	}
	
	/**
	 * Обработчик для магического метода __set().
	 * @param string $name имя атрибута
	 * @param mixed $value значение
	 */
	public function __handlerSet($name, $value)
	{
		return function($name, $value) {
		    if(strpos($name, $this->originPrefix) === 0) {
		        $name=substr($name, strlen($this->originPrefix));
		        $origin=$this->attributes[$name]['origin'];
		        if($origin === false) {
		            $origin=$name;
		        }
		        $this->originValues[$origin] = $value;
		    }
			elseif(A::exists($name, $this->attributes)) {
				$this->values[HRegion::i()->getId()][$name]=$value;
			}
			else {
				throw new \common\components\exceptions\PropertyNotFound();
			}
		};
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \CBehavior::attach()
	 */
	public function attach($owner)
	{
		// нормализация массива атрибутов
		$paramAttributes=$this->getAttributes();
		if($this->attributes) {
			foreach($paramAttributes as $attribute=>$config) {
				if(is_array($config)) {
					$attributes[$attribute]=[
						'required'=>A::get($config, 'required', false),
						'label'=>A::get($config, 'label', false),
						'origin'=>A::get($config, 'origin', $attribute)
					];
				}
				elseif(is_string($config)) {
					$attributes[$config]=['required'=>false, 'label'=>false, 'origin'=>$config];
				}
			}
		}
		if($this->attribute) {
			if($this->originAttribute === false) {
				$this->originAttribute=$this->attribute;
			}
			$attributes[$this->attribute] = [
				'required'=>$this->required, 
				'label'=>$this->attributeLabel, 
				'origin'=>$this->originAttribute
			];
		}
		$this->attributes=$attributes;
		
		if($this->originAttribute === false) {
			$this->originAttribute=$this->attribute;
		}
		
		parent::attach($owner);
	}
	
	/**
	 * @see \CActiveRecord::rules()
	 * @return array
	 */
	public function rules()
	{
		if(!$this->rules) {
			$rules=[];
			
			foreach($this->attributes as $attribute=>$config) {
				$rules[]=[$attribute, 'safe'];
				if(A::get($config, 'required', false)) {
					$rules[]=[$attribute, 'required'];
				}
			}
			
			$this->rules=$rules;
		}
		
		return $this->rules;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \CActiveRecord::attributeLabels()
	 */
	public function attributeLabels()
	{
		if(!$this->attributeLabels) {
			$labels=[];
			foreach($this->attributes as $attribute=>$config) {
				if($label=A::get($config, 'label')) {
					$labels[$attribute]=$label;
				}
			}
			
			$this->attributeLabels=$labels;
		}
		
		return $this->attributeLabels;
	}
	
	/**
	 * Обработчик afterAttributeLabels()
	 * @return array
	 */
	public function afterAttributeLabels()
	{
		if(!HRegion::i()->hasAdminControllerBehavior()) {
			return [];
		}
		
		return [function($labels, $owner, $behaviorName) {
			foreach($labels as $attribute=>$label) {
				if(A::exists($attribute, $this->attributes)) {
					$labels[$attribute]=$label;
					if(!HRegion::i()->isDefault()) { 
						$labels[$attribute].=' (' . HRegion::i()->getTitle() . ')'; 
							// . $owner->asa($behaviorName)->getExtendFormFields($attribute);
					}
					$labels[$attribute].=self::getRegionHashField();
				}
			}
			return $labels;
		}];
	}
	
	/**
	 * Получить список имен атрибутов.
	 * @return array
	 */
	public function getAttributes()
	{
		if(is_array($this->_attributes)) {
			return $this->_attributes;
		}
		elseif(!$this->attributes) {
			$this->_attributes=[$this->attribute];
		}
		elseif(is_string($this->attributes)) {
			$this->_attributes=explode(',', str_replace(' ', '', $this->attributes));
		}
		elseif(is_array($this->attributes)) {
			$this->_attributes=$this->attributes;
		}
		else {
			$this->_attributes=[];
		}
		
		return $this->_attributes;
	}
	
	/**
	 * Получить имя атрибута оригинального значения.
	 * @param string $attribute имя атрибута модели.
	 * @return string
	 */
	public function getOriginAttributeName($attribute)
	{
		return $this->originPrefix . $attribute;
	}
	
	/**
	 * Получить значение оригинального атрибута
	 * @param string $attribute имя атрибута
	 */
	public function getOriginAttributeValue($attribute, $owner=null)
	{
	    if($owner === null) {
	        $owner = $this->owner;
	    }
	    if($owner instanceof \CActiveRecord) {
	        if($owner->hasAttribute($attribute)) {
	            return $owner->getAttribute($attribute);
	        }
	        else {
	            $relations = $owner->relations();
	            foreach($relations as $relName=>$relConfig) {
	                if ($related = $owner->getRelated($relName)) {
                       return $this->getOriginAttributeValue($attribute, $related);
                    }
	            }
	        }
	    }
	    elseif($owner instanceof \CFormModel) {
            static::$getOriginAttributeMode = true;
            $originAttribute = $this->originPrefix . $attribute;
            $value = $owner->$originAttribute;
            static::$getOriginAttributeMode = false;
            return $value;
	    }
	    
	    return null;
	}
	
	/**
	 * Получить имя атрибута значения региона.
	 * @param string $attribute имя атрибута модели.
	 * @return string
	 */
	public function getRegionAttributeName($attribute, $regionId=false)
	{
		if(!$regionId) {
			$regionId = HRegion::i()->getId();
		}
		
		return $this->regionAttributePrefix . $regionId . $attribute;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \CActiveRecord::beforeSave()
	 */
	public function beforeSave()
	{
		$ownerName=$this->getOwnerName();
		
		if(!$ownerName) {
			return true;
		}
	
		if(!static::checkRegionHashField()) {
			$t=Y::ct('\extend\modules\regions\RegionsModule.behaviors/regionAttributeBehavior', 'extend.regions');
			Y::setFlash(Y::FLASH_SYSTEM_ERROR, $t('error.regionChanged'));
			return false;
		}
		
		$this->cacheBeforeSaveModels = [];
		foreach($this->attributes as $attribute=>$config) {
			$model=Data::model()->findByUHash($this->getUHash($attribute));
			
			if(!$model) {
				$model=new Data();
				$model->region_id=HRegion::i()->getId();
				$model->model_name=$ownerName;
				$model->model_attribute=$attribute;
				$model->model_id=$this->getOwnerId();
			}
			
			$model->value=$this->owner->$attribute;
			$model->use_default=$this->getUseDefaultParam($attribute);
			
			if(HRegion::i()->enableForced) {
				$model->is_forced=$this->getIsForcedParam($attribute);
			}
			
			$model->updateHash();
			$model->updateUHash();
			
			if($this->isOwnerIsNewRecord()) {
				$this->cacheBeforeSaveModels[] = $model;
			}
			else {
				$model->save();
			}
		}
		
		// возвращаем внешней модели предыдущие значения (чтобы оставить без изменений оригинал)
		// работает только с моделями CActiveRecord и \common\components\base\FormModel
		foreach($this->attributes as $attribute=>$config) {
		    $this->cacheBeforeSaveValues[$attribute] = $this->owner->$attribute;
		    if($config['origin']) {
				$origin=$config['origin'];
			}
			else {
			    $origin=$attribute;
			}
			
			$originName=$this->originPrefix . $origin;
			$this->owner->$origin=$this->owner->{$originName};
		}
		
		return true;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \CActiveRecord::afterSave()
	 */
	public function afterSave()
	{
		if($this->isOwnerIsNewRecord()) {
			foreach($this->cacheBeforeSaveModels as $model) {
				$model->model_id=$this->getOwnerId();
				$model->updateHash();
				$model->updateUHash();
				$model->save();
			}
		}
		
		foreach($this->attributes as $attribute=>$config) {
		    if(isset($this->cacheBeforeSaveValues[$attribute])) {
		        $this->owner->$attribute = $this->cacheBeforeSaveValues[$attribute];
		    }
		}
		
		return true;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \CActiveRecord::afterDelete()
	 */
	public function afterDelete()
	{
		if($id=$this->getOwnerId()) {
			Data::model()->deleteAllByAttributes(['model_name'=>$this->getOwnerName(), 'model_id'=>$this->getOwnerId()]);
		}
		
		return true;
	}
	
	/**
	 * Получить значение параметра use_default из формы.
	 * Только POST данные.
	 * @param string $attribute имя атрибута
	 * @return boolean
	 */
	public function getUseDefaultParam($attribute, $regionId=false)
	{
		return $this->getExtendFormFieldValue('use_default', $attribute, $regionId);
	}
	
	/**
	 * Получить значение параметра is_forced из формы.
	 * Только POST данные.
	 * @param string $attribute имя атрибута
	 * @return boolean
	 */
	public function getIsForcedParam($attribute, $regionId=false)
	{
		return $this->getExtendFormFieldValue('is_forced', $attribute, $regionId);
	}
	
	/**
	 * Получить HTML-код поля выбора расширенных параметров (use_default и is_forced).
	 * @param string $attribute имя атрибута
	 * @param bool $checked значение атрибута use_default по умолчанию.
	 * @param int|false $regionId id региона. По умолчанию (false) текущий.
	 * @return string
	 */
	public function getExtendFormFields($attribute, $checked=false, $regionId=false)
	{
		if(empty($this->owner)) return '';
		
		$regionDataName=\CHtml::modelName('\extend\modules\regions\models\Data');
		$ownerName=\CHtml::modelName($this->owner);
		$ownerId=$this->getOwnerId();
		$checkBoxId=uniqid('regiondata');
		
		if(!$regionId) {
			$regionId=HRegion::i()->getId();
		}
		
		$hash=Data::generateUHash($ownerName, $attribute, $ownerId, $regionId);
		if($model=Data::model()->findByUHash($hash, ['select'=>'use_default,is_forced'])) {
			$checked=(bool)$model->use_default;
		}
		
		$t=Y::ct('\extend\modules\regions\RegionsModule.behaviors/regionAttributeBehavior', 'extend.regions');
		$defaultAttributeName=$this->getRegionAttributeName($attribute, HRegion::i()->getDefaultRegion()->id);		
		$labelUseDefault = $t('label.use_default', [
			'{default_title}'=>HRegion::i()->getDefaultRegion()->title, 
			'{default_value}'=>HHtml::intro($this->owner->$defaultAttributeName) ?: $t('value.emptyTextOrOrigin')
		]);
		
		$jsId=HHash::ujs();
		$html = '&nbsp;<span class="label label-primary">'
			. \CHtml::checkBox(
				"{$regionDataName}[use_default][{$regionId}][{$ownerName}][{$attribute}]" . ($ownerId ? "[{$ownerId}]" : ''),
				$checked,
				[
					'id'=>$checkBoxId, 
					'class'=>'inline', 
					'style'=>'position:relative;top:3px', 
					'title'=>$t('label.use_default.title', ['{default_title}'=>HRegion::i()->getDefaultRegion()->title])
				]
			);
			
		if(HRegion::i()->enableForced) {
			$html .= \CHtml::checkBox(
				"{$regionDataName}[is_forced][{$regionId}][{$ownerName}][{$attribute}]" . ($ownerId ? "[{$ownerId}]" : ''),
				(bool)$model->is_forced,
				['class'=>'inline', 'style'=>'position:relative;top:3px', 'title'=>$t('label.is_forced')]
			);
		}
		
		$html .= '&nbsp;' . \CHtml::tag('label', ['class'=>'inline', 'for'=>$checkBoxId], $labelUseDefault);
			
		Y::js($jsId, 
			';(function(){'
			. "\n" . 'if($("#'.$jsId.'").parent().is("label")) {'
				. "\n". 'var html='.\CJavaScript::jsonEncode($html).';'
				. "\n". '$("#'.$jsId.'").html(html);$("#'.$jsId.'").show();'
				. "\n". '}'
				. "\n". '})();', 
			\CClientScript::POS_READY
		);
		
		return \CHtml::tag('span', ['style'=>'display:none','id'=>$jsId], '', true); 
	} 
	
	/**
	 * @todo
	 * Получить значение параметра из поля формы,
	 * сгенерированного методом RegionAttributeBehavior::textField().
	 * Только POST данные.
	 * @param string $attribute имя атрибута
	 * @param boolean $saveValues сохранять переданные значения для всех регионов, кроме текущего.
	 */
	public function getTextFieldParam($attribute, $saveValues=false)
	{
		$returnValue=null;
		
		$ownerClass=get_class($this->owner);
		if(isset($_POST[\CHtml::modelName($ownerClass)][$attribute]['_regions_'])) {
			$values=$_POST[\CHtml::modelName($ownerClass)][$attribute]['_regions_'];
			foreach($values as $regionId=>$value) {
				if($regionId == R::i()->id) $returnValue=$value;
				elseif($saveValues) {
					if($model=RegionData::model()->findByHash($this->getHash($attribute, $regionId))) {
						$model->value=$value;
						$model->use_default=$this->getUseDefaultParam($attribute, $regionId);
						$model->save();
					}
					else {
						$model=new RegionData();
						$model->region_id=$regionId;
						$model->model_name=$ownerClass;
						$model->model_attribute=$attribute;
						$model->model_id=$this->getOwnerId();
						$model->value=$value;
						$model->use_default=$this->getUseDefaultParam($attribute, $regionId);
						$model->hash=$model->getHash(true);
						$model->save();
					}
				}
			}
		}
		
		return $returnValue;
	}
	
	/**
	 * @todo
	 * Элемент формы текстовое поле для множественного отображения.
	 * @param \CActiveForm $form объект формы, в которую добавляется поле.
	 * @param string $attribute имя атрибута
	 * @param array $regionCodes массив кодов регионов. По умолчанию (пустой массив) отображаться все.
	 * @param string|NULL $view шаблон отображения поля. По умолчанию (NULL) используется
	 * установленный по умолчанию в виджете \common\widgets\form\TextField.
	 * @return string
	 */
	public function textField($form, $attribute, $htmlOptions=[], $regionCodes=[], $view=null)
	{
		$output='';
		
		$criteria=new \CDbCriteria();
		if(!empty($regionCodes)) {
			$criteria->addInCondition('code', $regionCodes);
		}
		
		if($regions=Region::model()->findAll($criteria)) {
			$currentRegionId=R::i()->id;
			$ownerClass=get_class($this->owner);
			$ownerId=$this->getOwnerId();
			$htmlOptions['data-region-field-id']=$region->id;
			foreach($regions as $region) {
				R::i()->setCurrent($region->id);
				
				$model=new $ownerClass();
				$model->{$this->attributeId}=$ownerId;
				if($modelData=RegionData::model()->findByHash($this->getHash($attribute, $region->id))) {
					$model->$attribute=$modelData->value;
				}
				else $model->$attribute=$this->owner->$attribute;
				
				$htmlOptions['name']=\CHtml::modelName($model)."[{$attribute}][_regions_][{$region->id}]";
				$htmlOptions['value']=$model->$attribute;
				
				$labelOptions=[];
				if($region->id == $currentRegionId) $labelOptions['class']='region__label-active';
				$output.=\Yii::app()->getController()
				->widget('\common\widgets\form\TextField', A::m(compact('form', 'model', 'attribute'), ['htmlOptions'=>$htmlOptions, 'labelOptions'=>$labelOptions]), true);
				
				R::i()->setCurrent($currentRegionId);
			}
			Y::js(
					'region-attribute-behavior-text-field',
					'$("[data-region-field-id]").each(function() { $(this).attr("name", $(this).attr("name")+"[region]["+$(this).data("region-field-id")+"]"); });',
					\CClientScript::POS_READY
					);
		}
		
		return $output;
	}
	
	/**
	 * @access protected
	 *
	 * Получить значение расширенного параметра (такие как "use_default", "is_forced") из формы.
	 * Только POST данные.
	 * @param string $extendAttribute имя расширенного атрибута
	 * @param string $attribute имя атрибута
	 * @param integer|false $regionId идентификатор региона.
	 * По умолчанию (false) текущий.
	 * @return boolean
	 */
	protected function getExtendFormFieldValue($extendAttribute, $attribute, $regionId=false)
	{
		if(!$this->owner) {
			return false;
		}
		
		if(!$regionId) {
			$regionId=HRegion::i()->getId();
		}
		
		$regionDataName=\CHtml::modelName('\extend\modules\regions\models\Data');
		$ownerName=\CHtml::modelName($this->owner);
		$ownerId=$this->getOwnerId();
		
		if(isset($_POST[$regionDataName][$extendAttribute][$regionId][$ownerName][$attribute])) {
			$data=$_POST[$regionDataName][$extendAttribute][$regionId][$ownerName][$attribute];
			if(is_array($data)) {
				return A::existsKey($data, $ownerId) ? $data[$ownerId] : false;
			}
			return $data;
		}
		
		return false;
	}	
	
	/**
	 * Получить общий хэш модели по атрибуту. 
	 * @param string $attribute имя атрибута
	 * @return string
	 */
	protected function getHash($attribute)
	{
		return Data::generateHash($this->owner, $attribute, $this->getOwnerId());
	}
	
	/**
	 * Получить уникальный хэш модели по атрибуту. 
	 * @param string $attribute имя атрибута
	 * @param integer|false $regionId идентификатор региона. 
	 * По умолчанию (false) текущий.
	 * @return string
	 */
	protected function getUHash($attribute, $regionId=false)
	{
		return Data::generateUHash($this->owner, $attribute, $this->getOwnerId(), $regionId);
	}
	
	/**
	 * Получить имя класса внешней модели.
	 * @return string|false возвращает имя класса внешней модели,
	 * либо (false) если внешняя модель не является объектом.
	 */
	protected function getOwnerName()
	{
		if(is_object($this->owner)) {
			return get_class($this->owner);
		}
		
		return false;
	}
	
	/**
	 * Получить значение ID модели
	 * @return integer|false возвращает (false) если атрибут
	 * идентификатора не задан.
	 */
	protected function getOwnerId()
	{
		if($this->attributeId) {
			return $this->owner->{$this->attributeId};
		}
		
		return false;
	}
	
	/**
	 * Внешняя модель является новой моделью \CActiveRecord
	 * @return boolean
	 */
	protected function isOwnerIsNewRecord()
	{
		return (($this->getOwner() instanceof \CActiveRecord) && $this->owner->isNewRecord);
	}	
}
