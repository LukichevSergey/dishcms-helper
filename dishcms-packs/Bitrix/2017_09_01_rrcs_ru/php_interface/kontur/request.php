<?php
namespace kontur;

use Bitrix\Main\Application;

class Request
{
	public static function getRequest()
	{
		return Application::getInstance()->getContext()->getRequest(); 
	}
	
	public static function getPost($name, $default=null)
	{
		return self::getRequest()->getPost($name) ?: $default;
	}
	
	public static function getQuery($name, $default=null)
	{
		return self::getRequest()->getQuery($name) ?: $default;
	}
}