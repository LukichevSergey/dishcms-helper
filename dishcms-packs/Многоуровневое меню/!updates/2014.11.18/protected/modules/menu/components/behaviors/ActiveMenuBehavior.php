<?php
/**
 * Active menu behavior
 * 
 * Поведение объекта, как пункта меню.
 * Необходимо, чтобы у объекта были аттрибуты "id", "title"
 */
namespace menu\components\behaviors;

use \menu\models\Menu;

class ActiveMenuBehavior extends \CActiveRecordBehavior
{
	
	/**
	 * (non-PHPdoc)
	 * @see CActiveRecordBehavior::afterSave()
	 */
	public function afterSave()
	{
		// Добавление нового пункта меню
		$menu = $this->findByOptionId($this->owner->id);
		if(!$menu) $menu = new Menu();
		
		$menu->title = $this->owner->title;
		$menu->type = 'model';
		$menu->options = array('model'=> lcfirst(get_class($this->owner)), 'id'=> $this->owner->id);
		if(!$menu->save()) {
			throw new \ErrorException("Пункт меню \"{$this->title}\" не был добавлен.", 0, E_NOTICE);
		}
		
		return true;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CActiveRecordBehavior::afterDelete()
	 */
	public function afterDelete()
	{
		// Удаление пункта меню
		if($menu = $this->findByOptionId($this->owner->id)) {
			Menu::model()->deleteByPk($menu->id);
		}
		
		return true;
	}
	
	/**
	 * Find \menu\models\Menu model by option id
	 * @param integer $optionId options id.
	 * @return \menu\models\Menu|NULL
	 */
	protected function findByOptionId($optionId)
	{
		$items = Menu::model()->findAll();
		foreach($items as $item) {
			if(isset($item->options['model']) && ($item->options['model'] == strtolower(get_class($this->owner)))
				&& isset($item->options['id']) && ($item->options['id'] == $optionId)) {
				return $item;
			}
		}
		
		return null;
	}
}