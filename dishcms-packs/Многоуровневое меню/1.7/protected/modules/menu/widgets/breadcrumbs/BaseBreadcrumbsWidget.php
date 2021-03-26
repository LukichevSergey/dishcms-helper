<?php
/**
 * Base breadcrumbs widget
 */
namespace menu\widgets\breadcrumbs;

use \menu\models\Menu;
use \menu\components\helpers\UrlHelper;

class BaseBreadcrumbsWidget extends \CWidget
{
	/**
	 * breadcrumbs leaf id
	 * Default (null) get form \CHttpRequest::$pathInfo.
	 * @var integer
	 */
	public $id = null;
	
	/**
	 * Is admin section.
	 * @var boolean
	 */
	public $adminMode = false;
	
	/**
	 * Get all or only visibled items. Default (true) only visibled.
	 * @var boolean
	 */
	public $visibled = true;

	/**
	 * Css class for root breadcrumbs DOM element
	 * @var string
	 */
	public $cssClass = '';
	
	/**
	 * Breadcrumbs (for pseudo-caching)
	 * @var array
	 */
	protected $breadcrumbs = array();
	
	/**
	 * (non-PHPdoc)
	 * @see CWidget::init()
	 */
	public function init()
	{
		if(is_null($this->id)) 
			$this->id = UrlHelper::getMenuId(\Yii::app()->request->pathInfo);
		
		$model = Menu::model()->nonsystem();
		if($this->visibled) $model = $model->visibled();
		
		$this->breadcrumbs = \TreeModelHelper::getBreadcrumbs($this->id, $model->findAll(array('order'=>'ordering')), 'id', 'parent_id');
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CWidget::run()
	 */
	public function run()
	{
	}
}