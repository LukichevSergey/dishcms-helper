<?php
namespace ecommerce\modules\moysklad\components\helpers;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HTools;
use settings\components\helpers\HSettings;
use ecommerce\modules\order\models\Order;
use MoySklad\Lists\EntityList;
use MoySklad\Components\FilterQuery;
use MoySklad\Components\Specs\QuerySpecs\QuerySpecs;
use MoySklad\Components\Specs\QuerySpecs\Reports\StockReportQuerySpecs;
use MoySklad\Entities\Store;
use MoySklad\Entities\Assortment;
use MoySklad\Entities\Products\Product;
use MoySklad\Entities\Products\Variant;
use MoySklad\Entities\Folders\ProductFolder;
use MoySklad\Entities\Reports\StockReport;
use MoySklad\Entities\Organization;
use MoySklad\Entities\Counterparty;
use MoySklad\Entities\Documents\Orders\CustomerOrder;
use MoySklad\Entities\Documents\Positions\CustomerOrderPosition;
use MoySklad\Components\Fields\MetaField;
use MoySklad\Components\Fields\ImageField;

/**
 * Класс-помощник для модуля "Мой склад"
 *
 */
class HMoySklad
{
    public static $lastError='';
    
    /**
     * Получить модель настроек модуля интеграции с сервисом "Мой Склад"
     * @return \ecommerce\modules\moysklad\models\MoySkladSettings
     */
    public static function settings()
    {
        return HSettings::getById('moysklad_settings');
    }
    
    /**
     * Получить значение параметра настроек модуля интеграции с сервисом "Мой Склад"
     * @param string $attribute имя параметра
     * @return mixed
     */
    public static function param($attribute)
    {
        return static::settings()->$attribute;
    }
    
    /**
     * Получить объект соединения с сервисом "Мой Склад"
     * @return \MoySklad\MoySklad
     */
    public static function sklad()
    {
        try {
            return \MoySklad\MoySklad::getInstance(static::param('login'), static::param('password'));
        }
        catch(\Throwable $e) {
            return null;
        }
    }
    
    /**
     * Получить объект фильтра для запроса в сервис "Мой Склад"
     * @return \MoySklad\Components\FilterQuery
     */
    public static function filter()
    {
        return new FilterQuery();        
    }
    
    /**
     * Получить список групп товаров
     * @param array $query дополнительный критерий выборки. 
     * Подробнее о параметрах QuerySpecs::create().
     * @return \MoySklad\Lists\EntityList
     */
    public static function getProductFolders($query=[])
    {        
        try {
            return ProductFolder::query(static::sklad(), QuerySpecs::create($query))->getList();
        }
        catch(\Throwable $e) {
            return null;
        }
    }
    
    /**
     * Получить список товаров
     * @param array $query дополнительный критерий выборки. 
     * Подробнее о параметрах QuerySpecs::create().
     * @return \MoySklad\Lists\EntityList
     */
    public static function getProductList($query=[])
    {        
        try {
            return Product::query(static::sklad(), QuerySpecs::create($query))->getList();
        }
        catch(\Throwable $e) {
            return null;
        }
    }
    
    /**
     * Получить выборку модификаторов с использованием фильтра
     * @param FilterQuery объект фильтра
     * @param array $query дополнительный критерий выборки.
     * Подробнее о параметрах QuerySpecs::create().
     * @return \MoySklad\Lists\EntityList
     */
    public static function getVariantsByFilter($filter, $query=[])
    {
        static::setQueryLimit($query);
        
        try {
            return Variant::query(static::sklad(), QuerySpecs::create($query))->filter($filter);
        }
        catch(\Throwable $e) {
            return null;
        }
    }
    
    /**
     * Получить модификаторы товара 
     * @param string $productId идентификатор товара
     * @param array $query дополнительный критерий выборки.
     * Подробнее о параметрах QuerySpecs::create().
     * @return \MoySklad\Lists\EntityList
     */
    public static function getVariants($productId, $query=[])
    {
        static::setQueryLimit($query);
        
        try {
            return Variant::query(static::sklad(), QuerySpecs::create($query))
                ->filter(static::filter()->eq('productid', $productId));
        }
        catch(\Throwable $e) {
            return null;
        }
    }
    
