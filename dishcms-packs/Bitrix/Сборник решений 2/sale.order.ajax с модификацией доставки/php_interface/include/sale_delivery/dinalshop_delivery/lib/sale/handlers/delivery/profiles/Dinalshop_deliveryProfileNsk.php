<?php
namespace Sale\Handlers\Delivery\Profiles;

use Sale\Handlers\Delivery\Dinalshop_deliveryHandler;

class Dinalshop_deliveryProfileNsk extends \Bitrix\Sale\Delivery\Services\Base
{
    protected static $isProfile = true;
    protected static $parent = null;

    public function __construct(array $initParams)
    {
        parent::__construct($initParams);
        $this->parent = \Bitrix\Sale\Delivery\Services\Manager::getObjectById($this->parentId);
    }

    public static function getClassTitle()
    {
        return 'Доставка в Новосибирске';
    }

    public static function getClassDescription()
    {
        return 'Доставка в Новосибирске (без грузчиков/выгрузка с борта)';
    }

    public function getParentService()
    {
        return $this->parent;
    }

    public function isCalculatePriceImmediately()
    {
        return $this->getParentService()->isCalculatePriceImmediately();
    }

    public static function isProfile()
    {
        return self::$isProfile;
    }

    protected function getConfigStructure()
    {
        $result = array(
            "MAIN" => array(
                'TITLE' => 'Стоимость',
                'DESCRIPTION' => 'Основные настройки стоимости',
                'ITEMS' => array(
                    'MIN_PRICE' => array(
                        "TYPE" => 'NUMBER',
                        "MIN" => 0,
                        "NAME" => 'Минимальная стоимость доставки (руб)',
                        "DEFAULT" => 600
                    ),
                    'PRICE_PER_WEIGHT' => array(
                        "TYPE" => 'NUMBER',
                        "MIN" => 0,
                        "NAME" => 'Cтоимость 1 кг (руб)',
                        "DEFAULT" => 5
                    ),
                    'REMOTE_DISTRICT_PRICE' => array(
                        "TYPE" => 'NUMBER',
                        "MIN" => 0,
                        "NAME" => 'Дополнительная стоимость доставки в удаленный район (руб)',
                        "DEFAULT" => 300
                    ),
                    'BOXING_PRICE' => array(
                        "TYPE" => 'NUMBER',
                        "MIN" => 0,
                        "NAME" => 'Стоимость упаковки (руб)',
                        "DEFAULT" => 750
                    ),
                )
            ),
            "SYSTEM" => array(
                'TITLE' => 'Системные',
                'DESCRIPTION' => 'Системные настройки необходимые для корректной работы расчета доставки',
                'ITEMS' => array(
                    'DISTRICT_IBLOCK_ID' => array(
                        "TYPE" => 'NUMBER',
                        "NAME" => 'Идентификатор инфоблока районов Новосибирска',
                        "REQUIRED" => "Y"
                    ),
                )
            )
        );

        $rs=\CSalePersonType::GetList(['SORT'=>'ASC']);
        while($personType=$rs->Fetch()) {
            $title=' (' . $personType['NAME'] . ')';
            $result['SYSTEM']['ITEMS']['ORDER_PROPERTY_DESIRED_DATE_ID_' . $personType['ID']] = [
                "TYPE" => 'NUMBER',
                "NAME" => 'Идентификатор свойства заказа: Желаемая дата доставки' . $title,
                // "REQUIRED" => "Y"
            ];
            $result['SYSTEM']['ITEMS']['ORDER_PROPERTY_DISTRICT_ID_' . $personType['ID']] = [
                "TYPE" => 'NUMBER',
                "NAME" => 'Идентификатор свойства заказа: Район Новосибирска' . $title,
                // "REQUIRED" => "Y"
            ];
            $result['SYSTEM']['ITEMS']['ORDER_PROPERTY_ADDRESS_ID_' . $personType['ID']] = [
                "TYPE" => 'NUMBER',
                "NAME" => 'Идентификатор свойства заказа: Адрес доставки' . $title,
                // "REQUIRED" => "Y"
            ];
            $result['SYSTEM']['ITEMS']['ORDER_PROPERTY_IS_RECEIVER_ID_' . $personType['ID']] = [
                "TYPE" => 'NUMBER',
                "NAME" => 'Идентификатор свойства заказа: "Я не являюсь получателем"' . $title,
                // "REQUIRED" => "Y"
            ];
            $result['SYSTEM']['ITEMS']['ORDER_PROPERTY_RECEIVER_DISTRICT_ID_' . $personType['ID']] = [
                "TYPE" => 'NUMBER',
                "NAME" => 'Идентификатор свойства заказа: Район Новосибирска для получателя' . $title,
                // "REQUIRED" => "Y"
            ];
            $result['SYSTEM']['ITEMS']['ORDER_PROPERTY_RECEIVER_NAME_ID_' . $personType['ID']] = [
                "TYPE" => 'NUMBER',
                "NAME" => 'Идентификатор свойства заказа: ФИО получателя' . $title,
                // "REQUIRED" => "Y"
            ];
            $result['SYSTEM']['ITEMS']['ORDER_PROPERTY_RECEIVER_ADDRESS_ID_' . $personType['ID']] = [
                "TYPE" => 'NUMBER',
                "NAME" => 'Идентификатор свойства заказа: Адрес получателя' . $title,
                // "REQUIRED" => "Y"
            ];
            $result['SYSTEM']['ITEMS']['ORDER_PROPERTY_RECEIVER_PHONE_ID_' . $personType['ID']] = [
                "TYPE" => 'NUMBER',
                "NAME" => 'Идентификатор свойства заказа: Телефон получателя' . $title,
                // "REQUIRED" => "Y"
            ];
            $result['SYSTEM']['ITEMS']['ORDER_PROPERTY_USE_BOXING_ID_' . $personType['ID']] = [
                "TYPE" => 'NUMBER',
                "NAME" => 'Идентификатор свойства заказа: Упаковка' . $title,
                // "REQUIRED" => "Y"
            ];
        }

        return $result;
    }

