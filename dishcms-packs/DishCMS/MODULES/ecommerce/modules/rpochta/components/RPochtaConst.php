<?php
/**
 * Константы API модуля Почта.России
 * 
 */
namespace rpochta\components;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HRequest as R;

class RPochtaConst
{
    use \common\traits\Singleton;
    
    /**
     * Типы доставки
     */
    const MODE_OPS='ops';
    const MODE_ADDRESS='address';
    
    /**
     * Типы платежей
     */
    const PAYMENT_TYPE_CASHLESS='CASHLESS';
    const PAYMENT_TYPE_STAMP='STAMP';
    const PAYMENT_TYPE_FRANKING='FRANKING';
    
    /**
     * Категория РПО
     */
    const RPO_CATEGORY_SIMPLE='SIMPLE';
    const RPO_CATEGORY_ORDERED='ORDERED';
    const RPO_CATEGORY_ORDINARY='ORDINARY';
    const RPO_CATEGORY_WITH_DECLARED_VALUE='WITH_DECLARED_VALUE';
    const RPO_CATEGORY_WITH_DECLARED_VALUE_AND_CASH_ON_DELIVERY='WITH_DECLARED_VALUE_AND_CASH_ON_DELIVERY';
    
    /**
     * Вид РПО
     */
    const RPO_TYPE_POSTAL_PARCEL='POSTAL_PARCEL';
    const RPO_TYPE_ONLINE_PARCEL='ONLINE_PARCEL';
    const RPO_TYPE_ONLINE_COURIER='ONLINE_COURIER';
    const RPO_TYPE_EMS='EMS';
    const RPO_TYPE_EMS_OPTIMAL='EMS_OPTIMAL';
    const RPO_TYPE_LETTER='LETTER';
    const RPO_TYPE_BANDEROL='BANDEROL';
    const RPO_TYPE_BUSINESS_COURIER='BUSINESS_COURIER';
    const RPO_TYPE_BUSINESS_COURIER_ES='BUSINESS_COURIER_ES';
    const RPO_TYPE_PARCEL_CLASS_1='PARCEL_CLASS_1';
    
    /**
     * Тип адреса
     */
    const ADDRESS_TYPE_DEFAULT='DEFAULT';
    const ADDRESS_TYPE_PO_BOX='PO_BOX';
    const ADDRESS_TYPE_DEMAND='DEMAND';
    
    /**
     * Код качества нормализации адреса
     */
    const ADDRESS_VALIDATE_CODE_GOOD='GOOD';
    const ADDRESS_VALIDATE_CODE_ON_DEMAND='ON_DEMAND';
    const ADDRESS_VALIDATE_CODE_POSTAL_BOX='POSTAL_BOX';
    const ADDRESS_VALIDATE_CODE_UNDEF_01='UNDEF_01';
    const ADDRESS_VALIDATE_CODE_UNDEF_02='UNDEF_02';
    const ADDRESS_VALIDATE_CODE_UNDEF_03='UNDEF_03';
    const ADDRESS_VALIDATE_CODE_UNDEF_04='UNDEF_04';
    const ADDRESS_VALIDATE_CODE_UNDEF_05='UNDEF_05';
    const ADDRESS_VALIDATE_CODE_UNDEF_06='UNDEF_06';
    const ADDRESS_VALIDATE_CODE_UNDEF_07='UNDEF_07';
    
