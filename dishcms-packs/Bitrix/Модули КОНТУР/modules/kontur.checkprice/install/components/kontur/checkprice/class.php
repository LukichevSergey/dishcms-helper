<?php
namespace Kontur\CheckPrice\Component;

use Bitrix\Main;
use Bitrix\Main\Loader;
use Bitrix\Main\Application;
use Bitrix\Main\HttpApplication;
use Bitrix\Main\Web\Uri;
use Kontur\CheckPrice\Helper;
use Kontur\CheckPrice\SnapTable;
use Kontur\CheckPrice\SnapProductTable;
use Kontur\CheckPrice\PriceTagCollection;

/**
 * Class CheckPrice
 *
 * @package Kontur\CheckPrice
 */
class CheckPrice extends \CBitrixComponent
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

        $arParams['CACHE_TYPE'] = 'N';
        // $arParams['CATALOG_GROUP_ID'] = Helper::getCatalogGroupId();
        // $arParams['SNAP_LIMIT'] = Helper::DEFAULT_SNAP_LIMIT;
        // $arParams['SNAP_DELAY'] = Helper::DEFAULT_SNAP_DELAY;

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
     * Получить тип цены
     *
     * @return integer
     */
    public function getCatalogGroupId()
    {
        return isset($this->arParams['CATALOG_GROUP_ID']) ? (int)$this->arParams['CATALOG_GROUP_ID'] : Helper::getCatalogGroupId();
    }

    /**
     * Получить лимит обрабатываемых товаров за шаг
     *
     * @return integer
     */
    public function getSnapLimit()
    {
        return isset($this->arParams['SNAP_LIMIT']) ? (int)$this->arParams['SNAP_LIMIT'] : Helper::getSnapLimit();
    }

    /**
     * Получить задержку между итерациями
     *
     * @return integer
     */
    public function getSnapDelay()
    {
        return isset($this->arParams['SNAP_DELAY']) ? (int)$this->arParams['SNAP_LIMIT'] : Helper::DEFAULT_SNAP_DELAY;
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

    /**
     * Получить количество снимков
     *
     * @return integer
     */
    public function getSnapCount()
    {
        return (int)SnapTable::getCount([
            'STATUS'=>Helper::SNAP_STATUS_DONE
        ]);
    }

    /**
     * Получение последнего снимка
     *
     * @return SnapTable
     */
    public function getLastSnap()
    {
        return SnapTable::getList([
            'filter'=>['STATUS'=>Helper::SNAP_STATUS_DONE],
            'order'=>['CREATE_TIME'=>'DESC'],
            'limit'=>1
        ])->fetchObject();
    }

    /**
     * Получение снимка по идентификатору
     *
     * @param int $id идентификатор снимка
     * @param [] $parameters дополнительные параметры для запроса
     * 
     * @return SnapTable
     */
    public function getSnapById($id, $withProducts=false)
    {
        return SnapTable::getSnapById($id, $withProducts);
    }

    /**
     * Получение предыдущего снимка
     *
     * @param SnapTable $snap идентификатор снимка
     * 
     * @return SnapTable
     */
    public function getPreviousSnap($snap)
    {
        return SnapTable::getList([
            'filter'=>['<ID'=>$snap->getId(), 'STATUS'=>Helper::SNAP_STATUS_DONE],
            'limit'=>1
        ])->fetchObject();
    }

    public function getAdminTab($tabTitle, $title=null, $onSelect=null)
    {
        return [
            'DIV'=>'tab_' . uniqid(),
            'TAB'=>$tabTitle,
            'ICON'=>'iblock',
            'TITLE'=>$title ?: $tabTitle,
            'ONSELECT'=>$onSelect
        ];
    }

    public function getActualSnapData($snapId)
    {
        $data=[];

        if($snap=$this->getSnapById($snapId, true)) {
            set_time_limit(0);

            $snapProducts=[];
            foreach($snap->getProducts() as $product) {
                $id=(int)$product->getOfferId() ?: $product->getProductId();
                $snapProducts[$id]=[
                    'PRODUCT_ID'=>(int)$product->getProductId(),
                    'PRICE'=>$product->getPrice()
                ];
            }

            $cml2linkProperty=Helper::getCml2LinkProperty();
            $catalogGroupId=Helper::getCatalogGroupId();
            $rs=\CIBlockElement::GetList(
                ['NAME'=>'ASC'], 
                ['IBLOCK_ID'=>[Helper::getItemsIblockId(), Helper::getOffersIblockId()]], 
                false, 
                false, 
                ['ID', 'IBLOCK_ID', 'CODE', 'NAME', 'DETAIL_PAGE_URL', "PROPERTY_{$cml2linkProperty}", "CATALOG_GROUP_{$catalogGroupId}"]
            );
            $products=[];
            while($el=$rs->GetNext()) {
                $id=(int)$el['ID'];
                $productId=(int)$el["PROPERTY_{$cml2linkProperty}_VALUE"];
                if($productId) { 
                    $products[$productId]['OFFERS'][$id]=$el;
                }
                else {
                    $products[$id]['PRODUCT']=$el;
                }
            }

            foreach($products as $productId=>$product) {
                if(empty($product['OFFERS'])) {
                    // $newPrice=(float)$product['PRODUCT']["CATALOG_PRICE_{$catalogGroupId}"]; // для получения цен без скидок
                    $newPrice=Helper::getOptimalPrice($productId);
                    $oldPrice=isset($snapProducts[$productId]) ? (float)$snapProducts[$productId]['PRICE'] : null;
                    if($oldPrice != $newPrice) {
                        $data[$id]=[
                            'NAME'=>$product['PRODUCT']['NAME'],
                            'DETAIL_PAGE_URL'=>$product['PRODUCT']['DETAIL_PAGE_URL'],
                            'OLD_PRICE'=>$oldPrice,
                            'NEW_PRICE'=>$newPrice
                        ];
                    }
                }
                else {
                    foreach($product['OFFERS'] as $offerId=>$offer) {
                        // $newPrice=(float)$offer["CATALOG_PRICE_{$catalogGroupId}"]; // для получения цен без скидок
                        $newPrice=Helper::getOptimalPrice($offerId);
                        $oldPrice=isset($snapProducts[$offerId]) ? (float)$snapProducts[$offerId]['PRICE'] : null;
                        if($oldPrice != $newPrice) {
                            if(!isset($data[$productId])) {
                                $data[$productId]=[
                                    'NAME'=>$product['PRODUCT']['NAME'],
                                    'DETAIL_PAGE_URL'=>$product['PRODUCT']['DETAIL_PAGE_URL']
                                ];
                            }
                            $data[$productId]['OFFERS'][$offerId]=[
                                'NAME'=>$offer['NAME'],
                                'DETAIL_PAGE_URL'=>$offer['DETAIL_PAGE_URL'],
                                'OLD_PRICE'=>$oldPrice,
                                'NEW_PRICE'=>$newPrice
                            ];
                        }
                    }
                }              
            }
        }

        return $data;
    }

    public function getSnap2SnapData($snapId1, $snapId2)
    {
        $data=[];

        if(!$snapId1 && !$snapId2) {
            return [];
        }

        // выбираем только товары только с измененными ценами
        $connection=Application::getConnection();
        $sql='SELECT `ID`, `SNAP_ID`, `PRODUCT_ID`, `OFFER_ID`, `PRICE`, COUNT(*) AS `CNT` FROM `'
            . (new SnapProductTable)->getTableName() 
            . '` WHERE `SNAP_ID` IN (' . (int)$snapId1 . ',' . (int)$snapId2 . ')'
            . ' GROUP BY `PRODUCT_ID`, `OFFER_ID`, `PRICE` HAVING `CNT`=1 ORDER BY `PRODUCT_ID`, `OFFER_ID`, `SNAP_ID`';

        $rs=$connection->query($sql);

        $ids=[];
        $last=null;
        while($row=$rs->fetch()) {
            if($last) {
                if(!isset($ids[$row['PRODUCT_ID']])) {
                    $ids[$row['PRODUCT_ID']]=$row['PRODUCT_ID'];
                    $data[$row['PRODUCT_ID']]=[
                        'NAME'=>'',
                        'DETAIL_PAGE_URL'=>'',
                        'OFFERS'=>[]
                    ];
                }

                $isNewProduct=(($last['PRODUCT_ID'] != $row['PRODUCT_ID']) || ($last['OFFER_ID'] != $row['OFFER_ID']));
                $oldPrice=$isNewProduct ? null : (($last['SNAP_ID'] == $snapId2) ? $last['PRICE'] : $row['PRICE']);
                $newPrice=($row['SNAP_ID'] == $snapId1) ? $row['PRICE'] : ($isNewProduct ? null :  $last['PRICE']);

                if(!empty($row['OFFER_ID'])) {
                    if(!isset($ids[$row['OFFER_ID']])) {
                        $ids[$row['OFFER_ID']]=$row['PRODUCT_ID'];
                        $data[$row['PRODUCT_ID']]['OFFERS'][$row['OFFER_ID']]=[
                            'NAME'=>'',
                            'DETAIL_PAGE_URL'=>'',
                            'OLD_PRICE'=>$oldPrice,
                            'NEW_PRICE'=>$newPrice
                        ];
                    }
                    else {
                        $data[$row['PRODUCT_ID']]['OFFERS'][$row['OFFER_ID']]['OLD_PRICE']=$oldPrice;
                        $data[$row['PRODUCT_ID']]['OFFERS'][$row['OFFER_ID']]['NEW_PRICE']=$newPrice;
                    }
                }
                else {
                    $data[$row['PRODUCT_ID']]['OLD_PRICE']=$oldPrice;
                    $data[$row['PRODUCT_ID']]['NEW_PRICE']=$newPrice;
                }
            }
            $last=$row;
        }

        // получим дополнительные данные для товаров и торговых предложений
        if(!empty($ids)) {
            $rs=\CIBlockElement::GetList([], ['ID'=>array_keys($ids)], false, false, ['ID', 'IBLOCK_ID', 'CODE', 'NAME', 'DETAIL_PAGE_URL']);
            while($el=$rs->GetNext()) {
                if($ids[$el['ID']] != $el['ID']) {
                    // обрабатывается торговое предложение
                    $data[$ids[$el['ID']]]['OFFERS'][$el['ID']]['NAME']=$el['NAME'];
                    $data[$ids[$el['ID']]]['OFFERS'][$el['ID']]['DETAIL_PAGE_URL']=$el['DETAIL_PAGE_URL'];
                }
                else {
                    $data[$el['ID']]['NAME']=$el['NAME'];
                    $data[$el['ID']]['DETAIL_PAGE_URL']=$el['DETAIL_PAGE_URL'];
                }
            }
        }

        return $data;
    }

    public function getSnaps()
    {
        return SnapTable::getList([
            'filter'=>['STATUS'=>Helper::SNAP_STATUS_DONE],
            'order'=>['CREATE_TIME'=>'desc'],
            'limit'=>100
        ])->fetchAll();
    }

    public function getSnapList($parameters=[])
    {
        $parameters=array_merge([
            'order'=>['CREATE_TIME'=>'desc']
        ], $parameters);
        
        return SnapTable::getList($parameters);
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
                // Action: Создание снимка
                case 'SNAP':
                    $this->actionSnap();
                    $this->sendResponse();
                    break;

                // Action: Следующая итерация создания снимка
                case 'SNAP_NEXT':
                    set_time_limit(0);
                    ignore_user_abort(true);
                    $this->actionSnapNext((int)$request->getPost('ID'));
                    $this->sendResponse();
                    break;

                // Action: Создание снимака завершено
                case 'SNAP_DONE':
                    $this->setResponseData([
                        'html'=>(new \CAdminMessage(['TYPE'=>'OK', 'MESSAGE'=>'Создание снимка цен завершено']))->Show()
                            . '<input type="button" class="button" onclick="window.location.href=\''.$this->getUri().'\'" value="Вернуться к списку" />'
                    ]);
                    $this->sendResponse();
                    break;

                // Action: Загрузка данных снимка
                case 'LOAD':
                    // для исключения бесконечной рекурсии
                    if(!($this->getParent() instanceof \Kontur\CheckPrice\Component\CheckPrice)) {
                        $snapId1=(int)$request->getPost('SNAP1');
                        $snapId2=(int)$request->getPost('SNAP2');
                        if($snapId1) {
                            ob_start();
                            global $APPLICATION;
                            $APPLICATION->IncludeComponent('kontur:checkprice', 'snaplist', [
                                'ITEMS_IBLOCK_ID'  => $this->arParams['ITEMS_IBLOCK_ID'],
                                'OFFERS_IBLOCK_ID' => $this->arParams['OFFERS_IBLOCK_ID'],
                                'SNAP_1'=>$snapId1,
                                'SNAP_2'=>$snapId2
                            ], $this);
                            $html=ob_get_clean();
                        }
                        else {
                            $html=(new \CAdminMessage(['TYPE'=>'ERROR', 'MESSAGE'=>'Не удалось загрузить данные']))->Show();
                        }
                        $this->setResponseData(['html'=>$html]);
                        $this->sendResponse();
                    }
                    break;

                case 'PRICETAG':
                    if($id=(int)$request->getPost('ID')) {
                        if((int)$request->getPost('ADD')) {
                            PriceTagCollection::getInstance()->add($id);
                        }
                        else {
                            PriceTagCollection::getInstance()->remove($id);
                        }
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

    private function getSnapProgressHtml($snap, $redirectUrl=null)
    {
        $details='#PROGRESS_BAR#';
        if(($snap->getProcessedProducts() > 0) && ($snap->getProcessedProducts() == $snap->getTotalProducts())) {
            $details.='<p>Товары обработаны. Дождитесь, пожалуйста завершения процесса...</p>';
        }
        else {
            $details.='<p>Обработано товаров: ' . $snap->getProcessedProducts() . ' из ' . $snap->getTotalProducts() . '</p>';
        }
        return (new \CAdminMessage([
            'MESSAGE' => 'Создание снимка цен',
            'DETAILS' => $details,
            'HTML'=>true,
            'TYPE'=>'PROGRESS',
            'PROGRESS_TOTAL'=>100,
            'PROGRESS_VALUE'=>$this->getPercent($snap),
            // 'PROGRESS_WIDTH'=>600
        ]))->Show();
    }

    /**
     * Action: создание снимка цен
     *
     * @return void
     */
    private function actionSnap()
    {
        // создаем объект снимка
        if($snap=SnapTable::createSnap()) {
            $redirectUrlData=['ACTION' => 'SNAP_NEXT', 'ID'=>$snap->getId()];
            $this->setResponseData([
                'html'=>$this->getSnapProgressHtml($snap, $this->getUri($redirectUrlData)),
                'data' => $redirectUrlData
            ]);
        }        
    }
    
    /**
     * Action: Следующая итерация создания снимка
     *
     * @return void
     */
    private function actionSnapNext($snapId)
    {
        if($snap=SnapTable::createSnapNext($snapId)) {
            if($snap->getStatus() == Helper::SNAP_STATUS_RUNNED) {
                $redirectUrlData=['ACTION' => 'SNAP_NEXT', 'ID'=>$snap->getId()];
                $this->setResponseData([
                    'html'=>$this->getSnapProgressHtml($snap, $this->getUri($redirectUrlData)),
                    'data' => $redirectUrlData
                ]);
            }
            else {
                $this->setResponseData(['data' => ['ACTION' => 'SNAP_DONE']]);
            }
        }
    }

    private function getSnapProducts($snap, $productIds=[])
    {
        $products=[];

        $filter=['SNAP_ID'=>$snap->getId()];
        if(!empty($productIds)) {
            $filter['=PRODUCT_ID']=$productIds;
        }
        $rs=SnapProductTable::getList([
            'filter'=>$filter,
            'order'=>['PRODUCT_ID'=>'asc', 'OFFER_ID'=>'asc']
        ]);
        while($product=$rs->fetchObject()) {
            $tmp=$product->getProductId();
            $tmpprice=$product->getPrice();

            $productId=(int)$product->getProductId();
            if(empty($products[$productId])) {
                $products[$productId]=[
                    'ID'=>$productId,
                    'PRICE'=>0,
                    'OFFERS'=>[]
                ];
            }

            if($product->getOfferId()) {
                $products[$productId]['OFFERS'][$product->getOfferId()]=[
                    'ID'=>$product->getOfferId(),
                    'PRICE'=>(float)$product->getPrice()
                ];
            }
            else {
                $products[$productId]['PRICE']=(float)$product->getPrice();
            }
        }

        return $products;
    }

    private function getPercent($snap)
    {
        if($snap->getStatus() == Helper::SNAP_STATUS_RUNNED) {
            $percent=(int)($snap->getProcessedProducts() * 100 / $snap->getTotalProducts());
            return $percent < 100 ? $percent : 99;
        }
        else {
            return 100;
        }
    }    
}