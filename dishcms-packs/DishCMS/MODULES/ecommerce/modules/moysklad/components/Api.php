<?php
namespace ecommerce\modules\moysklad\components;

/**
 * API Мой Склад
 *
 */
class Api
{
    use \common\traits\Singleton;
    
    /**
     * Базовый URL для запросов 
     * @var string
     */
    const ENDPOINT='https://online.moysklad.ru/api/remap/1.1';
    
    public function get($url)
    {
        
    }
    
    public function curl($url, $data=[], $options=[])
    {
        $ch=curl_init(self::ENDPOINT . '/' . trim($url, '/'));
        
        curl_setopt($ch, CURLOPT_HEADER, [
            'Authorization: ' . base64_encode("login:password")
        ]);
        
        if(!empty($options)) {
            curl_setopt_array($ch, $options);
        }
        
        if(!empty($data)) {
            
        }
    }
}