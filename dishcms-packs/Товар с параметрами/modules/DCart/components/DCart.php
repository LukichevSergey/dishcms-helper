<?php
/**
 * Корзина
 * 
 * @use \AttributeHelper
 * @use \YiiHelper
 */
namespace DCart\components;

use \AttributeHelper as A;

class DCart extends \CComponent
{
	/**
	 * Тип аттрибута: внеший ключ (foreign key)
	 * @var string
	 */
	const FK = 'fk';
	
	/**
	 * Тип аттрибута: значение аттрибута из связи модели (relation)
	 * @var string
	 */
	const RELATION = 'relation';
	
	/**
	 * Идентификатор корзины.
	 * @var string
	 */
	public $cartId=null;
	
	
	/**
	 * Основные ключи (свойства модели), которые будут участвовать в генерации хэша.
	 * @var array
	 */
	public $primaryKeys = array('id');
	
	/**
	 * Дополнительные ключи (свойства модели), которые будут участвовать в генерации хэша.
	 * @var array
	 */
	public $extendKeys = array();
	
	/**
	 * Длина хэш-строки элемента корзины.
	 * @var integer
	 */
	public $hashLength = 8;
	
	/**
	 * Аттрибут pk модели.
	 * @var string
	 */
	public $attributeId = 'id';
	
	/**
	 * Аттрибут заголовока модели.
	 * @var string
	 */
	public $attributeTitle = 'title';
	
	/**
	 * Аттрибут цены модели
	 * @var string
	 */
	public $attributePrice = 'price';
	
	/**
	 * Аттрибут мини-изображения модели.
	 * @var string имя аттрибута модели с ссылкой на файл изображения.
	 * @var array (model, method, params) метод модели, который возвращает ссылку на изображение.
	 */
	public $attributeImage = null;
	
	/**
	 * Массив аттрибутов модели для дополнительной информации.
	 * Может содержать в значении дополнительные параметры.
	 * 
	 * attribute - имя аттрибута
	 * 
	 * 1. Простой тип
	 * array(attribute, attribute, ...)
	 * 
	 * 2. Тип FK:
	 * fk - foreign key, указывает на то, что значение аттрибута является внешним ключом.
	 * array(
	 * 	attribute => array(
	 * 		'type'=>'fk'
	 * 		'label' => метка аттрибута, 
	 * 		'relation' => array(
	 * 			'name' => имя связи,
	 * 			'attributeId' => поле id значения, по умолчанию `id`
	 * 			'attributeValue' => поле значения
	 *		)
	 * ), ...)
	 * 
	 * 3. Тип RELATION
	 * array(
	 * 	attribute => array(
	 * 		'type'=>'relation'
	 * 		'label' => метка аттрибута, 
	 * 		'relation' => array(
	 * 			'name' => имя связи,
	 * 			'attribute' => аттрибут значения в модели связи
	 *		)
	 * ), ...)
	 * 
	 * @var array
	 */
	public $attributes = array();
	
	/**
	 * Специально введенное свойств для отображения мини-корзины
	 * Массив дополнительных аттрибутов для отображения
	 * @var array
	 */
	public $miniCartAttributes = array();
	
	/**
	 * Специально введенное свойств для отображения корзины
	 * Массив дополнительных аттрибутов для отображения
	 * @var array
	 */
	public $cartAttributes = array();
	
	/**
	 * Заголовк по умолчанию, если заданный DCart::$attributeTitle в модели не доступен.
	 * @var string
	 */
	public $defaultTitle = 'Unnamed';
	
	/**
	 * Данные корзины
	 * Данные хранятся в виде массивов с ключами:
	 * id=>id модели
	 * model=>имя класса модели
	 * price=>цена
	 * attributes=>аттрибуты модели со значениями, которые будут участвовать 
	 * в формировании отображения виджетов корзины. 
	 * count=>количество данного товара.
	 * @var array
	 */
	private $_data = array();
	
	/**
	 * Инициализация корзины
	 */
	public function init()
	{
		$this->_load();
	}
	
