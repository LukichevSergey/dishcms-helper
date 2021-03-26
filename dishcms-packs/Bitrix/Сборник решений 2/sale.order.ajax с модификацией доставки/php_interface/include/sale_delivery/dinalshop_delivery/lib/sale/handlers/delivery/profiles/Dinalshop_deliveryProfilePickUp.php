<?php
namespace Sale\Handlers\Delivery\Profiles;

class Dinalshop_deliveryProfilePickUp extends \Bitrix\Sale\Delivery\Services\Base
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
        return 'Самовывоз';
    }

    public static function getClassDescription()
    {
        return '';
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
        );
        
        return $result;
    }

    protected function calculateConcrete(\Bitrix\Sale\Shipment $shipment = null)
    {
        $result = new \Bitrix\Sale\Delivery\CalculationResult();
        $result->setDeliveryPrice(
            roundEx(
                0,
                SALE_VALUE_PRECISION
            )
        );
        $result->setPeriodDescription('');

        return $result;
    }
}