    protected function getConfigSystemProperty(\Bitrix\Sale\Shipment $shipment, $code, $default=null)
    {
        $personTypeId=$shipment->getCollection()->getOrder()->getPersonTypeId();

        if(isset($this->config['SYSTEM']["{$code}_{$personTypeId}"])) {
            return $this->config['SYSTEM']["{$code}_{$personTypeId}"];
        }

        return $default;
    }

    protected function getOrderPropertyValue($propertyId, $default=null)
    {
        if(!empty($_POST['order']['ORDER_PROP_' . $propertyId])) {
            return $_POST['order']['ORDER_PROP_' . $propertyId];
        }
        elseif(!empty($_POST['ORDER_PROP_' . $propertyId])) {
            return $_POST['ORDER_PROP_' . $propertyId];
        }
        elseif(!empty($_POST['formData']['PROPERTIES'][$propertyId])) {
            return $_POST['formData']['PROPERTIES'][$propertyId];
        }
        elseif(!empty($_POST['PROPERTIES'][$propertyId])) {
            return $_POST['PROPERTIES'][$propertyId];
        }

        return null;
    }

    protected function isRemoteDisctrict(\Bitrix\Sale\Delivery\CalculationResult &$result, \Bitrix\Sale\Shipment $shipment)
    {
        $order=$shipment->getCollection()->getOrder();
        $propertyCollection = $order->getPropertyCollection();
        
        if($districtIblockId=$this->config['SYSTEM']['DISTRICT_IBLOCK_ID']) {
            $isReceiver=$this->getOrderPropertyValue($this->getConfigSystemProperty($shipment, 'ORDER_PROPERTY_IS_RECEIVER_ID'));
            if($isReceiver == 'Y') {
                $districtId=$this->getOrderPropertyValue($this->getConfigSystemProperty($shipment, 'ORDER_PROPERTY_RECEIVER_DISTRICT_ID'));
            }
            else {
                $districtId=$this->getOrderPropertyValue($this->getConfigSystemProperty($shipment, 'ORDER_PROPERTY_DISTRICT_ID'));
            }

            if($districtId) {
                $filter=['IBLOCK_ID'=>$districtIblockId];
                if(is_numeric($districtId)) {
                    $filter=['=ID'=>$districtId];
                }
                else {
                    $filter=['NAME'=>$districtId];
                }
                $rs=\CIBlockElement::GetList([], $filter, false, false, ['ID', 'IBLOCK_ID', 'PROPERTY_IS_REMOTE']);
                if($district=$rs->Fetch()) {
                    return ($district['PROPERTY_IS_REMOTE_VALUE'] == 'Y');
                }
            }
        }
        
        $result->addError(Dinalshop_deliveryHandler::addError($isReceiver ? 'Не выбран район города для получателя' : 'Не выбран район города'));

        return false;
    }

    protected function checkDesiredDate($shipment)
    {
        $desiredDate=$this->getOrderPropertyValue($this->getConfigSystemProperty($shipment, 'ORDER_PROPERTY_DESIRED_DATE_ID'));
        if(!empty($desiredDate)) {
            if(preg_match('/^\d{2}\.\d{2}\.\d{4}$/', $desiredDate)) {
                $date=\DateTime::createFromFormat('d.m.Y', $desiredDate);
                $date->setTime(0, 0, 0);
                $current=new \DateTime();
                $current->setTime(0, 0, 0);
                if($date < $current) {
                    Dinalshop_deliveryHandler::addError('Указана неверная желаемая дата доставки');
                }
            }
            else {
                Dinalshop_deliveryHandler::addError('Указана неверная желаемая дата доставки');
            }
        }         
    }