	/**
	 * Генерация хэша элемента корзины 
	 * @param string $model
	 * @return string
	 */
	public function generateHash($model)
	{
		$str = '';
		foreach(array_merge($this->primaryKeys, $this->extendKeys) as $key) { 
			if(\YiiHelper::attributeExists($model, $key)) 
				$str .= $model->$key;
		}
			
		return substr(sha1($str), 0, $this->hashLength);
	}
	
	/**
	 * Получение элемента корзины
	 * @param string $hash хэш-строка элемента
	 * @return array|null Если элемент не найден, возвращается null.
	 */
	public function get($hash)
	{
		return A::get($this->_data, $hash);		
	}
	
	/**
	 * Получить данные корзины
	 * @param boolean $returnALV возвращать результат в виде простого массива 
	 * (hash=>array(attribute=>array('label'=>label, 'value'=>value)), или внутреннее представление.
	 * @param boolean $serialize Сериализовать результат или нет.
	 * @param boolean $forcy принудительно получить значение аттрибута из модели, если не получено.
	 * @return array|string возвращается строка, если параметр $serialize установлен в true.	 
	 */
	public function getData($returnALV=false, $serialize=false, $forcy=true)
	{
		if($returnALV) {
			$resultData = array();
			foreach ($this->_data as $hash=>$data) {
				$resultData[$hash] = array(
					'id' => array('label'=>'Идентификатор', 'value' => $data['id']),
					'model' => array('label'=>'Модель', 'value' => $data['model']),
					'title' => array('label'=>'Заголовок', 'value' => $data['attributes'][$this->attributeTitle]),
					'price' => array('label'=>'Цена', 'value' => $data['price']),
					'count' => array('label'=>'Количество', 'value' => $data['count'])
				);
				foreach($this->getAttributes(true) as $attribute) {
					list($label, $value) = $this->getAttributeValue($hash, $attribute, true, $forcy);
					$resultData[$hash][$attribute] = array('label' => $label, 'value' => $value);
				}
			}
			return  $serialize ? serialize($resultData) : $resultData;
		}
		
		return  $serialize ? serialize($this->_data) : $this->_data;
	}
	
	/**
	 * Получение аттрибутов дополнительной информации
	 * @param boolean $returnNames Возвращать только имена аттрибутов или нет.
	 * @param boolean $isMiniCart Возвращать аттрибуты для мини-корзины или нет.
	 * @param boolean $isCartWidget Возвращать аттрибуты для виджета корзины или нет.
	 * @return array
	 */
	public function getAttributes($returnNames=false, $isMiniCart=false, $isCartWidget=false)
	{
		if($returnNames) {
			$names = array();
			$attributes = $isMiniCart ? $this->miniCartAttributes : ($isCartWidget ? $this->cartAttributes : $this->attributes);
			foreach($attributes as $key=>$attribute)
				$names[] = is_array($attribute) ? $key : $attribute;
			
			return $names; 
		}
		return $isMiniCart ? $this->miniCartAttributes : ($isCartWidget ? $this->cartAttributes : $this->attributes);
	}
	
	/**
	 * Получить все разрешенные аттрибуты модели
	 * @return array
	 */
	public function getAllAttributes()
	{
		// @todo cache array_merge
		return array_merge(
			array($this->attributeId, $this->attributeTitle, $this->attributePrice), 
			$this->primaryKeys, 
			$this->extendKeys, 
			$this->attributes
		);
	}
	
