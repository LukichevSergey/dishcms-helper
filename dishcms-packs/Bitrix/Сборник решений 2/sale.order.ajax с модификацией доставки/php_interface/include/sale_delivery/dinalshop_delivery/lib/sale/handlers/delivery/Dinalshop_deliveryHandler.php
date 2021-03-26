<?php
namespace Sale\Handlers\Delivery;

require_once dirname(__FILE__) . '/profiles/Dinalshop_deliveryProfilePickUp.php';
require_once dirname(__FILE__) . '/profiles/Dinalshop_deliveryProfileNsk.php';
require_once dirname(__FILE__) . '/profiles/Dinalshop_deliveryProfileOther.php';

use Bitrix\Sale\Registry;

class Dinalshop_deliveryHandler extends \Bitrix\Sale\Delivery\Services\Base
{
    const DELIVERY_ERROR_REGISTRY_KEY='SALE_ORDER_AJAX_DELIVERY_ERRORS';

    protected static $isCalculatePriceImmediately = true;
    protected static $whetherAdminExtraServicesShow = false;

    public function __construct(array $initParams)
    {
        $initParams=array_merge($initParams, [
            'GET_ADD_INFO_SHIPMENT_VIEW' => array('\Sale\Handlers\Delivery\Dinalshop_deliveryHandler', 'getAdditionalInfoShipmentView'),
			'GET_ADD_INFO_SHIPMENT_EDIT' => array('\Sale\Handlers\Delivery\Dinalshop_deliveryHandler', 'getAdditionalInfoShipmentEdit'),
			'PROCESS_ADD_INFO_SHIPMENT_EDIT' => array('\Sale\Handlers\Delivery\Dinalshop_deliveryHandler', 'processAdditionalInfoShipmentEdit'),
        ]);

        \Bitrix\Main\EventManager::getInstance()->addEventHandlerCompatible(
            'sale', 
            'OnSaleComponentOrderJsData', 
            '\Sale\Handlers\Delivery\Dinalshop_deliveryHandler::OnSaleComponentOrderJsDataHandler'
        );

        \Bitrix\Main\EventManager::getInstance()->addEventHandlerCompatible(
            'sale', 
            'OnSaleComponentOrderUserResult', 
            '\Sale\Handlers\Delivery\Dinalshop_deliveryHandler::OnSaleComponentOrderUserResultHandler'
        );

        \Bitrix\Main\EventManager::getInstance()->addEventHandlerCompatible(
            'sale', 
            'OnSaleComponentOrderResultPrepared', 
            '\Sale\Handlers\Delivery\Dinalshop_deliveryHandler::OnSaleComponentOrderResultPreparedHandler'
        );

        \Bitrix\Main\EventManager::getInstance()->addEventHandlerCompatible(
            'sale', 
            'OnSaleComponentOrderCreated', 
            '\Sale\Handlers\Delivery\Dinalshop_deliveryHandler::OnSaleComponentOrderCreatedHandler'
        );

        \Bitrix\Main\EventManager::getInstance()->addEventHandler( 
            'sale',         
            'OnSaleOrderBeforeSaved',         
            '\Sale\Handlers\Delivery\Dinalshop_deliveryHandler::onSaleOrderBeforeSavedHandler'        
        ); 
        
        parent::__construct($initParams);
    }

    public static function getClassTitle()
    {
        return 'DinalShop';
    }

    public static function getClassDescription()
    {
        return 'Доставка для сайта DinalShop';
    }

    public function isCalculatePriceImmediately()
    {
        return self::$isCalculatePriceImmediately;
    }

    public static function whetherAdminExtraServicesShow()
    {
        return self::$whetherAdminExtraServicesShow;
    }

    protected function getConfigStructure()
    {
        $result = array(     
            
        );

        return $result;
    }

    protected function calculateConcrete(\Bitrix\Sale\Shipment $shipment = null)
    {
        $result = new \Bitrix\Sale\Delivery\CalculationResult();

        return $result;
    }

    public function isCompatible(\Bitrix\Sale\Shipment $shipment)
    {
        $calcResult = self::calculateConcrete($shipment);

        return $calcResult->isSuccess();
    }

    protected static $canHasProfiles = true;

    public static function canHasProfiles()
    {
        return self::$canHasProfiles;
    }

    public static function getChildrenClassNames()
    {
        return array(
            '\Sale\Handlers\Delivery\Profiles\Dinalshop_deliveryProfilePickUp',
            '\Sale\Handlers\Delivery\Profiles\Dinalshop_deliveryProfileNsk',
            '\Sale\Handlers\Delivery\Profiles\Dinalshop_deliveryProfileOther'
        );
    }

    public function getProfilesList()
    {
        return array("Новый профиль");
    }
    
    public static function OnSaleComponentOrderResultPreparedHandler($order, &$arUserResult, $request, &$arParams, &$arResult)
    {
        $registry = Registry::getInstance(Registry::REGISTRY_TYPE_ORDER);

        try {
            $errors=$registry->get(self::DELIVERY_ERROR_REGISTRY_KEY);
            if(!empty($errors)) {
                return new \Bitrix\Main\EventResult(
                    \Bitrix\Main\EventResult::ERROR,
                    \Bitrix\Sale\ResultError::create(new \Bitrix\Main\Error("Необходимо заполнить поля", "ORDER_ERROR"))
                );                
            }
        }
        catch(\Exception $e) {

        }
    }
    
