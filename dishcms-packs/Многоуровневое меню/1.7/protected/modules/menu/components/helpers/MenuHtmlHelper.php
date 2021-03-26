<?php
/**
 * Html helper for menu module
 * 
 * @use \menu\components\helpers\UrlHelper
 * @use \TreeModelHelper
 * @use \HtmlHelper
 */
namespace menu\components\helpers;

class MenuHtmlHelper extends \CComponent
{
	/**
	 * Get menu list (<ul><li>)
	 * @param array|\menu\models\Menu $models array of menu models.
	 * @param string $id id attribute name. @see \TreeModelHelper::getTree()
	 * @param string $parentId parent id attribute name. @see \TreeModelHelper::getTree() 
	 * @param array $htmlOptions additional html options for root (<ul>) DOM element.
	 * @param string $view alias for Yii::import() of view template for rendring item. Default (null) only model title.
	 * @param null|integer $rootLimit max count of visible root items. Null value for unlimit. 
	 * @return string
	 */
	public static function getList($models, $id=null, $parentId=null, $htmlOptions=array(), $view=null, $rootLimit=null)
	{
		// @var function render menu items.
		// @param array $items menu items, like as, result of \TreeModelHelper::getTree().
		// @param boolean $level deep level. Zero value is root.
	 	// @return string list HTML code of menu items.
		$funcGetList = function (&$items, $level=0) use (&$funcGetList, &$htmlOptions, &$view, &$rootLimit)
		{
			$html = '<ul ';
			if(!$level) $html .= \HtmlHelper::AttributesToString($htmlOptions);
			$html .= '>';
		
			$i=0;
			foreach($items as $item) {
				if((!$level && !is_null($rootLimit)) && ($i++ >=$rootLimit)) break;
		
				$html .= '<li>';
				if(is_null($view)) {
					$html .= '<div>' . $item['model']->title . '</div>';
				}
				else {
					$model = $item['model'];
					\Yii::import($view, true);
				}
					
				if(!empty($item['childs'])) {
					$html .= $funcGetList($item['childs'], ($level + 1), true);
				}
					
				$html .= '</li>';
			}
		
			$html .= '</ul>';
		
			return $html;
		};
		
		$tree = \TreeModelHelper::getTree($models, $id, $parentId);
		
		return $funcGetList($tree);
	}
}