	/**
	 * Получить значение аттрибута в модели
	 * @param string $hash хэш элемента в корзине
	 * @param string $attribute аттрибут
	 * @param boolean $withLabel получать метку или нет.
	 * @param boolean $forcy принудительно получить значение аттрибута из модели, если не получено.
	 * @return array|mixed Если параметр $withLabel установлен в true, 
	 * возвращается массив вида array(label, value), иначе возращается value.
	 */
	public function getAttributeValue($hash, $attribute, $withLabel=false, $forcy=false)
	{
		$result = $withLabel ? array(null, null) : null;
		
		//if(@is_null($this->_data[$hash]['attributes'][$attribute])) return $result;

		$attributes = $this->getAttributes(true);
		if(!isset($attributes[$attribute]) && !in_array($attribute, $attributes)) return $result;

		$modelClass = $this->_data[$hash]['model'];
		
		$label = '';
		if($withLabel) {
			$label = isset($this->attributes[$attribute]['label']) 
				? $this->attributes[$attribute]['label']
				: A::get($modelClass::model()->attributeLabels(), $attribute, '');
		}
		
		$value = null;
		
		// если аттрибут в ключе, значит для данного аттрибута переданы параметры
		if(isset($this->attributes[$attribute]['type'])) {
			switch($this->attributes[$attribute]['type']) {
				case self::FK:
					if(!isset($this->attributes[$attribute]['relation']['name']) 
						|| !isset($this->attributes[$attribute]['relation']['attributeValue']))
							return $result;
					
					$id = $this->_data[$hash]['attributes'][$attribute];
					$relation = $this->attributes[$attribute]['relation']['name'];
					$relationAttributeValue = $this->attributes[$attribute]['relation']['attributeValue'];
					$relationAttributeId = isset($relation['attributeId']) ? $relation['attributeId'] : 'id';
					
					$model = $modelClass::model()->findByPk($this->_data[$hash]['id'], array(
						'select' => $relationAttributeId . ',' . $relationAttributeValue
					));
					if($model) {
						foreach($model->$relation as $m) {
							if($m->$relationAttributeId == $id) {
								
								return $withLabel ? array($label, $m->$relationAttributeValue) : $m->$relationAttributeValue;
							}
						}
					}
					break; 
				case self::RELATION:
					if(!isset($this->attributes[$attribute]['relation']['name']) 
						|| !isset($this->attributes[$attribute]['relation']['attribute']))
							return $result;
														
					$model = $modelClass::model()->findByPk($this->_data[$hash]['id']);
					if($model) {
						$relation = $this->attributes[$attribute]['relation']['name'];
						if($relModel = $model->getRelated($relation)) {
							$value = $relModel->getAttribute($this->attributes[$attribute]['relation']['attribute']);
							
							return $withLabel ? array($label, $value) : $value;
						}
					}
					break;
			}
		}
		else {
			if(!isset($this->_data[$hash]['attributes'][$attribute])) {
				if(in_array($attribute, $modelClass::model()->attributeNames())) {
					$model = $modelClass::model()->findByPk($this->_data[$hash]['id'], array('select'=>$attribute));
					$value = $model->$attribute;
				}
			}
			else {
				$value = $this->_data[$hash]['attributes'][$attribute];
			}
			
			return $withLabel ? array($label, $value) : $value;
		}
		
		return $result;
	}
	
	/**
	 * Получить количество товара
	 * @param string $hash хэш-товара в корзине.
	 * @return integer|NULL Возвращает NULL, если товар не найден.
	 */
	public function getCount($hash)
	{
		if(isset($this->_data[$hash])) {
			return $this->_data[$hash]['count'];
		}
		
		return null;
	}
	
	/**
	 * Получить общее количество товаров в корзине.
	 */
	public function getTotalCount()
	{
		$count = 0;
		foreach($this->_data as $data)
			$count += $data['count'];
		
		return $count;
	}
	
	/**
	 * Получить общую стоимость 
	 * @param string $hash Хэш товара в корзине. Если передан, 
	 * общая стоимость будет вычислена только для этого товара,
	 * иначе вычисляется общая стоимость всей корзины.
	 * @return number общая стоимость.
	 */
	public function getTotalPrice($hash=null)
	{	
		if($hash && $this->exists($hash)) {
			$item = $this->get($hash);
			return $item['count'] * $item['price'];
		}
		else {
			$total = 0;
			foreach ($this->getData() as $hash=>$item) {
				$total += $item['count'] * $item['price'];
			}
			return $total;
		}
		
		return null;
	}
	
	/**
	 * Получить все хэши товаров в корзине
	 * @return array
	 */
	public function getHashes()
	{
		return array_keys($this->_data);
	}
	
