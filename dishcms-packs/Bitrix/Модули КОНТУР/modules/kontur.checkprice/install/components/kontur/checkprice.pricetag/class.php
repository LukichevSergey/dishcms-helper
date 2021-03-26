<?php
namespace Kontur\CheckPrice\Component;

use Bitrix\Main;
use Bitrix\Main\Loader;
use Bitrix\Main\HttpApplication;
use Bitrix\Main\Web\Uri;
use Kontur\CheckPrice\Helper;
use Kontur\CheckPrice\PriceTagCollection;

/**
 * Class CheckPrice_PriceTag
 *
 * @package Kontur\CheckPrice
 */
class CheckPricePriceTag extends \CBitrixComponent
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
        Loader::includeModule("kontur.checkprice");
        
        Helper::checkAccess();

        if (!($arParams['ITEMS_IBLOCK_ID'] || $arParams['OFFERS_IBLOCK_ID'])) {
            $arParams['ITEMS_IBLOCK_ID'] = Helper::getItemsIblockId();
            $arParams['OFFERS_IBLOCK_ID'] = Helper::getOffersIblockId();
            
            if (!($arParams['ITEMS_IBLOCK_ID'] || $arParams['OFFERS_IBLOCK_ID'])) {
                throw new Main\SystemException('Ошибка: Не удалось найти инфоблоки товаров или торговых предложений');
            }
        }

        $this->setTemplateName(isset($_REQUEST['print']) && ($_REQUEST['print'] == 'Y') ? 'print' : '.default');

        $arParams['CACHE_TYPE'] = 'N';

        return $arParams;
    }

    /** 
     * @inheritDoc
     */
    public function executeComponent()
    {
        $this->runAction();

        $this->includeComponentTemplate();
    }

    /**
     * Получить идентификатор инфоблока товаров
     *
     * @return integer
     */
    public function getItemsIblockId()
    {
        return (int)$this->arParams['ITEMS_IBLOCK_ID'];
    }

    /**
     * Получить идентификатор инфоблока торговых предложений
     *
     * @return integer
     */
    public function getOffersIblockId()
    {
        return (int)$this->arParams['OFFERS_IBLOCK_ID'];
    }

    public function getPriceTagList($properties=[])
    {
        $data=[];

        $priceTagCollection=PriceTagCollection::getInstance()->getCollectionData();
        if(!empty($priceTagCollection)) {
            $cml2linkProperty=Helper::getCml2LinkProperty();
            $catalogGroupId=Helper::getCatalogGroupId();
            $properties=array_map(function($prop) { return mb_strtoupper($prop); }, $properties);

            $ids=array_keys($priceTagCollection);
            $select=array_merge(
                ['ID', 'IBLOCK_ID', 'CODE', 'NAME', 'DETAIL_PAGE_URL', "PROPERTY_{$cml2linkProperty}"],
                array_map(function($prop) { return 'PROPERTY_' . $prop; }, $properties)
            );
            // получаем торговые предложения
            $rs=\CIBlockElement::GetList(
                ['NAME'=>'ASC'], 
                ['IBLOCK_ID'=>Helper::getOffersIblockId(), 'ID'=>$ids], 
                false, 
                false, 
                $select
            );
            $offersId=[];
            while($el=$rs->GetNext()) {
                $id=(int)$el['ID'];
                $productId=(int)$el["PROPERTY_{$cml2linkProperty}_VALUE"];
                $price=Helper::getOptimalPrice($id, false);
                
                $data[$productId]['OFFERS'][$id]['NAME']=$el['NAME'];
                $data[$productId]['OFFERS'][$id]['DETAIL_PAGE_URL']=$el['DETAIL_PAGE_URL'];
                $data[$productId]['OFFERS'][$id]['OLD_PRICE']=$price['RESULT_PRICE']['UNROUND_BASE_PRICE'] ?? null;
                $data[$productId]['OFFERS'][$id]['PRICE']=$price['RESULT_PRICE']['UNROUND_DISCOUNT_PRICE'] ?? null;
                $data[$productId]['OFFERS'][$id]['CURRENCY']=$price['RESULT_PRICE']['CURRENCY'] ?? 'RUB';
                $data[$productId]['OFFERS'][$id]['IS_PRICETAG']=isset($priceTagCollection[$id]);
                $data[$productId]['OFFERS'][$id]['PROPERTIES']=[];
                if(!empty($properties)) {
                    foreach($properties as $prop) {
                        $data[$productId]['OFFERS'][$id]['PROPERTIES'][$prop]=$el["PROPERTY_{$prop}_VALUE"] ?? null;
                    }
                }
                
                $ids[]=$productId;
                $offersId[$id]=$productId;
            }

            // Получаем единицы измерения для торговых предложений
            $measures=\Bitrix\Catalog\ProductTable::getCurrentRatioWithMeasure(array_keys($offersId));
            foreach($measures as $id=>$measure) {
                $data[$offersId[$id]]['OFFERS'][$id]['MEASURE']=$measure['MEASURE']['SYMBOL_RUS'];
            }

            // получаем товары
            $rs=\CIBlockElement::GetList(
                ['NAME'=>'ASC'], 
                ['IBLOCK_ID'=>Helper::getItemsIblockId(), 'ID'=>$ids], 
                false, 
                false, 
                $select
            );
            $productsId=[];
            while($el=$rs->GetNext()) {
                $id=(int)$el['ID'];
                $price=Helper::getOptimalPrice($id, false);
                
                $data[$id]['NAME']=$el['NAME'];
                $data[$id]['DETAIL_PAGE_URL']=$el['DETAIL_PAGE_URL'];
                $data[$id]['OLD_PRICE']=$price['RESULT_PRICE']['UNROUND_BASE_PRICE'] ?? null;
                $data[$id]['PRICE']=$price['RESULT_PRICE']['UNROUND_DISCOUNT_PRICE'] ?? null;
                $data[$id]['CURRENCY']=$price['RESULT_PRICE']['CURRENCY'] ?? 'RUB';
                $data[$id]['IS_PRICETAG']=isset($priceTagCollection[$id]);
                $data[$id]['PROPERTIES']=[];
                if(!empty($properties)) {
                    foreach($properties as $prop) {
                        $data[$id]['PROPERTIES'][$prop]=$el["PROPERTY_{$prop}_VALUE"] ?? null;
                    }
                }

                $productsId[]=$id;
            }
            
            // Получаем единицы измерения для торговых предложений
            $measures=\Bitrix\Catalog\ProductTable::getCurrentRatioWithMeasure($productsId);
            foreach($measures as $id=>$measure) {
                $data[$id]['MEASURE']=$measure['MEASURE']['SYMBOL_RUS'];
            }
        }

        return $data;
    }

    /**
     * Запуск действия
     *
     * @return void
     */
    private function runAction()
    {
        $request = HttpApplication::getInstance()->getContext()->getRequest();
        
        if($request->isPost()) {
            /*
            if(!check_bitrix_sessid()) {
                throw new Main\SystemException('Ошибка: Доступ запрещен');
            }
            /**/
            switch($request->getPost('ACTION')) {
                case 'REMOVE_PRICETAG':
                    if($id=(int)$request->getPost('ID')) {
                        PriceTagCollection::getInstance()->remove($id);
                        $this->setResponseData(['success'=>true]);                        
                    }
                    $this->sendResponse();
                    break;
            }
        }
    }

    private function setResponseData($data)
    {
        $this->responseData=$data;
    }

    private function sendResponse()
    {
        global $APPLICATION;
        
        $APPLICATION->RestartBuffer();
        echo json_encode($this->responseData);
        exit;
    }

    private function getUri($params=[])
    {
        $request = HttpApplication::getInstance()->getContext()->getRequest();
        
        $uri = new Uri($request->getRequestUri());
        $uri->deleteParams(['ACTION']);
        $uri->addParams($params);

        return $uri->getUri();
    }
}