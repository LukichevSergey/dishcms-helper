<?php
namespace ecommerce\ext\mobidel\components\helpers;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;

class HMobidel
{
	public static $debug = false;
	
	public static function config($param, $default)
	{
		return Y::param('ecommerce.ext.mobidel.'.$param, $default);
	}
	
	/**
	 * Отправка заказа в сервис Mobidel
	 * @param $order \DOrder\models\DOrder объект заказа
	 */
	public static function sendOrder($order)
	{
		$url = 'http://online.mobidel.ru/makeOrder.php?';
		
		$requestData=[
			'user'=>static::config('user'),
			'password'=>static::config('password'),
			'wid'=>static::config('wid'),
			'webID'=>$order->id,
			'articles'=>[],
			'quantities'=>[]
		];
		
		$customer = $order->getCustomerData();
		$requestData['family'] = A::rget($customer, 'name.value');
		$requestData['phone'] = preg_replace(['/[^0-9]/', '/^(7|8)/'], '', A::rget($customer, 'phone.value'));
		$requestData['comment'] = A::rget($customer, 'comment.value');
		$requestData['street'] = A::rget($customer, 'delivery_street.value');
		$requestData['home'] = A::rget($customer, 'delivery_home.value');
		$requestData['room'] = A::rget($customer, 'delivery_room.value');
		$requestData['building'] = A::rget($customer, 'delivery_building.value');
		$requestData['entrance'] = A::rget($customer, 'delivery_entrance.value');
		$requestData['floor'] = A::rget($customer, 'delivery_floor.value');
		
		$orderItems = $order->getOrderData();
		foreach($orderItems as $item) {
			$requestData['articles'][]=$item['code']['value'];
			$requestData['quantities'][]=$item['count']['value'];
		}
		
		$url .= http_build_query($requestData);
		
		static::log($url);
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		static::log(curl_exec($ch));
		curl_close($ch);
	}
	
	public static function log()
	{
		if(static::$debug) {
			file_put_contents(dirname(__FILE__).'/hmobidel.log', date('[d.m.Y H:i:s]') . "\n" . var_export(func_get_args(), true) . "\n\n", FILE_APPEND);
		}
	}
}