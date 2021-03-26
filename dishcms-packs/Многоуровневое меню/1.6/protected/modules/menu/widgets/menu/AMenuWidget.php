<?php 
/**
 * Use aMenu jQuery plugin
 * @link http://plugins.jquery.com/jquery-amenu/
 * 
 */
namespace menu\widgets\menu;

class AMenuWidget extends BaseMenuWidget
{
	/**
	 * (non-PHPdoc)
	 * @see BaseMenuWidget::$id
	 */
	public $id='amenu-list';
	
	/**
	 * (non-PHPdoc)
	 * @see BaseMenuWidget::$cssClass
	 */
	public $cssClass = 'amenu-widget-list';
	
	/**
	 * AMenu plugin options
	 * @var array
	 */	
	public $options = array('speed'=>100, 'animation'=>'none'); // animation: show, fade, slide, wind, none

	/**
	 * (non-PHPdoc)
	 * @see \menu\widgets\BaseMenuWidget::init()
	 */
	public function init()
	{
		parent::init();
	
		// publish assets
		\AssetsHelper::publishAssets(array(
			'path' 	=> \Yii::getPathOfAlias('menu.widgets.menu.assets'),
			'js' 	=> array('js/amenu/amenu.js'),
			'css'	=> array('css/amenu/amenu.css')
		));
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \menu\widgets\BaseMenuWidget::run()
	 */
	public function run()
	{
		$tree = $this->getTree(true);
		$menu = $this->renderItems($tree, 0, true);
		$this->render('amenu', compact('menu'));
	}
}