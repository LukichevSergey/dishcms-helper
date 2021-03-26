<?php
/**
 * Base widget class for menu module
 * 
 * use \TreeModelHelper::getTree()
 */
namespace menu\widgets\menu;

use \menu\models\Menu;
use \menu\components\helpers\UrlHelper;

abstract class BaseMenuWidget extends \CWidget
{
	/**
	 * Root DOM-element id
	 * @var string
	 */
	public $id;
	
	/**
	 * Css class name of root DOM-element
	 * @var string
	 */
	public $cssClass = '';
	
	/**
	 * Max count of visible root items.
	 * Zero value for unlimit.
	 * @var int
	 */
	public $rootLimit=0;

	/**
	 * Is admin section. 
	 * @var boolean
	 */
	public $adminMode = false;
	
	/**
	 * Флаг режима интеграции с меню старого Dishman'a.
	 * Вкл.: в таблицу "menu", добавляется поле "parent_id", если уже не добавлено. 
	 * @var boolean
	 */
	public $integrationMode = false;
	
	/**
	 * Menu tree. For pseudo-caching of generate menu tree. 
	 * Default "false" - not initialized.  
	 * @var boolean|array
	 */
	protected $tree = false;
	
	/**
	 * (non-PHPdoc)
	 * @see CWidget::init()
	 */
	public function init()
	{
		Menu::model()->install($this->integrationMode);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CWidget::run()
	 */
	public function run() 
	{
		// for example	
		// $tree = $this->getTree();
		// $menu = $this->renderItems($tree, 0, true);
		// $this->render('default', compact('menu'));
	} 
	
	/**
	 * Get tree of menu items
	 * @param boolean $visibled if set as true return only visible items. Default (false) get all nonsystem.
	 * @param boolean $reload regenerate tree or not. Default "not" (false).
	 * @return array menu tree (for more @see \TreeModelHelper::getTree()).
	 */
	protected function getTree($visibled=false, $reload=false)
	{
		if($reload || ($this->tree === false)) {
			$model = Menu::model()->nonsystem();
			if($visibled) $model = $model->visibled();
			$this->tree = \TreeModelHelper::getTree($model->findAll(array('order'=>'ordering')), 'id', 'parent_id');
		}
		
		return $this->tree;
	}
	
	/**
	 * Render menu items
	 * @param array $items menu items, like as, result of \TreeModelHelper::getTree().
	 * @param boolean $level deep level. Zero value is root.
	 * @param boolean $return return html code or output to broweser. Default "false" - output to browser. 
	 * @return void|string if parameter $return is true, return HTML code of menu items.
	 */
	protected function renderItems(&$items, $level=0, $return=false)
	{
		$html = '<ul ';
		if(!$level) $html .= \HtmlHelper::AttributesToString(array('id'=>$this->id, 'class'=>$this->cssClass));
		$html .= '>';
		
		$i=0;
		foreach($items as $item) {
			if((!$level && $this->rootLimit) && ($i++ >=$this->rootLimit)) break;

			$url = UrlHelper::createUrl($item['model'], $this->adminMode);
			$html .= '<li';
			if(preg_match('/^\/?(' . preg_replace('/^([^\/]+)\/?.*/', '\\1', \Yii::app()->request->pathInfo) . ')$/Ui', $url)) {
				$html .= ' class="active"';
			}
			$html .= '>';  
			$html .= \CHtml::link($item['model']->title, $url);
			
			if(!empty($item['childs'])) {
				$html .= $this->renderItems($item['childs'], ($level + 1), true);
			}
			
			$html .= '</li>';
		}
		
		$html .= '</ul>';
		
		if($return) return $html; 
		else echo $html;
	}
}