    /**
     * Код проверки нормализации адреса
     */
    const ADDRESS_CONFIRMED_CODE_CONFIRMED_MANUALLY='CONFIRMED_MANUALLY';
    const ADDRESS_CONFIRMED_CODE_VALIDATED='VALIDATED';
    const ADDRESS_CONFIRMED_CODE_OVERRIDDEN='OVERRIDDEN';
    const ADDRESS_CONFIRMED_CODE_NOT_VALIDATED_HAS_UNPARSED_PARTS='NOT_VALIDATED_HAS_UNPARSED_PARTS';
    const ADDRESS_CONFIRMED_CODE_NOT_VALIDATED_HAS_ASSUMPTION='NOT_VALIDATED_HAS_ASSUMPTION';
    const ADDRESS_CONFIRMED_CODE_NOT_VALIDATED_HAS_NO_MAIN_POINTS='NOT_VALIDATED_HAS_NO_MAIN_POINTS';
    const ADDRESS_CONFIRMED_CODE_NOT_VALIDATED_HAS_NUMBER_STREET_ASSUMPTION='NOT_VALIDATED_HAS_NUMBER_STREET_ASSUMPTION';
    const ADDRESS_CONFIRMED_CODE_NOT_VALIDATED_HAS_NO_KLADR_RECORD='NOT_VALIDATED_HAS_NO_KLADR_RECORD';
    const ADDRESS_CONFIRMED_CODE_NOT_VALIDATED_HOUSE_WITHOUT_STREET_OR_NP='NOT_VALIDATED_HOUSE_WITHOUT_STREET_OR_NP';
    const ADDRESS_CONFIRMED_CODE_NOT_VALIDATED_HOUSE_EXTENSION_WITHOUT_HOUSE='NOT_VALIDATED_HOUSE_EXTENSION_WITHOUT_HOUSE';
    const ADDRESS_CONFIRMED_CODE_NOT_VALIDATED_HAS_AMBI='NOT_VALIDATED_HAS_AMBI';
    const ADDRESS_CONFIRMED_CODE_NOT_VALIDATED_EXCEDED_HOUSE_NUMBER='NOT_VALIDATED_EXCEDED_HOUSE_NUMBER';
    const ADDRESS_CONFIRMED_CODE_NOT_VALIDATED_INCORRECT_HOUSE='NOT_VALIDATED_INCORRECT_HOUSE';
    const ADDRESS_CONFIRMED_CODE_NOT_VALIDATED_INCORRECT_HOUSE_EXTENSION='NOT_VALIDATED_INCORRECT_HOUSE_EXTENSION';
    const ADDRESS_CONFIRMED_CODE_NOT_VALIDATED_FOREIGN='NOT_VALIDATED_FOREIGN';
    const ADDRESS_CONFIRMED_CODE_NOT_VALIDATED_DICTIONARY='NOT_VALIDATED_DICTIONARY';
   
    
    public function modeLabels($mode=false)
    {
        $modes=[
            self::MODE_OPS=>'До отделения Почты России (ОПС)',
            self::MODE_ADDRESS=>'До адреса покупателя',
        ];
        
        return $mode ? A::get($modes, $mode) : $modes;
    }
    
    public function paymentTypeLabels($type=false)
    {
        $types=[
            self::PAYMENT_TYPE_CASHLESS=>'Безналичный расчет',
            self::PAYMENT_TYPE_STAMP=>'Оплата марками',
            self::PAYMENT_TYPE_FRANKING=>'Франкирование'
        ];
        
        return $type ? A::get($types, $type) : $types;
    }
    
    public function rpoCategoryLabels($category=false)
    {
        $categories=[
            self::RPO_CATEGORY_SIMPLE=>'Простое',
            self::RPO_CATEGORY_ORDERED=>'Заказное',
            self::RPO_CATEGORY_ORDINARY=>'Обыкновенное',
            self::RPO_CATEGORY_WITH_DECLARED_VALUE=>'С объявленной ценностью',
            self::RPO_CATEGORY_WITH_DECLARED_VALUE_AND_CASH_ON_DELIVERY=>'С объявленной ценностью и наложенным платежом'
        ];
        
        return $category ? A::get($categories, $category) : $categories;
    }
    
    public function rpoTypeLabels($type=false)
    {
        $types=[
            self::RPO_TYPE_POSTAL_PARCEL=>'Обычная',
            //self::RPO_TYPE_POSTAL_PARCEL=>'Посылка "нестандартная"',
            self::RPO_TYPE_ONLINE_PARCEL=>'Посылка "онлайн"',
            self::RPO_TYPE_ONLINE_COURIER=>'Курьер "онлайн"',
            self::RPO_TYPE_EMS=>'Экспресс (ЕМS)',
            //self::RPO_TYPE_EMS=>'Отправление EMS',
            self::RPO_TYPE_EMS_OPTIMAL=>'EMS оптимальное',
            self::RPO_TYPE_LETTER=>'Письмо',
            self::RPO_TYPE_BANDEROL=>'Бандероль',
            self::RPO_TYPE_BUSINESS_COURIER=>'Бизнес курьер',
            self::RPO_TYPE_BUSINESS_COURIER_ES=>'Бизнес курьер экпресс',
            self::RPO_TYPE_PARCEL_CLASS_1=>'Посылка 1-го класса'
        ];
        
        return $type ? A::get($types, $type) : $types;
    }    
    
    public function addressTypeLabels($type=false)
    {
        $types=[
            self::ADDRESS_TYPE_DEFAULT=>'Стандартный (улица, дом, квартира)',
            self::ADDRESS_TYPE_PO_BOX=>'Абонентский ящик',
            self::ADDRESS_TYPE_DEMAND=>'До востребования',
        ];
        
        return $type ? A::get($types, $type) : $types;
    }    
    
