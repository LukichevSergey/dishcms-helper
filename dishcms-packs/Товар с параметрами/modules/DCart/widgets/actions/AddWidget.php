<?php
/**
 * DCart "Add to cart" action widget
 * 
 * @use \AjaxHelper
 * @use \ARHelper
 */
namespace DCart\widgets\actions;

class AddWidget extends BaseActionWidget
{
	/**
	 * Model id.
	 * @var integer
	 */
	public $id;
	
	public function run()
	{
		$debugMode = defined('YII_DEBUG') && (YII_DEBUG === true);
		 
		$ajaxHelper = new \AjaxHelper();
		
		$modelClass = \Yii::app()->request->getPost('model');
		if($modelClass && !substr_count($modelClass, '\\')) 
			$modelClass = '\\' . $modelClass;
		
		if($debugMode) { 
			if(!$modelClass) 
				$ajaxHelper->errors[] = 'Error: DCart. AddWidget. Model not defined.';
			elseif(!class_exists($modelClass)) 
				$ajaxHelper->errors[] = 'Error: DCart. AddWidget. Model class not exists.';
			elseif(!in_array('CActiveRecord', class_parents($modelClass))) 
				$ajaxHelper->errors[] = 'Error: DCart. AddWidget. Model not instanceof CActiveRecord.';
		}
		
		if($modelClass && class_exists($modelClass) && in_array('CActiveRecord', class_parents($modelClass))) {
			$attributes = \ARHelper::getNonVirtualAttributes($modelClass::model(), \Yii::app()->cart->getAllAttributes());
			$model = $modelClass::model()->findByPk($this->id, array('select' => implode(',', $attributes)));
			
			if(!$model && defined('YII_DEBUG') && (YII_DEBUG === true)) {
				$ajaxHelper->errors[] = 'Error: DCart. AddWidget. Model not found.';
			}
			elseif($model) {
				$data = \Yii::app()->request->getPost('data');
				if(is_array($data)) {
					foreach($data as $attribute=>$value)
						if(property_exists($model, $attribute))
							$model->$attribute = $value;
				}
				
				$count = \Yii::app()->request->getPost('count', 1);
				
				$isEmpty = \Yii::app()->cart->isEmpty();
				if(\Yii::app()->cart->add($model, $count)) {
					$ajaxHelper->success = true;
					$this->prepareAjaxData($ajaxHelper);
					$ajaxHelper->data['cartIsFirst'] = $isEmpty;
				}
			}
		}
			
		$ajaxHelper->endFlush();
	}
}