<?php
/**
 * ActiveRecord Helper
 */
class ARHelper extends CComponent
{
	/**
	 * Получение списка аттрибутов, существующих в таблице
	 * @param \CActiveRecord $model модель.
	 * @param array $attributes массив аттрибутов.
	 * @return array
	 */
	public static function getNonVirtualAttributes(\CActiveRecord $model, $attributes)
	{
		if(!is_array($attributes)) return array();
		
		$columns = $model->getTableSchema()->getColumnNames(); 
		foreach($attributes as $idx=>$attribute) {
			if(!in_array($attribute, $columns)) {
				unset($attributes[$idx]);
			}
		}
		
		return $attributes;
	}
}