<?php
/**
 * Помощник для модуля ПЭК
 */
namespace pecom\components\helpers;

use common\components\helpers\HArray as A;
use settings\components\helpers\HSettings;
use pecom\components\PecomApi;

class HPecom
{
    private static $settings;
    
    public static function settings()
    {
        if(!static::$settings) {
            static::$settings=HSettings::getById('shop');
        }
        return static::$settings;
    }
    
    public static function calc($deliveryCity, &$isOversized, &$params)
    {
        $params=[
            'deliver'=>[
                'town'=>$deliveryCity
            ],
            'take'=>[
                'town'=>static::settings()->pecom_take_town
            ],
            'plombir'=>0,
            'places'=>[]
        ];
        
        // параметры груза, по очереди
        // Ширина, Длина, Высота, Объем, Вес, Признак негабаритности груза, Признак ЖУ
        $totalWeight=0;
        $isOversized=false;
        foreach(\Yii::app()->cart->getData() as $item) {
            $width=(float)sprintf('%1.2f', A::rget($item, 'attributes.width', 0)/100);
            $height=(float)sprintf('%1.2f', A::rget($item, 'attributes.height', 0)/100);
            $length=(float)sprintf('%1.2f', A::rget($item, 'attributes.length', 0)/100);
            if(($width>0) && ($height>0) && ($length>0)) {
                $volume=sprintf('%.6f', $width * $height * $length);
            }
            else {
                $volume=sprintf('%.6f', A::rget($item, 'attributes.volume', 1));
            }

            $weight=(float)A::rget($item, 'attributes.weight', 0);
            $totalWeight+=$weight;

            if(!$weight || !$volume) {
                return false;
            }
            
            for($i=1; $i<=(int)$item['count']; $i++) {
                if($oversized=(int)A::rget($item, 'attributes.is_oversized', 0)) {
                    $isOversized=true;
                }
                
                $params['places'][]=[
                    $width,
                    $height,
                    $length,
                    $volume,
                    $weight,
                    $oversized,
                    0 //(int)A::rget($item, 'attributes.is_rigid', 0),
                ];
            }
        }
        
        if($totalWeight <= 3) {
            $params['plombir']=1;
        }

        return (array)PecomApi::i()->calc($params);
    }
    
    public static function getCalcTotalPrice($result, $isOversized)
    {
        $totalPrice=0;
        // ADD - дополнительные услуги (например, изготовление жесткой упаковки) 
        // ADD_1 - услуга пломбирования груза 
        // ADD_2 - услуга запаллечивания груза 
        // ADD_3 - услуга страхования груза 
        // ADD_4 - услуга доставки в ночное время 
        // deliver - данные по доставке 
        // take - данные по забору 
        // auto - данные по автоперевозке без учета наценки за негабарит 
        // autonegabarit - данные по автоперевозке с учетом наценки за негабарит, если указан "Признак негабаритности" 
        // хотя бы для одного из мест (иначе совпадает со значением auto)
        // alma_auto - только, если филиал-отправитель или филиал-получатель город Алматы, перевозка идет через Екатеринбург,
        // при этом в auto передаются данные по автоперевозке от места отправки до Екатеринбурга, в alma_auto - от Ектеринбурга
        // до места названчения 
        // avia - данные по авиаперевозке 
        $priceFields=[
            'ADD', 
            'ADD_1', 
            'ADD_2', 
            'ADD_3', 
            'ADD_4', 
            //'deliver', 
            //'take', 
            'avia'
        ];
        if($isOversized) {
            $priceFields[]='autonegabarit';
        }
        else {
            $priceFields[]='auto';
            $priceFields[]='alma_auto';
        }
        foreach($priceFields as $paramName) {
            if($priceData=A::get($result, $paramName, 0)) {
                $totalPrice+=(float)A::get($priceData, 2, 0);
            }
        }
        
        return $totalPrice;
    }
    
    public static function getCalcPriceData($result, $isOversized, $skipEmpty=true)
    {
        // ADD - дополнительные услуги (например, изготовление жесткой упаковки) 
        // ADD_1 - услуга пломбирования груза 
        // ADD_2 - услуга запаллечивания груза 
        // ADD_3 - услуга страхования груза 
        // ADD_4 - услуга доставки в ночное время 
        // deliver - данные по доставке 
        // take - данные по забору 
        // auto - данные по автоперевозке без учета наценки за негабарит 
        // autonegabarit - данные по автоперевозке с учетом наценки за негабарит, если указан "Признак негабаритности" 
        // хотя бы для одного из мест (иначе совпадает со значением auto)
        // alma_auto - только, если филиал-отправитель или филиал-получатель город Алматы, перевозка идет через Екатеринбург,
        // при этом в auto передаются данные по автоперевозке от места отправки до Екатеринбурга, в alma_auto - от Ектеринбурга
        // до места названчения 
        // avia - данные по авиаперевозке 
        $priceFields=[
            'ADD'=>'Дополнительные услуг', 
            'ADD_1'=>'Услуга пломбирования груза', 
            'ADD_2'=>'Услуга запаллечивания груза ', 
            'ADD_3'=>'Услуга страхования груза ', 
            'ADD_4'=>'Услуга доставки в ночное время', 
            //'deliver'=>'Данные по доставке', 
            //'take'=>'Данные по забору', 
            'avia'=>'Данные по авиаперевозке'
        ];
        if($isOversized) {
            $priceFields['auto']='Данные по автоперевозке без учета наценки за негабарит';
            $priceFields['alma_auto']='Перевозка идет через Екатеринбург';
            //$priceFields['autonegabarit']='Данные по автоперевозке с учетом наценки за негабарит';
        }
        else {
            $priceFields['auto']='Данные по автоперевозке без учета наценки за негабарит';
            $priceFields['alma_auto']='Перевозка идет через Екатеринбург';
        }
        
        $data=[];
        foreach($priceFields as $name=>$label) {
            if($priceData=A::get($result, $name, 0)) {
                if($skipEmpty && !(float)A::get($priceData, 2, 0)) continue;
                $data[$name]=[
                    'label'=>$label,
                    'price'=>(float)A::get($priceData, 2, 0),
                    'note'=>A::get($priceData, 1, '')
                ];
            }
        }
        
        return $data;
    }
    
    public static function getDeliveryInfo($deliveryCity, $encode=false)
    {
        $result=HPecom::calc($deliveryCity, $isOversized, $params);
        $data=[
            'result'=>$result,
            'isOversized'=>$isOversized,
            'params'=>$params
        ];
        
        if($encode) {
            return json_encode($data, JSON_UNESCAPED_UNICODE);
        }
        return $data;
    }
    
    public static function getCityName($deliveryCity)
    {
        $deliveryCity=(int)$deliveryCity;
        
        $towns=PecomApi::i()->towns();
        foreach($towns as $townId=>$townTitle) {
            if(is_array($townTitle)) {
                foreach($townTitle as $id=>$title) {
                    if((int)$id===$deliveryCity) {
                        return $title;
                    }
                }
            }
            elseif(is_numeric($townId) && ((int)$townId===$deliveryCity)) {
                return $townTitle;
            }
        }
        
        return false;
    }
}