    /**
     * Получить модификацию товара по идентификатору
     * @param string $id идентификатор модификации товара в сервисе "Мой Склад"
     * @return \MoySklad\Entities\AbstractEntity
     */
    public static function getVariant($id)
    {
        try {
            return Variant::query(static::sklad())->byId($id);
        }
        catch(\Throwable $e) {
            return null;
        }
    }
    
    /**
     * Получить выборку ассортимента с использованием фильтра
     * @param FilterQuery объект фильтра
     * @param array $query дополнительный критерий выборки. 
     * Подробнее о параметрах QuerySpecs::create().
     * @return \MoySklad\Lists\EntityList
     */
    public static function getAssortmentByFilter($filter, $query=[])
    {
        try {
            return Assortment::query(static::sklad(), QuerySpecs::create($query))->filter($filter);
        }
        catch(\Throwable $e) {
            return null;
        }
    }
    
    /**
     * Получить выборку ассортимента с использованием фильтра
     * @param array $query дополнительный критерий выборки. 
     * Подробнее о параметрах QuerySpecs::create().
     * @return \MoySklad\Lists\EntityList
     */
    public static function getAssortmentList($query=[])
    {
        try {
            return Assortment::query(static::sklad(), QuerySpecs::create($query))->getList();
        }
        catch(\Throwable $e) {
            return null;
        }
    }
    
    /**
     * Получить выборку товаров с использованием фильтра
    * @param FilterQuery объект фильтра
     * @param array $query дополнительный критерий выборки. 
     * Подробнее о параметрах QuerySpecs::create().
     * @return \MoySklad\Lists\EntityList
     */
    public static function getProductsByFilter($filter, $query=[])
    {
        try {
            return Product::query(static::sklad(), QuerySpecs::create($query))->filter($filter);
        }
        catch(\Throwable $e) {
            return null;
        }
    }
    
    /**
     * Получить товар по идентификатору
     * @param string $id идентификатор товара в сервисе "Мой Склад"
     * @return \MoySklad\Entities\AbstractEntity
     */
    public static function getProduct($id)
    {
        try {
            return Product::query(static::sklad())->byId($id);
        }
        catch(\Throwable $e) {
            return null;
        }
    }
    
    public static function getProductUuId($product)
    {
        if($meta=$product->getMeta()) {
            return preg_replace('#^.*?([^=]+)$#', '$1', $meta->uuidHref);
        }
        
        return null;
    }
    
    /**
     * Получить цену товара.
     * Если в настройках не определен тип цены, будет возвращена максимальная цена.
     * @param \MoySklad\Entities\Products\Product $product объект товара
     * @param bool $returnType возвращать тип цены
     * @return float|[] если передан параметр $returnType=true, будет 
     * возвращен массив вида array("price"=>float, "type"=>string)
     */
    public static function getSalePrice($product, $returnType=false)
    {
        $price=0;
        $type=null;
        
        if(!empty($product->salePrices)) {
            $priceType=trim((string)static::settings()->price_type);
            foreach($product->salePrices as $sp) {
                if($priceType) {
                    if($priceType == (string)$sp->priceType) {
                        $price=(float)$sp->value;
                        $type=$priceType;
                    }
                }
                elseif($price < $sp->value) {
                    $price=(float)$sp->value;
                    $type=(string)$sp->priceType;
                }
            }            
        }
        
        if($returnType) {
            return [
                'price'=>$price/100,
                'type'=>$type
            ];
        }
        else {
            return $price/100;
        }
    }
    
    /**
     * Получить список складов
     * @param array $query дополнительный критерий выборки. 
     * Подробнее о параметрах QuerySpecs::create().
     * @return \MoySklad\Lists\EntityList
     */
    public static function getStoreList($query=[])
    {
        try {
            return Store::query(static::sklad(), QuerySpecs::create($query))->getList();
        }
        catch(\Throwable $e) {
            return null;
        }
    }
    
    /**
     * Получить список складов вида array(id=>name)
     * @param array $query дополнительный критерий выборки. 
     * Подробнее о параметрах QuerySpecs::create().
     * @return [] список складов вида array(id=>name)
     */
    public static function getStoreListData($query=[])
    {
        $data=[];
            
        try {
            static::getStoreList($query)->each(function($store, $key) use (&$data) {
                $data[$store->id]=$store->name;
            });
        }
        catch(\Throwable $e) {
        }
        
        return $data;
    }
    
