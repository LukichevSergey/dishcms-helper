<?php
namespace kontur\ajax;

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use kontur\Request;

Loader::includeModule("sale");

class Locations
{
	/**
	 * Получить торговое предложение
	 */
	public static function getLocationIdByCode()
	{
		$response=array();
		
		if($locationCode=Request::getPost('code')) {
   			$location = \Bitrix\Sale\Location\LocationTable::getByCode($locationCode, array(
			    'filter' => array('=NAME.LANGUAGE_ID' => LANGUAGE_ID),
			    'select' => array('*', 'NAME_RU' => 'NAME.NAME')
			))->fetch();
			$response=array('location'=>$location);
		}
		
		static::sendResponse($response);
	}
	
	/**
	 * Отправить ответ
	 * @param array $response массив ответа
	 */
	protected static function sendResponse($response=array())
	{
		$response=array_merge(array(
			'success'=>true
		), $response);
		
		echo json_encode($response);
	}
}
