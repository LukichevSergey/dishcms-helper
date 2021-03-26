<?php
namespace extend\modules\points\components\helpers;

use settings\components\helpers\HSettings;

/**
 * Класс-помощник модуля "Точки продаж"
 *
 */
class HPoint
{
    /**
     * Получить объект настроек модуля
     * @return \accounts\models\AccountSettings
     */
    public static function settings()
    {
        return HSettings::getById('points');
    }
    
    /**
     * Получить параметры метки для карты по умолчанию
     * @return array
     */
    public static function getPlacemarkOptions()
    {
        $placemarkOptions=[];
        
        if(static::settings()->placemarkIconBehavior->exists()) {
            $placemarkOptions['iconLayout']='default#image';
            $placemarkOptions['iconImageHref']=static::settings()->placemarkIconBehavior->getSrc();
        }
        else {
            $placemarkOptions['preset']='islands#nightHomeIcon';
        }
            
        return $placemarkOptions;
    }
}