    protected function checkReceiver(\Bitrix\Sale\Shipment $shipment)
    {
        $isReceiver=$this->getOrderPropertyValue($this->getConfigSystemProperty($shipment, 'ORDER_PROPERTY_IS_RECEIVER_ID'));
        if($isReceiver == 'Y') {
            if(!trim($this->getOrderPropertyValue($this->getConfigSystemProperty($shipment, 'ORDER_PROPERTY_RECEIVER_NAME_ID')))) {
                Dinalshop_deliveryHandler::addError('Не указано ФИО получателя');
            }
            if(!trim($this->getOrderPropertyValue($this->getConfigSystemProperty($shipment, 'ORDER_PROPERTY_RECEIVER_ADDRESS_ID')))) {
                Dinalshop_deliveryHandler::addError('Не указан адрес доставки получателя');
            }
            if(!trim($this->getOrderPropertyValue($this->getConfigSystemProperty($shipment, 'ORDER_PROPERTY_RECEIVER_PHONE_ID')))) {
                Dinalshop_deliveryHandler::addError('Не указан номер телефона получателя');
            }
        }
        else {
            if(!trim($this->getOrderPropertyValue($this->getConfigSystemProperty($shipment, 'ORDER_PROPERTY_ADDRESS_ID')))) {
                Dinalshop_deliveryHandler::addError('Не указан адрес доставки');
            }
        }
    }

    protected function isUseBoxing(\Bitrix\Sale\Delivery\CalculationResult &$result, \Bitrix\Sale\Shipment $shipment)
    {
        $isUseBoxing=$this->getOrderPropertyValue($this->getConfigSystemProperty($shipment, 'ORDER_PROPERTY_USE_BOXING_ID'));

        return ($isUseBoxing == 'Y');
    }

    protected function calculateConcrete(\Bitrix\Sale\Shipment $shipment = null)
    {
        $result = new \Bitrix\Sale\Delivery\CalculationResult();

        $minPrice=roundEx($this->config['MAIN']['MIN_PRICE'], SALE_VALUE_PRECISION);
        $pricePerWeight=roundEx($this->config['MAIN']['PRICE_PER_WEIGHT'], SALE_VALUE_PRECISION);
        $remoteDistrictPrice=roundEx($this->config['MAIN']['REMOTE_DISTRICT_PRICE'], SALE_VALUE_PRECISION);
        $boxingPrice=roundEx($this->config['MAIN']['BOXING_PRICE'], SALE_VALUE_PRECISION);
        
        $products=[];
        $basketItems = $shipment->getCollection()->getOrder()->getBasket()->getBasketItems();
        foreach($basketItems as $item) {
            $products[$item->getProductId()]=[
                'ID'=>$item->getProductId(),
                'QUANTITY'=>(int)$item->getQuantity()
            ];
        }
        
        $rs=\CIBlockElement::GetList(
            [], 
            ['=ID'=>array_keys($products)], 
            false, 
            false, 
            ['ID', 'IBLOCK_ID', 'PROPERTY_BULK', 'PROPERTY_LONG_W', 'PROPERTY_HEIGHT_W']
        );
        
        $totalWeight = 0;
        $isUseBoxing = $this->isUseBoxing($result, $shipment);
        $totalBoxingPrice = 0;
        while($el=$rs->Fetch()) {
            $quantity=$products[$el['ID']]['QUANTITY'];
            $totalWeight+=(float)$el['PROPERTY_BULK_VALUE'] * $quantity;
            if($isUseBoxing) {
                $totalBoxingPrice+=(float)$el['PROPERTY_LONG_W_VALUE'] * (float)$el['PROPERTY_HEIGHT_W_VALUE'] * $quantity * $boxingPrice / 1000000;
            }
        }

        $deliveryPrice=$totalWeight * $pricePerWeight;
        if($deliveryPrice < $minPrice) {
            $deliveryPrice=$minPrice;
        }

        if($this->isRemoteDisctrict($result, $shipment)) {
            $deliveryPrice+=$remoteDistrictPrice;
        }

        if($isUseBoxing) {
            $deliveryPrice+=$totalBoxingPrice;
        }

        $result->setDeliveryPrice(
            roundEx(
                $deliveryPrice,
                SALE_VALUE_PRECISION
            )
        );
        $result->setPeriodDescription('');

        $this->checkReceiver($shipment);
        $this->checkDesiredDate($shipment);

        return $result;
    }
}