<?php
namespace kontur\ajax;

use kontur\Ajax;

class Changelocation
{
	// subdomain=>parent-location-code
	protected static $sub = [
		'ekb'=>'0000028083'
	];
	
	public static function geturl()
	{
		\Bitrix\Main\Loader::includeModule('sale');

		$response=array(
			'url'=>static::getAbsoluteUrl(), 
			'changed'=>($_SERVER['HTTP_HOST'] != static::getBaseDomain())
		);
		
		if($locationId=Ajax::getPost('id')) {
			foreach(static::$sub as $code=>$parentLocationCode) {
				if (static::isLocation($locationId, $parentLocationCode)) {
					$response['url'] = static::getAbsoluteUrl($code);
					$response['changed'] = ($_SERVER['HTTP_HOST'] != ($code . '.' . static::getBaseDomain()));
					break;
				}
			}
		}
		
		Ajax::sendResponse($response);
	}
	
	protected static function isLocation($id, $code)
	{
		$res = \Bitrix\Sale\Location\LocationTable::getList(array(
		    'filter' => array(
		        '=ID' => $id, 
		        '=PARENTS.NAME.LANGUAGE_ID' => LANGUAGE_ID,
		        '=PARENTS.TYPE.NAME.LANGUAGE_ID' => LANGUAGE_ID,
		    ),
		    'select' => array(
		        'I_ID' => 'PARENTS.ID',
		        'I_CODE' => 'PARENTS.CODE',
		        'I_NAME_RU' => 'PARENTS.NAME.NAME',
		        'I_TYPE_CODE' => 'PARENTS.TYPE.CODE',
		        'I_TYPE_NAME_RU' => 'PARENTS.TYPE.NAME.NAME'
		    ),
		    'order' => array(
		        'PARENTS.DEPTH_LEVEL' => 'asc'
		    )
		));
		
		while($item = $res->fetch()) {
		    if($item['I_CODE'] == $code) {
		    	return true;
		    }
		}
		
		return false;
	}
	
	protected static function getBaseDomain()
	{
		return preg_replace('/^(' . implode('|', array_keys(static::$sub)) . ')\.(.*)$/', '\\2', $_SERVER["HTTP_HOST"]);
	}
	
	protected static function getAbsoluteUrl($code=null)
	{
		return ((\CMain::IsHTTPS()) ? "https://" : "http://") . ($code ? "{$code}." : '') . static::getBaseDomain();
	}
}
