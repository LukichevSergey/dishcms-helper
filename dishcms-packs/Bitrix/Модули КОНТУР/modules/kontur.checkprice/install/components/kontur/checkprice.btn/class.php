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
class CheckPriceBtn extends \CBitrixComponent
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

        $arParams['CACHE_TYPE'] = 'N';

        return $arParams;
    }

    /** 
     * @inheritDoc
     */
    public function executeComponent()
    {
        if(Helper::checkAccess(true)) {
            $this->runAction();
            $this->includeComponentTemplate();
        }
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
            switch($request->getPost('ACTION')) {
                case 'PRICETAG_BTN_LIST_GET_COUNT':
                    $this->setResponseData([
                        'success'=>true, 
                        'count'=>PriceTagCollection::getInstance()->count()
                    ]);
                    $this->sendResponse();
                    break;

                case 'PRICETAG_BTN_EXISTS':
                    if($id=(int)$request->getPost('ID')) {
                        $this->setResponseData([
                            'success'=>true, 
                            'exists'=>PriceTagCollection::getInstance()->exists($id)
                        ]);
                    }
                    $this->sendResponse();
                    break;

                case 'PRICETAG_BTN_PRICETAG':
                    if($id=(int)$request->getPost('ID')) {
                        if(PriceTagCollection::getInstance()->exists($id)) {
                            PriceTagCollection::getInstance()->remove($id);
                            $this->setResponseData(['success'=>true, 'status'=>'removed']);
                        }
                        else {
                            PriceTagCollection::getInstance()->add($id);
                            $this->setResponseData(['success'=>true, 'status'=>'added']);
                        }                        
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