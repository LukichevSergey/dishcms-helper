<?php
/**
 * Breadcrumbs widget based on \EXBreadcrumbs
 * 
 * Breadcrumbs items as \menu\models\Menu
 * 
 * use \TreeModelHelper
 */
namespace menu\widgets\breadcrumbs\exbreadcrumbs;

require_once('ExBreadcrumbs.php');

use \menu\models\Menu;
use \menu\components\helpers\UrlHelper;

class MenuEXBreadcrumbsWidget extends \EXBreadcrumbs 
{
	/**
	 * Is admin section.
	 * @var boolean
	 */
	public $adminMode = false;
	
	/**
	 * (non-PHPdoc)
	 * @see \EXBreadcrumbs::$homeText
	 */
    public $homeText='';
    
    /**
     * Get all or only visibled items. Default (true) only visibled.  
     * @var boolean
     */
    public $visibled = true;
     
    /**
     * (non-PHPdoc)
     * @see EXBreadcrumbs::run()
     */
	public function run() 
	{	  
		$id = UrlHelper::getMenuId(\Yii::app()->request->pathInfo);
		
		$model = Menu::model()->nonsystem();
		if($this->visibled) $model = $model->visibled();
		
		$breadcrumbs = \TreeModelHelper::getBreadcrumbs($id, $model->findAll(array('order'=>'ordering')), 'id', 'parent_id');
	    
		$this->links=array();
	    foreach($breadcrumbs as $model) {
	        $this->links[$model->title] = UrlHelper::createUrl($model, $this->adminMode);
	    } 

	    parent::run();
	}
}