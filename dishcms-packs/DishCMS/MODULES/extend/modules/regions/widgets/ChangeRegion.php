<?php
/**
 * Виджет смены региона
 */
namespace extend\modules\regions\widgets;

use common\components\helpers\HYii as Y;
use extend\modules\regions\components\helpers\HRegion;
use extend\modules\regions\models\Region;

class ChangeRegion extends \common\components\base\Widget
{
	/**
	 * @var string имя элемента выбора.
	 */
	public $name = 'rid';
	
	/**
	 * @var string имя переменной в COOKIE.
	 * По умолчанию (false) будет использована заданная 
	 * в модуле \extend\modules\regions\RegionsModule::$regionCookieName
	 */
	public $cookieName = false;
	
	/**
	 * @var string дополнительная подпись для основного региона.
	 * По умолчанию (false) будет использован заданный в данном виджете. 
	 */
	public $defaultLabelPostfix = false;
	
	/**
	 * {@inheritDoc}
	 * @see \common\components\base\Widget::$tagOptions
	 */
	public $tagOptions = ['class'=>'region__changebox'];
	
	/**
	 * {@inheritDoc}
	 * @see \common\components\base\Widget::$view
	 */
	public $view = 'change_region';
	
	/**
	 * {@inheritDoc}
	 * @see CWidget::init()
	 */
	public function init()
	{
		parent::init();
		
		if($this->defaultLabelPostfix === false) {
			$this->defaultLabelPostfix = \Yii::t('\extend\modules\regions\RegionsModule.widgets/changeRegion', 'label.default.postfix');
		}
		
		if(!$this->cookieName) {
			$this->cookieName = Y::module('extend.regions')->regionCookieName;
		}
	}
	
	/**
	 * {@inheritDoc}
	 * @see \common\components\base\Widget::run()
	 */
	public function run()
	{
		// Y::cs()->registerCoreScript('cookie');
		
		$this->publish();
		
		Y::js(
			false, 
			"extend_modules_regions_widgets_ChangeRegion('{$this->name}', '{$this->cookieName}');", 
			\CClientScript::POS_READY
		);
		
		$this->render($this->view, $this->params);
	}
	
	/**
	 * Получить список регионов вида array(id=>title).
	 * @return array
	 */
	public function getData($appendDefaultLabelPostfix=true)
	{
		if($data = Region::model()->scopeSort('regions')->listData('title')) {
			if($appendDefaultLabelPostfix && $this->defaultLabelPostfix) {
				foreach($data as $id=>$title) {
					if($id == HRegion::i()->getDefaultRegion()->id) {
						$data[$id] .= $this->defaultLabelPostfix;
						break;
					}
				}
			}
		}
		
		return $data;
	}
}