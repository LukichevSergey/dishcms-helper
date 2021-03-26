<?php
namespace Kontur\Calculator\Component;

use Bitrix\Main;
use Bitrix\Main\Loader;
use Bitrix\Main\HttpApplication;
use Bitrix\Main\Web\Uri;
use Kontur\Calculator\Helper;

/**
 * Class Calculator
 *
 * @package Kontur\Calculator
 */
class Calculator extends \CBitrixComponent
{
    private $responseData=[];

    /**
     * @inheritDoc
     *
     * @throws Main\SystemException
     */
    public function onPrepareComponentParams($arParams)
    {
        Loader::includeModule("iblock");
        Loader::includeModule("catalog");
        Loader::includeModule("kontur.calculator");

        $arParams['CACHE_TYPE'] = 'N';

        return $arParams;
    }

    /** 
     * @inheritDoc
     */
    public function executeComponent()
    {
        $this->init();
        $this->includeComponentTemplate();
    }

    private function init()
    {
        $this->arResult['ITEMS']=$this->getSettingsItems();
    }

    private function getSettingsItems()
    {
        $items=[];

        $rs=\CIBlockElement::GetList(
            ['SORT'=>'ASC'], 
            ['IBLOCK_ID'=>Helper::getSettingsIblockId(), 'ACTIVE'=>'Y'],
            false,
            false,
            ['ID', 'NAME', 'PROPERTY_' . Helper::getFormFieldPropertyId(), 'PROPERTY_' . Helper::getCalcFormulasPropertyId()]
        );
        while($item=$rs->Fetch()) {
            $items[$item['ID']]=[
                'ID'=>$item['ID'],
                'NAME'=>$item['NAME'],
                'FIELDS'=>Helper::getFormFields($item['ID'], $item['PROPERTY_' . Helper::getFormFieldPropertyId() . '_VALUE']),
                'FORMULAS'=>Helper::getCalcFormulas($item['ID'], $item['PROPERTY_' . Helper::getCalcFormulasPropertyId() . '_VALUE']),
            ];
        }

        return $items;
    }
}