    /**
     * Получить список организаций
     * @param array $query дополнительный критерий выборки. 
     * Подробнее о параметрах QuerySpecs::create().
     * @return \MoySklad\Lists\EntityList
     */
    public static function getOrganizationList($query=[])
    {
        try {
            return Organization::query(static::sklad(), QuerySpecs::create($query))->getList();
        }
        catch(\Throwable $e) {
            return null;
        }
    }
    
    /**
     * Получить список организаций вида array(id=>name)
     * @param array $query дополнительный критерий выборки. 
     * Подробнее о параметрах QuerySpecs::create().
     * @return [] список организаций вида array(id=>name)
     */
    public static function getOrganizationListData($query=[])
    {
        $data=[];
        
        try {
            static::getOrganizationList($query)->each(function($organization, $key) use (&$data) {
                $data[$organization->id]=$organization->name;
            });
        }
        catch(\Throwable $e) {
        }
        
        return $data;
    }
    
    /**
     * Получить организацию по идентификатору
     * @param string $id идентификатор организации в сервисе "Мой Склад"
     * @return \MoySklad\Entities\AbstractEntity
     */
    public static function getOrganizationById($id)
    {
        try {
            return Organization::query(static::sklad())->byId($id);
        }
        catch(\Throwable $e) {
            return null;
        }
    }
    
    /**
     * Получить список остатоков товара для выбранного склада.
     * @param \MoySklad\Lists\EntityList $products товары для которых 
     * получаются остатки
     * * @param array $query дополнительный критерий выборки. 
     * Подробнее о параметрах QuerySpecs::create().
     * @return [] массив остатков вида array(productId=>quantity)
     */
    public static function getQuantities($products, $query=[])
    {
        $quantities=[];
        
        try {
            if(static::settings()->store) {
                static::setQueryLimit($query);
                
                $query['store.id']=static::settings()->store;
                $query['stockMode']='all';
                $query['product.id']=[];
                $products->each(function($product, $key) use (&$query) {
                    $query['product.id'][]=$product->id;
                });
                
                $query['product.id']=implode('&product.id=', $query['product.id']);
                
                $sr=StockReport::all(static::sklad(), StockReportQuerySpecs::create($query));
                if(!empty($sr->rows)) {
                    foreach($sr->rows as $product) {
                        $meta=new MetaField($product->meta);
                        $id=preg_replace('/\?.+$/', '', $meta->getId());
                        $quantities[$id]=$product->quantity;
                    }
                }
            }
        }
        catch(\Throwable $e) {
        }
        
        return $quantities;
    }
    
    /**
     * Устанавливает лимит для запросов 
     * @param [] $query дополнительный критерий выборки. 
     * Подробнее о параметрах QuerySpecs::create().
     * @param int $default значение лимита по умолчанию.
     * По умолчанию 100000.
     */
    public static function setQueryLimit(&$query, $default=100000)
    {
        $query['limit']=A::get($query, 'limit', $default);
    }
    
