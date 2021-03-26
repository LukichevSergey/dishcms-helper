<?php
use common\components\helpers\HArray as A;

class DController extends \CController
{
	public function actions()
	{
		return A::m(parent::actions(), [
            'captcha'=>[
                'class'=>'CaptchaNumberAction',
                'testLimit'=>false,
                'width'=>150,
                'height'=>37,
                'foreColor'=>0x000000
            ],
		]);

	}

	/**
	 * Загрузка модели
	 * @param mixed $className имя класса модели или объект модели \CActiveRecord
	 * @param mixed $id идентификатор модели
	 * @param mixed $exception бросить исключение, если модель не найдена. По умолчанию TRUE.
	 * Может быть передан массив вида
	 * array(
	 * 	class=>класс исключения. По умолчанию \CHttpException.
	 *  code=>код исключения. По умолчанию 404.
	 *  message=>сообщение исключения. По умолчанию NULL.
	 * );
	 * В массиве могут быть переданы не все параметры.
	 * Любое пустое значение интерпретируется как FALSE.
	 * @param mixed объект критерия(\CDbCriteria или array) для запроса получения модели. По умолчанию NULL(не задан). 
	 * @see \CActiveRecord::findByPk()
	 * @throws CHttpException
	 * @return mixed объект найденой модели. Если $throwException=FALSE и модель не найдена, возвратит NULL.
	 * @FIXME имя первичного ключа для запроса поиска модели задано жестко "id". 
	 */
	public function loadModel($className, $id, $exception=true, $criteria=null)
	{
		if(empty($criteria) || is_array($criteria)) 
			$criteria=new CDbCriteria($criteria?:array());
		
		$criteria->params[':id']=$id;
		$criteria->addCondition('id=:id');
		$model=$className::model()->find($criteria);
	
		if(!empty($exception) && ($model===null)) {
			$isA=is_array($exception);
			$class=($isA && isset($exception['class'])) ? $exception['class'] : '\CHttpException';
			$code=($isA && isset($exception['code'])) ? $exception['code'] : 404;
			$msg=($isA && isset($exception['message'])) ? $exception['message'] : null;
	
			throw new $class($code, $msg);
		}
	
		return $model;
	}
	
	/**
	 * Performs the AJAX validation.
	 *
	 * @param mixed $model the model to be validated.
	 * @param string $formId form id.
	 * @param boolean $isPost Получить данные только из POST запроса.
	 * @return string Если запрос является проверкой на валидацию
	 * выводится результат CActiveForm::validate() и скрипт завершается.
	 */
	protected function performAjaxValidation($model, $formId, $isPost=true)
	{
		if ($this->isAjaxValidation($formId, $isPost)) {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	
	/**
	 * Проверка, является ли текущий запрос, запросом валидации формы.
	 * @param string $formId id формы.
	 * @param boolean $isPost Получить данные только из POST запроса.
	 * @return boolean
	 */
	protected function isAjaxValidation($formId, $isPost=true)
	{
		$ajax = $isPost ? Yii::app()->request->getPost('ajax') : Yii::app()->request->getParam('ajax');
		return (!empty($formId) && $ajax === $formId);
	}
}
