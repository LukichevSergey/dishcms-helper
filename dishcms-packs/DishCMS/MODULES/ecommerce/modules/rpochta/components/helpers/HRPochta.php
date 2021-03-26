<?php
/**
 * Помощник для модуля Почта.России 
 */
namespace rpochta\components\helpers;

use common\components\helpers\HTools;
use common\components\helpers\HHtml;
use settings\components\helpers\HSettings;
use rpochta\components\helpers\HRPochta;
use rpochta\components\RPochtaConst;
use rpochta\components\RPochtaApi;
use rpochta\models\Order;

class HRPochta
{
    private static $settings;
    
    public static function settings()
    {
        if(!static::$settings) {
            static::$settings=HSettings::getById('shop');
        }
        return static::$settings;
    }
    
    /**
     * Получить индекс города-отправителя
     */
    public static function indexFrom()
    {
        $postcode=static::settings()->rpochta_index_from;
        
        if(!$postcode) {
            $postcode=Y::param('rpochta.index_from');
        }
        
        return static::normalizeIndex($postcode);
    }
    
    /**
     * Получить название города-отправителя
     */
    public static function indexFromName()
    {
        $name=static::settings()->rpochta_index_from_name;
        
        if(!$name) {
            $name=Y::param('rpochta.index_from_name');
        }
        
        return $name;
    }
    
    public static function normalizePrice($price)
    {
        if(static::settings()->rpochta_extra_charge) {
            return HTools::incByPersent($price, HRPochta::settings()->rpochta_extra_charge);
        }
        
        return $price;
    }
    
    /**
     * Перевод из копеек в рубли
     */
    public static function toRuble($price)
    {
        return (float)number_format(((float)$price / 100), 2, '.', '');
    }
    
    /**
     * Нормализация почтового индекса
     */
    public static function normalizeIndex($postIndex)
    {
        return sprintf("%'.06d", $postIndex);        
    }
    
    /**
     * Проверка того, что ответ валидации адреса доставки верный.
     */
    public static function addressValided($qualityCode, $validationCode)
    {
        return in_array($qualityCode, [ 
                RPochtaConst::ADDRESS_VALIDATE_CODE_GOOD,
                RPochtaConst::ADDRESS_VALIDATE_CODE_ON_DEMAND,
                RPochtaConst::ADDRESS_VALIDATE_CODE_POSTAL_BOX
            ])
            && in_array($validationCode, [
                RPochtaConst::ADDRESS_CONFIRMED_CODE_CONFIRMED_MANUALLY,
                RPochtaConst::ADDRESS_CONFIRMED_CODE_VALIDATED,
                RPochtaConst::ADDRESS_CONFIRMED_CODE_OVERRIDDEN
            ]);
    }
    
    /**
     * Получить данные проверки корректности адреса доставки.
     * @param string $address адрес доставки.
     * @param boolean $encode кодировать результат в JSON.
     * @param boolean $forcyReturnData в любом случае возвращать данные результата.
     * @return array|string|false|null возвращает:
     * NULL - в случае, если сервис доставки Почта.России недоступен
     * FALSE - в случае некорректного адреса доставки
     */
    public static function getAddressData($address, $encode=true, $forcyReturnData=false)
    {
        $validationResult=RPochtaApi::i()->cleanAddress(['address'=>$address]);
        
        if(isset($validationResult[0])) {
            if($forcyReturnData || static::addressValided($validationResult[0]['quality-code'], $validationResult[0]['validation-code'])) {
                if($encode) {
                    return json_encode($validationResult[0], JSON_UNESCAPED_UNICODE);
                }
                else {
                    return $validationResult[0];
                }
            }
            else {
                return false;
            }
        }
        
        return null;
    }
    
    public static function normalizePhone($phone)
    {
        return preg_replace(['/[^+0-9]+/', '/^\+7/'], ['', '8'], $phone);
    }
    
    /**
     * Нормализация веса.
     * @param float &$weight вес в кг
     */
    public static function normalizeWeight(&$weight)
    {
        $weight=(float)$weight;
        
        if(!$weight) {            
            $weight=1;
        }
        
        $weight=static::round_out($weight, 2);
    }
    
    /**
     * @link http://php.net/manual/ru/function.ceil.php#50448
     */
    protected static function round_out($value, $places=0) {
        if ($places < 0) { $places = 0; }
        $mult = pow(10, $places);
        return ($value >= 0 ? ceil($value * $mult):floor($value * $mult)) / $mult;
    }
    
    public static function getStatusCssClass($status=false, $prefix='bg')
    {
        $cssClasses=[
            Order::STATUS_WAIT=>$prefix.'-warning',
            Order::STATUS_RPOCHTA=>$prefix.'-info',
            Order::STATUS_CLOSE=>$prefix.'-success',
            Order::STATUS_REJECT=>$prefix.'-danger',
            
            Order::STATUS_RPOCHTA_ERROR=>$prefix.'-danger',
        ];
        
        if($status) {
            if(isset($cssClasses[$status])) {
                return $cssClasses[$status];
            }
            return false;
        }
        
        return $cssClasses;
    }
}
