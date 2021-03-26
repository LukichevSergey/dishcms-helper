<?
namespace Kontur;

if (!\CModule::IncludeModule("highloadblock")) {
    return false;
}

use Bitrix\Highloadblock as HL;

class Highload
{
	/**
     * @link http://julliet.ru/articles/highload-api.html
     */
	public static function getEntityByXmlId($HBLOCK_ID, $ENTITY_XML_ID, $PROP_NAME=null)
	{
		$arResult = $PROP_NAME ? array() : null;

	   	if($entityDataClass = self::getEntityDataClass($HBLOCK_ID)) {
	   		$rsProp = $entityDataClass::getList(Array(
    	  		"select"   => Array('*'),
      			"filter"   => Array('=UF_XML_ID' => $ENTITY_XML_ID),
	   		));
   
   			if($arProp = $rsProp->Fetch()) {
	      		$arResult = $PROP_NAME ? $arProp[$PROP_NAME] : $arProp;
   			}
		}

		return $arResult;
	}

	public static function getList($HBLOCK_ID, $arFilter=array())
	{
		$arResult = array();

		if($entityDataClass = self::getEntityDataClass($HBLOCK_ID)) {
			$rsData = $entityDataClass::getList(array(
				"select" => array('*'), //выбираем все поля
				"filter" =>$arFilter,
				"order" => array("UF_SORT"=>"ASC") // сортировка по полю UF_SORT, будет работать только, если вы завели такое поле в hl'блоке
			));

			while($arData = $rsData->Fetch()) {
				$arResult[]=$arData;
			}
		}

		return $arResult;
	}

	public static function getEntityDataClass($HBLOCK_ID)
	{
		$hlblock = HL\HighloadBlockTable::getById($HBLOCK_ID)->fetch();

		if(!empty($hlblock)) {
	        $entity = HL\HighloadBlockTable::compileEntity($hlblock);
    	    return $entity->getDataClass();
		}

		return null;
	}
}
