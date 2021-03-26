<?php
/**
 * Menu url helper
 */
namespace menu\components\helpers;

use \AttributeHelper as A;
use \menu\models\Menu;

class UrlHelper extends \CComponent
{
	/**
	 * Create URL by \menu\models\Menu model
	 * @param \menu\models\Menu $model menu model.
	 * @param boolean $adminMode is admin mode.
	 * @return string url
	 */
	public static function createUrl($model, $adminMode=false)
	{
		if(!($model instanceof Menu)) return false;
		
		$url = 'javascript:;';
		$params = array();
		switch($model->type) {
			case 'model':
				switch($optionModel = A::get($model->options, 'model')) {
					case 'event': 
						if($adminMode) {
							if($id = A::get($model->options, 'id')) { 
								$params = array('id' => $id);
							}
							$url = 'cp/' . $optionModel;
						} else {
							return '/news';
						}
						break;
					case 'page':
						$id = A::get($model->options, 'id');
						if($adminMode) {
							$url = '/cp/page/update' . ($id ? "/{$id}" : '');
						}
						elseif($id) {
							$page = \Page::model()->find(array('select'=>'id, title, alias', 'condition'=>'id=:id', 'params'=>array(':id'=>$id)));
							if($page) {
								// @hook for index page
								if(strtolower($page->alias) == 'index') return \Yii::app()->homeUrl;
								else
									$url = ($page->alias) ? "/{$page->alias}" : "/page/{$id}";
							}
						}
						break;
					case 'blog':
						$id = A::get($model->options, 'id');
						if($adminMode) {
							$url = '/cp/blog/index' . ($id ? "/{$id}" : '');
						}
						else {
							$blog = \Blog::model()->findByPk($id);
							if($blog) return $blog->alias;
						}
						break;
					case 'link':
						$id = A::get($model->options, 'id');
						if($adminMode) {
							$url = '/cp/link/update' . ($id ? "/{$id}" : '');
						}
						elseif($id) {
							$link = \Link::model()->findByPk($id);
							if($link) { 
								$url = $link->url;
								return $url;
							}
						}
						break;
					default:
						if($id = A::get($model->options, 'id')) { 
							$params = array('id' => $id);
						}
						$url = ($adminMode ? 'cp/' : '') . $optionModel;
				}
				break;
		}
		
		return \Yii::app()->createUrl($url, $params);
	}
	
	public static function getMenuId($pathInfo)
	{
		$items = Menu::model()->findAll();
		
		// Поиск по алисам страниц
		$page = \Page::model()->findByAlias($pathInfo);
		if($page) {
			foreach($items as $item) {
				if(($item->type == 'model') 
					&& (A::get($item->options, 'model') == 'page') 
					&& (A::get($item->options, 'id') == $page->id)) {
						return $item->id;
				}
			}
		}
		// Поиск по модулям
		foreach($items as $item) {
			if(($item->type == 'model') && (A::get($item->options, 'model') == $pathInfo)) {
				return $item->id;
			}
		}
		
		return null;
	}
}