    public static function OnSaleComponentOrderUserResultHandler(&$arUserResult, $request, &$arParams)
    {
        
    }

    public static function OnSaleComponentOrderCreatedHandler($order, &$arUserResult, $request, &$arParams, &$arResult, &$arDeliveryServiceAll, &$arPaySystemServiceAll)
    {
        $registry = Registry::getInstance(Registry::REGISTRY_TYPE_ORDER);
        
        try {
            $errors=$registry->get(self::DELIVERY_ERROR_REGISTRY_KEY);
            if(!empty($errors)) {
                foreach($errors as $error) {
                    $arResult['ERROR_SORTED']['DELIVERY'][]=$error;
                }
            }
        }
        catch(\Exception $e) {

        }
    }

    public static function onSaleOrderBeforeSavedHandler(\Bitrix\Main\Event $event, $values=[])
    {
        $registry = Registry::getInstance(Registry::REGISTRY_TYPE_ORDER);

        try {
            $errors=$registry->get(self::DELIVERY_ERROR_REGISTRY_KEY);
            if(!empty($errors)) {
                return new \Bitrix\Main\EventResult(
                    \Bitrix\Main\EventResult::ERROR,
                    \Bitrix\Sale\ResultError::create(new \Bitrix\Main\Error("Необходимо заполнить поля", "ORDER_ERROR"))
                );                
            }
        }
        catch(\Exception $e) {

        }

        if(empty($errors)) {
            // заменяем идетификатор района на наименование района
            $order = $event->getParameter("ENTITY");

            $districtIBlockId=null;
            $shipmentCollection = $order->getShipmentCollection();
            foreach($shipmentCollection as $shipment) {
                $values=$shipment->getFields()->getValues();
                if(!empty($values['DELIVERY_ID'])) {
                    try {
                        $delivery=\Bitrix\Sale\Delivery\Services\Manager::getObjectById($values['DELIVERY_ID']);
                        if(!empty($delivery->config['SYSTEM']['DISTRICT_IBLOCK_ID'])) {
                            $districtIBlockId=$delivery->config['SYSTEM']['DISTRICT_IBLOCK_ID'];
                            break;
                        }
                    }
                    catch(\Exception $e) {

                    }
                }
            }

            if($districtIBlockId) {
                $propertyCollection = $order->getPropertyCollection();
                foreach ($propertyCollection as $propertyItem) {
                    if (in_array($propertyItem->getField("CODE"), ['DELIVERY_DISTRICT', 'DELIVERY_RECEIVER_DISTRICT'])) {
                        if(is_numeric(trim($propertyItem->getValue()))) {
                            $rs=\CIBlockElement::GetList([], ['=ID'=>$propertyItem->getValue(), 'IBLOCK_ID'=>$districtIBlockId]);
                            if($district=$rs->Fetch()) {
                                $propertyItem->setField("VALUE", $district['NAME']);
                            }
                        }
                    }
                }
            }
        }
    }

    public static function OnSaleComponentOrderJsDataHandler(&$arResult, &$arParams) 
    {
        static::modifyResultData($arResult, $arParams);
    }

    public static function modifyResultData(&$arResult, &$arParams)
    {
        /*
        if (isset($arResult['JS_DATA']['LAST_ORDER_DATA']['DELIVERY'])
            && $arResult['JS_DATA']['LAST_ORDER_DATA']['DELIVERY']!='') 
        {
            $arResult['JS_DATA']['LAST_ORDER_DATA']['DELIVERY'] = '';
        }
        */

        if(!empty($arParams['ORDER_AJAX_EXT_DELIVERY_GROUP_ID'])) {
            $groupParamsIds = [
                $arParams['ORDER_AJAX_EXT_DELIVERY_GROUP_ID'],
                $arParams['ORDER_AJAX_EXT_BIZ_DELIVERY_GROUP_ID']
            ];
            foreach ($arResult['JS_DATA']['ORDER_PROP']['properties'] as $key => $prop) {
                if ($prop['PROPS_GROUP_ID'] && in_array($prop['PROPS_GROUP_ID'], $groupParamsIds)) {
                    if(!in_array($prop['CODE'], ['ZIP', 'LOCATION'])) {
                        $arResult['JS_DATA']['DELIVERY_PROPS']['properties'][] = $arResult['JS_DATA']['ORDER_PROP']['properties'][$key];
                        unset($arResult['JS_DATA']['ORDER_PROP']['properties'][$key]);
                    }
                }
            }
            foreach ($arResult['JS_DATA']['ORDER_PROP']['groups'] as $key => $group) {
                if ($group['ID'] && in_array($group['ID'], $groupParamsIds)) {
                    $arResult['JS_DATA']['DELIVERY_PROPS']['groups'][] = $arResult['JS_DATA']['ORDER_PROP']['groups'][$key];
                    // unset($arResult['JS_DATA']['ORDER_PROP']['groups'][$key]);
                }
            }
        }
    }

    public static function addError($message)
    {
        $registry = Registry::getInstance(Registry::REGISTRY_TYPE_ORDER);
        
        try {
            $errors = $registry->get(self::DELIVERY_ERROR_REGISTRY_KEY);
        }
        catch(\Exception $e) {
            $errors=[];
        }

        $errors[md5($message)]=$message;
        
        $registry->set(self::DELIVERY_ERROR_REGISTRY_KEY, $errors);

        return new \Bitrix\Main\Error($message);
    }
}