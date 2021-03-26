<?php
/**
 * Обработка AJAX-запросов
 */
namespace kontur;

use Bitrix\Main\Application;
use Bitrix\Main\EventManager;

EventManager::getInstance()->addEventHandler('main', 'OnBeforeProlog', array('\kontur\Ajax', 'run'));

class Ajax
{
	/**
	 * @var string имя переменной контроллера
	 */
	const CONTROLLER_NAME='ajaxc';
	
	/**
	 * @var string имя переменной действия
	 */
	const ACTION_NAME='ajaxa';
	
	/**
	 * @var string текущий контроллер
	 */
	private static $_controller=null;
	
	/**
	 * @var string текущее действие
	 */
	private static $_action=null;
	
	/**
	 * @return boolean выполняется AJAX-запрос (TRUE) или нет (FALSE).
	 */
	public static function beginAjaxPage($title='', $onlyAjax=true)
	{
		global $APPLICATION;
		
		$isAjax=false;
		if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) || ($_REQUEST['AJAX_CALL']=='Y')) {
			require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
			$isAjax=true;
		}
		else {
			if($onlyAjax) {
				require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
				\CHTTP::SetStatus("404 Not Found");
				$APPLICATION->RestartBuffer();
				exit;
			}
			else {
				require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');
				$APPLICATION->SetTitle($title);
			}
		}
		
		return $isAjax;
	}
	
	public static function endAjaxPage($isAjax=true)
	{
		if(!$isAjax) {
			require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');
		}
		exit;
	}
	
	public static function getRequest()
	{
		return Application::getInstance()->getContext()->getRequest(); 
	}
	
	public static function getPost($name, $default=null)
	{
		return static::getRequest()->getPost($name) ?: $default;
	}
	
	public static function getQuery($name, $default=null)
	{
		return static::getRequest()->getQuery($name) ?: $default;
	}
	
	/**
	 * Запуск процесса обработки AJAX-запросов
	 */
	public static function run()
	{	
		if(static::isAjax() && static::isLoaded()) {
			if(method_exists(static::getClassName(), static::getMethodName())) {
				call_user_func(array(static::getClassName(), static::getMethodName()));
			}
			else {
				throw new \Bitrix\Main\ObjectNotFoundException();
			}
			exit;
		}
	}
	
	/**
	 * Получить текущий ajax-контроллер
	 * @return string|false
	 */
	public static function getController()
	{
		if(static::$_controller === null) {
			static::$_controller=static::getPost(self::CONTROLLER_NAME, static::getQuery(self::CONTROLLER_NAME, false));
		}
		
		return static::$_controller;
	}
	
	/**
	 * Получить текущий ajax-контроллер
	 * @return string|false
	 */
	public static function getAction()
	{
		if(static::$_action === null) {
			static::$_action=static::getPost(self::ACTION_NAME, static::getQuery(self::ACTION_NAME, false));
		}
		
		return static::$_action;
	}

	/**
	 * Запрос является ajax-запросом
	 * @return boolean
	 */
	public static function isAjax()
	{
		return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && static::getController() && static::getAction());
	}
	
	/**
	 * Проверить сущестовавания файла контроллера и подключить
	 * @return boolean файл контроллера найден
	 */
	public static function isLoaded()
	{
		$filename = __DIR__ . '/ajax/' . strtolower(static::getController()) . '.php';
		if(is_file($filename)) {
			include($filename);
			return true;
		}
		
		return false;
	}
	
	/**
	 * Получить имя класса
	 * @return string
	 */
	public static function getClassName()
	{
		return '\kontur\ajax\\' . ucfirst(strtolower(static::getController()));
	}
	
	/**
	 * Получить имя метода
	 * @return string
	 */
	public static function getMethodName()
	{
		return strtolower(static::getAction());
	}
	
		/**
	 * Отправить ответ
	 * @param array $response массив ответа
	 */
	public static function sendResponse($response=array())
	{
		$response=array_merge(array(
			'success'=>true
		), $response);
		
		echo json_encode($response);
	}
}
