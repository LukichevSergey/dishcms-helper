<?php
namespace extend\modules\slider\components\helpers;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HModel;
use common\components\helpers\HCache;

class HSlider
{
    public static function widget($code, $properties=[], $captureOutput=false)
    {
        if($slider=static::getSlider($code, ['scopes'=>['activly', 'utcache'=>HCache::MINUTE]])) {
            $widgetClass=$slider->getOption('widget');
            if(strpos($widgetClass, '\\') === false) {
                $widgetClass='\extend\modules\slider\widgets\\' . $widgetClass;
            }
            
            if($config=$slider->getOption('config')) {
                $properties['config']=$config;
            }
            
            if(class_exists($widgetClass)) {
				$properties=A::m(['sortKey'=>$slider->id], $properties);
                return Y::controller()->widget($widgetClass, A::m($properties, ['code'=>$code]), $captureOutput);
            }
        }
    }
    
    /**
     * Получить объект слайдера
     * @param string $code код слайдера
     * @param \CDbCriteria|string|array|null $criteria условия выборки.
     * @return \extend\modules\slider\models\Slider
     */
    public static function getSlider($code, $criteria=null)
    {
        return HModel::loadByColumn('\extend\modules\slider\models\Slider', ['code'=>$code], $criteria);
    }
}
