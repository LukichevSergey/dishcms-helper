<?php
/**
 * Ajax helper
 * 
 * Compliances of return result.
 * 
 * Результат возвращается JSON кодированным массивом:
 * array(
 * 	'success' => @see AjaxHelper::$success,
 *  'data' => @see AjaxHelper::$data,
 *  'errors' => @see AjaxHelper::$errors (может не возвращатся).
 * )
 */

class AjaxHelper extends CComponent
{
	/**
	 * State of ajax request
	 * @var boolean
	 */
	public $success = false;
	
	/**
	 * Data
	 * @var mixed
	 */
	public $data = null;
	
	/**
	 * Errors
	 * @var array
	 */
	public $errors = array();
	
	/**
	 * Error default message.
	 * @var string
	 */
	public $errorDefaultMessage = 'Произошла ошибка на сервере. Действие не выполнено.';
	
	/**
	 * Отправить результат и завершить приложение.
	 * Результат JSON кодируется.
	 *  
	 * @param string $sendErrors Возвращать ли ошибки, при успехе или нет. По умолчанию (false) не возвращать.
	 */
	public function endFlush($sendErrors=false)
	{
		$result = array(
			'success' => $this->success,
			'data' => $this->data,
			'errorDefaultMessage' => $this->errorDefaultMessage
		);
		
		if(!$this->success || ($this->success && $sendErrors))
			$result['errors'] = $this->errors;
		
		echo CJSON::encode($result);
		Yii::app()->end(); 
	}
}