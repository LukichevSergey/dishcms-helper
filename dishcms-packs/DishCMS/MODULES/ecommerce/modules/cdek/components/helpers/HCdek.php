<?php
/**
 * Помощник для модуля СДЭК 
 */
namespace cdek\components\helpers;

use common\components\helpers\HTools;
use settings\components\helpers\HSettings;
use cdek\models\Order;

class HCdek
{
    private static $settings;
    
    public static function settings()
    {
        if(!static::$settings) {
            static::$settings=HSettings::getById('shop');
        }
        return static::$settings;
    }
    
    public static function normalizePrice($price)
    {
        if(static::settings()->cdek_extra_charge) {
            return HTools::incByPersent($price, HCdek::settings()->cdek_extra_charge);
        }
        
        return $price;
    }
    
    public static function normalizePhone($phone)
    {
        return preg_replace('/[^+0-9]+/', '', $phone);
    }
    
    /**
     * Получить объемный вес по значению объема товара по формуле СДЭК.
     * @param float $volume объем в м3
     * @return float вес в кг
     */
    public static function toWeight($volume)
    {
        // 1м3 весит 200кг
        return $volume * 200;
    }
    
    /**
     * Получить объем товара по его весу по формуле СДЭК
     * @param float $weight вес в кг
     * @return float объем в м3
     */
    public static function toVolume($weight)
    {
        // 1м3 весит 200кг
        return $weight / 200;
    }
    
    /**
     * Нормализация веса и объема по формуле СДЭК.
     * Приоритет у веса товара.
     * Если не определен ни вес, ни объем, возвращается рассчет для 1кг.
     * @param float &$weight вес в кг
     * @param float &$volume объем в м3
     */
    public static function normalizeSize(&$weight, &$volume)
    {
        $weight=(float)$weight;
        $volume=(float)$volume;
        
        if($weight) {
            $volume=static::toVolume($weight);
        }
        elseif($volume) {
            $weight=static::toWeight($volume);
        }
        else {
            $weight=1;
            $volume=static::toVolume($weight);
        }
        
        $weight=static::round_out($weight, 2);
        $volume=static::round_out($volume, 2);
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
            Order::STATUS_CDEK=>$prefix.'-info',
            Order::STATUS_CLOSE=>$prefix.'-success',
            Order::STATUS_REJECT=>$prefix.'-danger',
            
            Order::STATUS_CDEK_ERROR=>$prefix.'-danger',
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
