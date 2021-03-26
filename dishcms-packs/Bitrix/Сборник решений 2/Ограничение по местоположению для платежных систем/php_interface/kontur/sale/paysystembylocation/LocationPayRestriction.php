<?php
use Bitrix\Sale\Services\Base;
use Bitrix\Sale\Internals\Entity;
use Bitrix\Sale\Payment;
use Bitrix\Main\Loader;
use Bitrix\Sale\Internals\CollectableEntity;
use Bitrix\Sale\Shipment;
use Bitrix\Sale\Internals\PaySystemLocationTable;
use Bitrix\Sale\Location\Tree\NodeNotFoundException;

Loader::includeModule("sale");

require_once dirname(__FILE__) . '/bitrix/modules/sale/lib/internals/paysystemlocation.php';
require_once dirname(__FILE__) . '/bitrix/modules/sale/lib/services/paysystem/manager.php';
require_once dirname(__FILE__) . '/bitrix/modules/sale/lib/services/paysystem/inputs.php';

class LocationPayRestriction extends Base\Restriction
{
    public static $easeSort = 200;
    
	public static function registerEvent($createTable=false)
	{
		\Bitrix\Main\EventManager::getInstance()->addEventHandler(
    		'sale',
    		'onSalePaySystemRestrictionsClassNamesBuildList',
    		function() {
				return new \Bitrix\Main\EventResult(
        			\Bitrix\Main\EventResult::SUCCESS,
        			array(
            			'\LocationPayRestriction' => __FILE__,
        			)
    			);
			}
		);
		
		if($createTable) {
		    global $DB;
		    $DB->Query('CREATE TABLE IF NOT EXISTS `b_sale_paysystem2location` (
		        `PAYSYSTEM_ID` int(11) NOT NULL,
		        `LOCATION_CODE` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
		        `LOCATION_TYPE` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT \'L\',
		        PRIMARY KEY (`PAYSYSTEM_ID`,`LOCATION_CODE`,`LOCATION_TYPE`)
		        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;'
		    );
		}
	}

	public static function getClassTitle()
    {
        return 'По местоположению';
    }

	public static function getClassDescription()
    {
        return 'Платежная система будет доступна только для выбранных местоположений';
    }

	/**
	 * @param $params
	 * @param array $restrictionParams
	 * @param int $serviceId
	 * @return bool
	 */
	public static function check($params, array $restrictionParams, $serviceId = 0)
	{
		if ((int)$serviceId <= 0)
			return true;

		if (!$params)
			return false;

		try
		{
		    return PaySystemLocationTable::checkConnectionExists(
				intval($serviceId),
				$params,
				array(
					'LOCATION_LINK_TYPE' => 'AUTO'
				)
			);
		}
		catch (NodeNotFoundException $e)
		{
			return false;
		}
	}

	/**
	 * @param Entity $entity
	 *
	 * @return null|string
	 */
	protected static function extractParams(Entity $entity)
	{
		if ($entity instanceof CollectableEntity)
		{
			/** @var \Bitrix\Sale\Order $order */
			$order = $entity->getCollection()->getOrder();
		}
		elseif ($entity instanceof Order)
		{
			/** @var \Bitrix\Sale\Order $order */
			$order = $entity;
		}

		if (!$order)
			return '';

		if(!$props = $order->getPropertyCollection())
			return '';

		if(!$locationProp = $props->getDeliveryLocation())
			return '';

		if(!$locationCode = $locationProp->getValue())
			return '';

		return $locationCode;
	}

	/**
	 * @param array $params
	 * @param int $companyId
	 * @return array
	 */
	protected static function prepareParamsForSaving(array $params = array(), $companyId = 0)
	{
		if($companyId > 0)
		{
			$arLocation = array();

			if(!!\CSaleLocation::isLocationProEnabled())
			{
				if(strlen($params["LOCATION"]['L']))
					$LOCATION1 = explode(':', $params["LOCATION"]['L']);

				if(strlen($params["LOCATION"]['G']))
					$LOCATION2 = explode(':', $params["LOCATION"]['G']);
			}

			if (isset($LOCATION1) && is_array($LOCATION1) && count($LOCATION1) > 0)
			{
				$arLocation["L"] = array();
				$locationCount = count($LOCATION1);

				for ($i = 0; $i<$locationCount; $i++)
					if (strlen($LOCATION1[$i]))
						$arLocation["L"][] = $LOCATION1[$i];
			}

			if (isset($LOCATION2) && is_array($LOCATION2) && count($LOCATION2) > 0)
			{
				$arLocation["G"] = array();
				$locationCount = count($LOCATION2);

				for ($i = 0; $i<$locationCount; $i++)
					if (strlen($LOCATION2[$i]))
						$arLocation["G"][] = $LOCATION2[$i];

			}

			PaySystemLocationTable::resetMultipleForOwner($companyId, $arLocation);
		}

		return array();
	}

	/**
	 * @param int $entityId
	 * @return array
	 */
	public static function getParamsStructure($entityId = 0)
	{

		$result =  array(
			"LOCATION" => array(
				"TYPE" => "PAYSYSTEM_LOCATION_MULTI"
			)
		);

		if($entityId > 0 )
			$result["LOCATION"]["PAYSYSTEM_ID"] = $entityId;

		return $result;
	}

	/**
	 * @param array $fields
	 * @param int $restrictionId
	 * @return \Bitrix\Main\Entity\AddResult|\Bitrix\Main\Entity\UpdateResult
	 */
	public static function save(array $fields, $restrictionId = 0)
	{
		$fields["PARAMS"] = self::prepareParamsForSaving($fields["PARAMS"], $fields["SERVICE_ID"]);
		return parent::save($fields, $restrictionId);
	}

	/**
	 * @param $restrictionId
	 * @param int $entityId
	 * @return \Bitrix\Main\Entity\DeleteResult
	 */
	public static function delete($restrictionId, $entityId = 0)
	{
	    PaySystemLocationTable::resetMultipleForOwner($entityId);
		return parent::delete($restrictionId);
	}
}
