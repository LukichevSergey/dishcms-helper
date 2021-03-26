<?php
namespace extend\modules\bitrix24\components\helpers;

use common\components\helpers\HYii as Y;

class HBitrix24
{
	/**
	 * Получение URL для запроса
	 */
	public static function getUrl($action=null)
	{
		// https://kontur.bitrix24.ru/rest/1/isokb839y5p4tand/profile/
		return rtrim(Y::param('bitrix24.url'), '/') 
			. '/rest/' . Y::param('bitrix24.user_id') 
			. '/' . Y::param('bitrix24.webhook') 
			. ($action ? "/{$action}/" : '');
	}
	
	/**
	 * Нормализация данных
	 */
	public static function normalizeFields($fields)
	{
		$multiFields=[
			'EMAIL'=>['WORK', 'HOME', 'OTHER'],
			'PHONE'=>['MOBILE', 'OTHER', 'PAGER', 'HOME', 'FAX', 'WORK'],
			'IM'=>['OTHER', 'JABBER', 'MSN', 'ICQ', 'SKYPE'], 
			'WEB'=>['OTHER', 'TWITTER', 'LIVEJOURNAL', 'FACEBOOK', 'HOME', 'WORK']
		];
		
		$_fields=$fields;
		foreach($fields as $field=>$value) {
			if(strpos($field, '_')) {
				$parts=explode('_', $field);
				if(count($parts) > 1) {
					$mCode=$parts[0];
					$mSub=$parts[1];
					if(isset($multiFields[$mCode]) && in_array($mSub, $multiFields[$mCode])) {
						if(!isset($_fields[$mCode]) || !is_array($_fields[$mCode])) { 
							$_fields[$mCode]=[];
						}
						$n='n' . count($_fields[$mCode]);
						$_fields[$mCode][$n]=[
							'VALUE'=>$value,
							'VALUE_TYPE'=>$mSub
						];
						unset($_fields[$field]);
					}
				}
			}
		}
		
		return $_fields;
	}
	
	/**
	 * Получение списка доступных полей для ЛИДа
	 */
	public static function getLidFields()
	{
		return static::send(static::getUrl('crm.lead.fields.json'));
	}
	
	/**
	 * Создание лида
	 * 
	 * @link https://blog.budagov.ru/bitrix24-sozdanie-lida-cherez-api/
	 * @param [] $data данные пользователя.
	 */
	public static function createLid($fields)
	{
		return static::send(static::getUrl('crm.lead.add.json'), [
			'fields'=>static::normalizeFields($fields),
			'params'=>['REGISTER_SONET_EVENT'=>'Y']
		]);
	}
	
	/**
	 * Отправка запроса
	 */
	protected static function send($url, $data=[])
	{
		$ch=curl_init();
		
		curl_setopt_array($ch, [
			CURLOPT_URL => $url,
			CURLOPT_SSL_VERIFYPEER => 0,
			CURLOPT_HEADER => 0,
			CURLOPT_RETURNTRANSFER => 1
		]);
		
		if(!empty($data)) {
			curl_setopt_array($ch, [
				CURLOPT_POST => 1,
				CURLOPT_POSTFIELDS => http_build_query($data),
			]);
		}
		
		$result=curl_exec($ch);
		curl_close($ch);
		
		return @json_decode($result, true);
	}
}