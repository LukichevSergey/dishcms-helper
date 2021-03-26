<?php
/**
 * Ajax Behavior for controller
 * 
 */
class AjaxControllerBehavior extends CBehavior
{
	/**
	 * Ajax ActiveForm validate.
	 *
	 * @param mixed $model A single model instance or an array of models.
	 * @param string $formId Form id.
	 */
	protected function performAjaxValidation($model, $formId)
	{
		if ($this->isAjaxValidation($formId)) {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	
	/**
	 * Is ajax validation
	 * @param string $formId form id.
	 * @return boolean
	 */
	protected function isAjaxValidation($formId)
	{
		// @TODO Profile it!
		// return (isset($_POST['ajax']) && ($_POST['ajax'] === $formId));
		return (Yii::app()->request->getPost('ajax') === $formId);
	}
	
}