	/**
	 * Получить ссылку на изображение товара
	 * @param string $hash хэш-строка товара в корзине
	 * @return string|null
	 */
	public function getImage($hash) 
	{
		if(!empty($this->attributeImage)) {
			if($data = $this->get($hash)) {
				try {
					if(is_string($this->attributeImage)) {
						$modelClass = $data['model'];
						$model = $modelClass::model()->findByPk($data['id'], array('select' => $this->attributeId));
						$attribute = $this->attributeImage;
						
						return $model->$attribute;
					}
					elseif(is_array($this->attributeImage) && (count($this->attributeImage) > 1)) {
						$modelClass = $this->attributeImage[0];
						$model = $modelClass::model()->findByPk($data['id'], array('select' => $this->attributeId));
						$method = $this->attributeImage[1];
						$params = A::get($this->attributeImage, 2, array());
						
						return call_user_method_array($method, $model, $params);
					}
				}
				catch(\Exception $e) {
				}
			}
		}
		return null;
	}
	
	/**
	 * Проверка на сущестовавание аттрибута элемента корзины. 
	 * @param string $attribute имя аттрибута
	 * @return boolean
	 */
	public function attributeExists($attribute)
	{
		return in_array($attribute, $this->getAllAttributes());
	}
	
	/**
	 * Пустая корзина или нет.
	 * @return boolean
	 */
	public function isEmpty()
	{
		return !boolval(count($this->_data));
	}
	
	/**
	 * Проверка существования данного товара в корзине
	 * @param string $hash хэш элемента в корзине
	 */
	public function exists($hash)
	{
		return isset($this->_data[$hash]);
	}
	
	/**
	 * Добавление модели в корзину 
	 * @param object $model модель товара.
	 * @param integer $count количество товара.
	 * @return string|boolean Если в модели не доступен аттрибут pk, заданный в DCart::$attributeId, возвращается false.
	 * В другом случае, возвращается хэш добавленного элемента.
	 */
	public function add($model, $count=1)
	{
		if(!\YiiHelper::attributeExists($model, $this->attributeId)) return false;
		
		$hash = $this->generateHash($model);
		
		if($this->exists($hash)) {
			$this->_data[$hash]['count'] += $count;
		}
		else {
			$this->_data[$hash]['count'] = $count;
			
			$attributeId = $this->attributeId;
			$this->_data[$hash]['id'] = $model->$attributeId;
			$this->_data[$hash]['model'] = get_class($model);
			
			$attributeTitle = $this->attributeTitle;
			$this->_data[$hash]['attributes'][$attributeTitle] = \YiiHelper::attributeExists($model, $attributeTitle) ? $model->$attributeTitle : $this->defaultTitle;
			
			$attributePrice = $this->attributePrice;
			$this->_data[$hash]['price'] = \YiiHelper::attributeExists($model, $attributePrice) ? $model->$attributePrice : 0;
			
			foreach($this->attributes as $key=>$attribute) {
				$attr = is_array($attribute) ? $key : $attribute;
				$this->_data[$hash]['attributes'][$attr] = \YiiHelper::attributeExists($model, $attr) ? $model->$attr : null;
			}
		}
		
		$this->_save();
		
		return $hash;
	}
	
	/**
	 * Обновление количества
	 * @param string $hash хэш товара в корзине.
	 * @param integer $count новое количество товара.
	 */
	public function updateCount($hash, $count)
	{
		if(isset($this->_data[$hash]) && ($count > 0)) {
			$this->_data[$hash]['count'] = $count;
			
			$this->_save();
			
			return true;
		}
		
		return false;
	}
	
	/**
	 * Удаление товара из корзины
	 * @param string $hash хэш товара в корзине.
	 */
	public function remove($hash)
	{
		if(isset($this->_data[$hash])) {
			unset($this->_data[$hash]);
			
			$this->_save();
			
			return true;
		}
		
		return false;
	}
	
	/**
	 * Очистка корзины
	 */
	public function clear()
	{
		$this->_data = array();
		
		$this->_save();
		
		return true;
	}
	
	/**
	 * Сохранение данных корзины в хранилище
	 */
	private function _save() 
	{
		\Yii::app()->user->setState('yii-module-dcart-data-store' . ($this->cartId ?: ''), $this->_data);
	}
	
	/**
	 * Загрузка данных корзины из хранилища
	 */
	private function _load()
	{
		$this->_data = \Yii::app()->user->getState('yii-module-dcart-data-store' . ($this->cartId ?: ''), array());
	}
}