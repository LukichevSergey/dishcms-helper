<?php
/**
 * Обработка AJAX-запросов
 */
namespace kontur;

use Bitrix\Main\EventManager; 
use kontur\Request;

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
	 * Запуск процесса обработки AJAX-запросов
	 */
	public static function run()
	{	
		if(self::isAjax() && self::isLoaded()) {
			if(method_exists(self::getClassName(), self::getMethodName())) {
				call_user_func(array(self::getClassName(), self::getMethodName()));
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
		if(self::$_controller === null) {
			self::$_controller=Request::getPost(self::CONTROLLER_NAME, Request::getQuery(self::CONTROLLER_NAME, false));
		}
		
		return self::$_controller;
	}
	
	/**
	 * Получить текущий ajax-контроллер
	 * @return string|false
	 */
	public static function getAction()
	{
		if(self::$_action === null) {
			self::$_action=Request::getPost(self::ACTION_NAME, Request::getQuery(self::ACTION_NAME, false));
		}
		
		return self::$_action;
	}

	/**
	 * Запрос является ajax-запросом
	 * @return boolean
	 */
	public static function isAjax()
	{
		return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && self::getController() && self::getAction());
	}
	
	/**
	 * Проверить сущестовавания файла контроллера и подключить
	 * @return boolean файл контроллера найден
	 */
	public static function isLoaded()
	{
		$filename = dirname(__FILE__) . '/ajax/' . strtolower(self::getController()) . '.php';
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
		return '\kontur\ajax\\' . ucfirst(strtolower(self::getController()));
	}
	
	/**
	 * Получить имя метода
	 * @return string
	 */
	public static function getMethodName()
	{
		return strtolower(self::getAction());
	}
}