    public function addressValidateCodeLabels($code=false)
    {
        $codes=[
            self::ADDRESS_VALIDATE_CODE_GOOD=>'Пригоден для почтовой рассылки',
            self::ADDRESS_VALIDATE_CODE_ON_DEMAND=>'До востребования',
            self::ADDRESS_VALIDATE_CODE_POSTAL_BOX=>'Абонентский ящик',
            self::ADDRESS_VALIDATE_CODE_UNDEF_01=>'Не определен регион',
            self::ADDRESS_VALIDATE_CODE_UNDEF_02=>'Не определен город или населенный пункт',
            self::ADDRESS_VALIDATE_CODE_UNDEF_03=>'Не определена улица',
            self::ADDRESS_VALIDATE_CODE_UNDEF_04=>'Не определен номер дома',
            self::ADDRESS_VALIDATE_CODE_UNDEF_05=>'Не определена квартира/офис',
            self::ADDRESS_VALIDATE_CODE_UNDEF_06=>'Не определен',
            self::ADDRESS_VALIDATE_CODE_UNDEF_07=>'Иностранный адрес'
        ];
        
        return $code ? A::get($codes, $code) : $codes;
    }
    
    public function addressConfirmedCodeLabels($code=false)
    {
        $codes=[
            self::ADDRESS_CONFIRMED_CODE_CONFIRMED_MANUALLY=>'Подтверждено контролером',
            self::ADDRESS_CONFIRMED_CODE_VALIDATED=>'Уверенное распознавание',
            self::ADDRESS_CONFIRMED_CODE_OVERRIDDEN=>'Распознан: адрес был перезаписан в справочнике',
            self::ADDRESS_CONFIRMED_CODE_NOT_VALIDATED_HAS_UNPARSED_PARTS=>'На проверку, неразобранные части',
            self::ADDRESS_CONFIRMED_CODE_NOT_VALIDATED_HAS_ASSUMPTION=>'На проверку, предположение',
            self::ADDRESS_CONFIRMED_CODE_NOT_VALIDATED_HAS_NO_MAIN_POINTS=>'На проверку, нет основных частей',
            self::ADDRESS_CONFIRMED_CODE_NOT_VALIDATED_HAS_NUMBER_STREET_ASSUMPTION=>'На проверку, предположение по улице',
            self::ADDRESS_CONFIRMED_CODE_NOT_VALIDATED_HAS_NO_KLADR_RECORD=>'На проверку, нет в КЛАДР',
            self::ADDRESS_CONFIRMED_CODE_NOT_VALIDATED_HOUSE_WITHOUT_STREET_OR_NP=>'На проверку, нет улицы или населенного пункта',
            self::ADDRESS_CONFIRMED_CODE_NOT_VALIDATED_HOUSE_EXTENSION_WITHOUT_HOUSE=>'На проверку, нет дома',
            self::ADDRESS_CONFIRMED_CODE_NOT_VALIDATED_HAS_AMBI=>'На проверку, неоднозначность',
            self::ADDRESS_CONFIRMED_CODE_NOT_VALIDATED_EXCEDED_HOUSE_NUMBER=>'На проверку, большой номер дома',
            self::ADDRESS_CONFIRMED_CODE_NOT_VALIDATED_INCORRECT_HOUSE=>'На проверку, некорректный дом',
            self::ADDRESS_CONFIRMED_CODE_NOT_VALIDATED_INCORRECT_HOUSE_EXTENSION=>'На проверку, некорректное расширение дома',
            self::ADDRESS_CONFIRMED_CODE_NOT_VALIDATED_FOREIGN=>'Иностранный адрес',
            self::ADDRESS_CONFIRMED_CODE_NOT_VALIDATED_DICTIONARY=>'На проверку, не по справочнику'
        ];
        
        return $code ? A::get($codes, $code) : $codes;
    }
    
    public function calcResultFields()
    {
        return [
            'avia-rate'=>'Плата за Авиа-пересылку',
            'ground-rate'=>'Плата за пересылку',
            'notice-rate'=>'Надбавка за уведомление о вручении',
            'fragile-rate'=>'Надбавка за отметку "Осторожно/Хрупкое"',
            'insurance-rate'=>'Плата за объявленную ценность',
            'oversize-rate'=>'Надбавка за негабарит при весе более 10кг'
        ];
    }
    
    public function addressResultFields()
    {
        return [
            'index'=>'Почтовый индекс',
            'region'=>'Область, регион',
            'place'=>'Населенный пункт',
            'area'=>'Район',
            'location'=>'Микрорайон',
            'hotel'=>'Название гостиницы',
            'street'=>'Улица',
            'house'=>'Номер здания',
            'letter'=>'Литера',
            'slash'=>'Дробь',
            'building'=>'Строение',
            'corpus'=>'Корпус',
            'room'=>'Номер помещения',
            'num-address-type'=>'Номер для а/я, войсковая часть, войсковая часть ЮЯ, полевая почта',
            //'address-type'=>'Тип адреса',
            //'id'=>'Идентификатор записи',
            //'original-address'=>'Оригинальные адрес одной строкой',
            //'quality-code'=>'Код качества нормализации адреса',
            //'validation-code'=>'Код проверки нормализации адреса'
        ];
    }
}
