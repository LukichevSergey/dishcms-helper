<?php
/**
 * Helper for struct tree for \CActiveRecord model
 */
class TreeModelHelper extends \CComponent
{
	/**
	 * Get tree for \CActiveRecord models.
	 * 
	 * @param array|\CActiveRecord $items array of models.
	 * @param string $id model id attribute name. If set as null, set default "id".
	 * @param string $parentId model parent id attribute name. If set as null, set default "parent_id".
	 * 
	 * @return array tree
	 */
	public static function getTree($models, $id=null, $parentId=null)
	{
		if(is_null($id)) $id = 'id';
		if(is_null($parentId)) $parentId = 'parent_id';
		
		// @var array models array where item like as ($model->id=>$model). "id" attribute name from $id.
		$items = array();//
		// @var array item like as (parent_id=>array(id_child1, id_child2, ...))
		$childs = array();
		// @var array array of ids root models
		$roots = array();
		
		// prepare for generate tree
		foreach($models as $model) {
			$items[$model->$id] = $model;
			
			if(!isset($childs[$model->$id]))
				$childs[$model->$id] = array();
			
			if($model->$parentId)
				$childs[$model->$parentId][] = $model->$id;
			else
				$roots[] = $model->$id;
		}

		// @var function generate tree.
		// @param false|array array of current roots ids.  
	  	// @return array like as array(
	  	//		[ $model->id ] => array(
	  	//			'model' => $model,
	 	//			'childs' => array( 
	 	//				[ $child->id ] => array (
	 	// 					'model' => $child,
	 	// 					'childs' => ...
	 	// 				),
	 	// 				...
	 	// 	  		) 
	 	// 		),
	 	// 		...
	 	// )
	 	// 
	 	// Если модель не содержит потомков, то 'childs' => array()
	 	$funcGenerateTree = function ($ids=false) use (&$funcGenerateTree, &$items, &$childs, &$roots)
		{
			if($ids === false) 
				$ids = $roots;
			elseif(!is_array($ids)) 
				return array();
			
			$tree = array();
			foreach($ids as $id) {
				$tree[$id] = array(
					'model' => $items[$id], 
					'childs' => $funcGenerateTree($childs[$id])
				);
			}
			
			return $tree;
		};
		
		return $funcGenerateTree();
	}
	
	/**
	 * Get breadcrumbs for \CActiveRecord models.
	 *
	 * @param integer $leafId leaf model id.
	 * @param array|\CActiveRecord $items array of models.
	 * @param string $id model id attribute name.
	 * @param string $parentId model parent id attribute name.
	 *
	 * @return array breadcrumbs
	 */
	public static function getBreadcrumbs($leafId, $models, $id='id', $parentId='parent_id')
	{
		// @var array result breadcrumbs
		$breadcrumbs = array();
		
		// @var array models array where item like as ($model->id=>$model). "id" attribute name from $id.
		$items = array();//
		
		// prepare for generate breadcrumbs
		foreach($models as $model) {
			$items[$model->$id] = $model;
		}
		
		if(isset($items[$leafId])) {
			$funcGetBreadcrumbs = function($item) use (&$funcGetBreadcrumbs, &$items, &$breadcrumbs, &$parentId) {
				array_unshift($breadcrumbs, $item);
				
				if($item->$parentId && isset($items[$item->$parentId])) {
					$funcGetBreadcrumbs($items[$item->$parentId]);
				}
			};
			$funcGetBreadcrumbs($items[$leafId]);
		}
		
		return $breadcrumbs;
	}
}