    /**
     * Загрузка изображения
     * @param ImageField $image объект изображения
     * @param string $filename имя файла
     */
    public static function download($image, $filename)
    { 
        if($image instanceof ImageField) {
            $fp=fopen ($filename, 'w+');
            
            $url=$image->meta->href;
            $ch=curl_init();
            
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_USERPWD, static::param('login') . ':' . static::param('password'));
            curl_setopt($ch, CURLOPT_HEADER,0);            
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_setopt($ch, CURLOPT_URL, $url);
            
            curl_exec($ch);
            
            curl_close($ch);
            fclose($fp);
        }
    }
    
    /**
     * Получить список контрагентов с использовнием фильтра
     * @param FilterQuery объект фильтра
     * @param array $query дополнительный критерий выборки.
     * Подробнее о параметрах QuerySpecs::create().
     * @return \MoySklad\Lists\EntityList
     */
    public static function getCounterPartyByFilter($filter, $query=[])
    {
        static::setQueryLimit($query);
        
        try {
            return Counterparty::query(static::sklad(), QuerySpecs::create($query))->filter($filter);
        }
        catch(\Throwable $e) {
            return null;
        }
    }
    
    /**
     * Нормализация номера телефона
     * @param string $phone номер телефона
     * @return string
     */
    public static function normalizePhone($phone)
    {
        return preg_replace('/^\+?[78]/', '+7', HTools::normalizePhone($phone));
    }
    
    /**
     * Получить контрагента по номеру телефона
     * @param string $phone номер телефона
     * @return Counterparty|null
     */
    public static function getCounterPartyByPhone($phone)
    {
        $counterParty=null;
        
        $filter=static::filter();
        $filter->eq('phone', static::normalizePhone($phone));
        
        if($counterParties=static::getCounterPartyByFilter($filter)) {
            if($counterParties->count() > 0) {
                $counterParty=$counterParties->get(0);
            }
        }
        
        return $counterParty;
    }
    
    /**
     * Выгрузка заказа в Мой Склад.
     * Для выгрузки заказов обязательно должена быть задана в 
     * настройках модуля организация для которой выгружаются заказы.
     * @param Order $order объект заказа
     * @return CustomerOrder|false
     */
    public static function createOrder($order)
    {
        try {
            Y::module('ecommerce.moysklad');        
            if(static::param('organization')) {
                if(($order instanceof Order) && !$order->moysklad_id) {
                    if($organization=static::getOrganizationById(static::param('organization'))) {
                        $customer=$order->getCustomerData();
                        $phone=static::normalizePhone(A::rget($customer, 'phone.value', ''));
                        
                        $counterparty=static::getCounterPartyByPhone($phone);
                        if(!$counterparty) {
                            // создаем нового контрагента
                            $counterparty=(new Counterparty(static::sklad(), [
                                'name'=>A::rget($customer, 'name.value', ''),
                                'email'=>A::rget($customer, 'email.value', ''),
                                'phone'=>$phone,
                            ]))->create();                    
                        }
                        
                        // получаем товары из заказа из "Мой Склад"
                        $filter=static::filter();
                        $products=[];
                        $orderItems=$order->getOrderData();
                        foreach($orderItems as $hash=>$product) {
                            $productMoySkaldId=A::rget($product, 'moysklad_id.value');
                            if(empty($productMoySkaldId)) {
                                static::$lastError='Один из товаров в заказе не имеет соотвествия в сервисе "Мой Склад"';
                                return false;
                            }
                            $products[$productMoySkaldId]=['order'=>$product, 'moysklad'=>null];
                            $filter->eq('id', $productMoySkaldId);
                        }
                        
                        $assortiments=static::getProductsByFilter($filter);
                        $assortiments->each(function($product, $key) use (&$products) {
                            if(isset($products[$product->id])) {
                                $products[$product->id]['moysklad']=$product;
                            }
                        });
                        
                        $preVariants=array_filter($products, function($item){ return empty($item['moysklad']); });
                        if(!empty($preVariants)) {
                            foreach($preVariants as $moyskladId=>$preVariant) {
                                $variant=static::getVariant($moyskladId.'aaa');
                                if(!$variant) {
                                    static::$lastError='Один из товаров в заказе не имеет соотвествия в сервисе "Мой Склад"';
                                    return false;
                                }
                                $product=new Product(HMoySklad::sklad(), [
                                    'meta'=>$variant->getMeta()
                                ]);
                                $products[$moyskladId]['moysklad']=$product;
                            }
                        }
                        
                        // @todo добавить кнопку ручной выгрузки заказа в список заказов, 
                        // если не удалось его выгрузить при создании
                        
                        $positions=new EntityList(static::sklad());                    
                        
                        $items=[];
                        foreach($products as $product) {
                            $product['moysklad']->quantity=(float)$product['order']['count']['value'];
                            $product['moysklad']->price=(float)$product['order']['price']['value'] * 100;
                            $items[]=$product['moysklad'];
                        }
                        $positions->replaceItems($items);
                        
                        $msOrder = new CustomerOrder(static::sklad());
                        
                        $msOrder->description='Адрес доставки: ' . A::rget($customer, 'address.value', '') . "\n"
                            . 'Комментарий к заказу: ' . A::rget($customer, 'comment.value', '');
                        
                        
                        $newOrder=$msOrder->buildCreation()
                            ->addCounterparty($counterparty)
                            ->addOrganization($organization)
                            ->addPositionList($positions)
                            ->execute();
                        
                        if($newOrder->id) {
                            $order->moysklad_id=$newOrder->id;
                            $order->update(['moysklad_id']);
                            
                            return $newOrder;
                        }                            
                    }
                }
            }
        }
        catch(\Throwable $e) {
            return false;
        }
        
        return false;
    }
}