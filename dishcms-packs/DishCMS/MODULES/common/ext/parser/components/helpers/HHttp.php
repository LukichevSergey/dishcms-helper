<?php
namespace common\ext\parser\components\helpers;

use common\components\helpers\HArray as A;

class HHttp
{
    /**
     * Информация последнего запроса
     * @var string
     */
    private static $lastInfo=null;
    
    /**
     * Текст последней ошибки
     * @var string
     */
    private static $lastError=null;
    
    /**
     * Получить информацию последнего запроса
     * @return string
     */
    public static function lastInfo()
    {
        return static::$lastInfo;
    }
    
    /**
     * Получить текст последней ошибки
     * @return string
     */
    public static function lastError()
    {
        return static::$lastError;
    }
    
    /**
     * Получение содержимого страницы.
     * @param string $url URL страницы
     * @param [] $options дополнительные параметры для инициализации соединения.
     * @return string|null содержимое страницы
     */
    public static function getContent($url, $options=[])
    {
        $ch=curl_init();
        
        curl_setopt_array($ch, A::m($options, [
            CURLOPT_URL=>$url,
            CURLOPT_SSL_VERIFYPEER=>false,
            CURLOPT_RETURNTRANSFER=>true,
        ]));
        
        $content=curl_exec($ch);
        
        static::$lastError=curl_error($ch);
        $hasError=!!static::$lastError;
        
        static::$lastInfo=curl_getinfo($ch);
        
        curl_close($ch);
        
        if($hasError) {
            return null;
        }
        
        return (string)$content;
    }
}