<?php
namespace ecommerce\modules\moysklad\models;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HRequest as R;
use common\components\helpers\HDb;
use common\components\helpers\HHash;
use common\components\helpers\HTools;
use ecommerce\modules\moysklad\components\helpers\HMoySklad;
use MoySklad\Components\Fields\ImageField;

class Import 
{
    /**
     * Режим завершения обмена
     * @var integer
     */
    const MODE_DONE=1;
    
    /**
     * Режим обмена категорий
     * @var integer
     */
    const MODE_CATEGORY=100;
    
    /**
     * Режим обмена товаров
     * @var integer
     */
    const MODE_PRODUCTS=200;   
    
    /**
     * Режим обмена "Остатки"
     * @var integer
     */
    const EXCHANGE_MODE_RESTS=1;
    
    /**
     * Режим обмена "Полная выгрузка"
     * @var integer
     */
    const EXCHANGE_MODE_FULL=200;
    
    /**
     * Получить режим обмена
     * @return int
     */
    protected static function getExchangeMode()
    {
        switch (R::post('mode')) {
            case 'full':
                return self::EXCHANGE_MODE_FULL;
                break;
                
            default:
                return self::EXCHANGE_MODE_RESTS;
        }
    }
    
    /**
     * Получить дополнительный ключ шифрования
     * @return string
     */
    public static function secure()
    {
        return (string)HMoySklad::settings()->secure;
    }
    
    /**
     * Запуск обмена с сервисом "Мой Склад"
     * @param \common\ext\iterator\models\Process $iteratorProcess
     * @return []
     */
    public static function start($iteratorProcess)
    {
        Y::module('ecommerce.moysklad');
        
        return ['limit'=>(int)HMoySklad::settings()->limit, 'mode'=>(int)static::getExchangeMode()];
    }
    
    /**
     * Обработка следующего шага обмена с сервисом "Мой Склад"
     * @param \common\ext\iterator\models\Process $iteratorProcess
     * @return integer процент завершенности процесса обмена
     */
    public static function next($iteratorProcess)
    {
        $percent=0;
        
        Y::module('ecommerce.moysklad');
        
        $limit=(int)$iteratorProcess->getDataParam('limit', HMoySklad::settings()->limit);
        $iteration=(int)$iteratorProcess->getParam('iteration', 1);
        
        $mode=$iteratorProcess->getParam('mode', null, true);
        switch($mode) {
            case self::MODE_DONE:
                $message='Импорт завершен';
                $percent=100;
                break;
                
            case self::MODE_PRODUCTS:
                switch((int)$iteratorProcess->getDataParam('mode', self::EXCHANGE_MODE_RESTS)) {
                    case self::EXCHANGE_MODE_FULL:
                        $result=static::importProducts([
                            'offset'=>(($iteration - 1) * $limit),
                            'maxResults'=>$limit,
                        ]);
                        break;
                        
                    case self::EXCHANGE_MODE_RESTS:
                        $result=static::importProductRests([
                            'offset'=>(($iteration - 1) * $limit),
                            'maxResults'=>$limit,
                        ]);
                        break;
                    
                    default:
                        throw new \CException('Неверный режим обмена');
                }
                
                if($result['count'] >= $result['size']) {
                    $iteratorProcess->setParam('mode', self::MODE_DONE, true);
                    
                    HDb::query('UPDATE `product` SET `hidden`=1 WHERE `exchange_exists`=0 OR ISNULL(`exchange_exists`)');
                    HDb::query('UPDATE `product` SET `exchange_exists`=0');
                    
                    $message='Импорт товаров успешно завершен!';
                    $percent=100;
                }
                else {
                    $message="Выполняется импорт товаров {$result['count']} из {$result['size']}";
                    $percent=50 + floor(($result['count'] * 100 / $result['size']) / 2);
                }
                break;
                
            case self::MODE_CATEGORY:
            default:
                switch((int)$iteratorProcess->getDataParam('mode', self::EXCHANGE_MODE_RESTS)) {
                    case self::EXCHANGE_MODE_FULL:
                        $result=static::importCategories([
                            'offset'=>(($iteration - 1) * $limit),
                            'maxResults'=>$limit,
                        ]);
                        
                        if($result['count'] >= $result['size']) {
                            $iteratorProcess->setParam('mode', self::MODE_PRODUCTS, true);
                            
                            $message='Импорт категорий завершен. Ожидается импорт товаров...';
                            $percent=50;
                            $iteration=0;
                        }
                        else {
                            $message="Выполняется импорт категорий {$result['count']} из {$result['size']}";
                            $percent=floor(($result['count'] * 100 / $result['size']) / 2);
                        }
                        break;
                        
                    default:
                        $iteratorProcess->setParam('mode', self::MODE_PRODUCTS, true);
                        $message='Ожидается импорт товаров...';
                        $percent=10;
                        $iteration=0;
                }
        }
        
        $iteratorProcess->setParam('message', $message);
        $iteratorProcess->setParam('percent', $percent);
        $iteratorProcess->setParam('iteration', ++$iteration);
        
        return $percent;
    }
    
    /**
     * Импорт категорий
     * @param array $query дополнительный критерий выборки
     * @return [] массив вида [
     *     'count' => (integer) количество обработанных категорий, 
     *     'size' => (integer) общее количество категорий
     *  ],
     */
    protected static function importCategories($query=[])
    {
        $list=HMoySklad::getProductFolders($query);
        if(!($list instanceof \MoySklad\Lists\EntityList)) {
            throw new \CException('Не удалось соединиться с сервисом "Мой Склад"');
        }
        
        $list->each(function($item, $key) {
            if(!$item->archived) {
                if($category=\Category::model()->findByAttributes(['moysklad_id'=>$item->id])) {
                    if(HMoySklad::settings()->normalize_categories) {
                        if($item->pathName) {
                            $parentFolder=$item->loadRelation('productFolder');
                            if($parent=\Category::model()->findByAttributes(['moysklad_id'=>$parentFolder->id])) {
                                $category->moveAsLast($parent);
                            }
                            else {
                                $paths=explode('/', $item->pathName);
                                $parentTitle=trim(array_pop($paths));
                                if($parent=\Category::model()->findByAttributes(['title'=>$parentTitle])) {
                                    $category->moveAsLast($parent);
                                }
                            }
                        }
                    }
                }
                else {
                    $category=null;
                    
                    if($item->pathName) {
                        $parentFolder=$item->loadRelation('productFolder');
                        $parent=\Category::model()->findByAttributes(['moysklad_id'=>$parentFolder->id]);
                        if(!$parent) {
                            $paths=explode('/', $item->pathName);
                            $parentTitle=trim(array_pop($paths));
                            $parent=\Category::model()->findByAttributes(['title'=>$parentTitle]);
                        }
                    }
                    
                    if($parent) {
                        $category=$parent->children()->findByAttributes(['title'=>trim($item->name)]);
                    }
                    else {
                        $category=\Category::model()->findByAttributes(['title'=>trim($item->name)]);
                    }
                    
                    if(!empty($category)) {
                        $category->moysklad_id=$item->id;
                        $category->saveNode();
                        if(!empty($parent)) {
                            $category->moveAsLast($parent);
                        }
                    }
                    else {
                        $category=new \Category;                        
                        $category->moysklad_id=$item->id;
                        $category->title=trim($item->name);
                        $category->alias=HTools::alias($category->title);
                        
                        if(\Category::model()->findByAttributes(['alias'=>$category->alias])) {
                            $category->alias.=HHash::u('-');
                        }                        
                        
                        if(!empty($parent)) {
                            $category->appendTo($parent);
                        }
                        else {
                            $category->saveNode();
                        }
                    }
                }
            }
        });
        
        return [
            'count' => ($list->getMeta()->offset + $list->count()), 
            'size' => $list->getMeta()->size
        ];
    }
    
    /**
     * Импорт товаров (полное обновление)
     * Будут выгружены только новые товары и новые торговые предложения.
     * @param array $query дополнительный критерий выборки
     * @return [] массив вида [
     *     'count' => (integer) количество обработанных товаров,
     *     'size' => (integer) общее количество товаров
     *  ],
     */
    protected static function importProducts($query=[])
    {
        $products=HMoySklad::getProductList($query);
        if(!($products instanceof \MoySklad\Lists\EntityList)) {
            throw new \CException('Не удалось соединиться с сервисом "Мой Склад"');
        }
       
        $aliases=\Product::model()->listData('moysklad_id', null, null, 'alias');
        $categories=\Category::model()->findAll(['index'=>'moysklad_id']);
        $quantities=HMoySklad::getQuantities($products);
        $data=[];
        $products->each(function($product, $key) use (&$data, $categories, $aliases, $quantities) {
            try {
                $parentFolder=$product->loadRelation('productFolder');
                if(isset($categories[$parentFolder->id])) {
                    
                    $mainImageFile='';
                    if($product->image instanceof ImageField) {
                        $mainImageFile=md5($product->id) . '.' . pathinfo($product->image->filename, PATHINFO_EXTENSION);
                        HMoySklad::download($product->image, \Yii::getPathOfAlias('webroot.images.product')."/{$mainImageFile}");
                    }
                
                    $alias=HTools::alias($product->name);
                    if(!empty($aliases[$alias])) {
                        $alias=$alias . HHash::u('-');
                    }
                    
                    if($productUuId=HMoySklad::getProductUuId($product)) {
                        $uom=$product->loadRelation('uom');
                        $price=HMoySklad::getSalePrice($product, true);
                        $item=[
                            'moysklad_id'=>$productUuId,
                            'category_id'=>$categories[$parentFolder->id]->id,
                            'code'=>$product->code,
                            'title'=>$product->name,
                            'alias'=>$alias,
                            'main_image'=>$mainImageFile,
                            'description'=>$product->description,
                            'quantity'=>A::get($quantities, $product->id, 0),
                            'price'=>$price['price'],
                            'unit'=>$uom ? $uom->name : '',
                            'exchange_exists'=>1,
                            'hidden'=>0,
                            'pricetype'=>$price['type'],
                            'update_time'=>new \CDbExpression('NOW()')
                        ];
                        
                        $variants=[];
                        if((int)$product->modificationsCount > 0) {
                            $offers=[];                        
                            if($prod=\Product::model()->findByAttributes(['moysklad_id'=>$productUuId])) {
                                $offers=$prod->offersBehavior->get(false);
                            }
                            
                            $variantOffers=[];
                            $variants=HMoySklad::getVariants($product->id);
                            $variantQuantities=HMoySklad::getQuantities($variants); 
                            $variants->each(function($variant, $key) use (&$variantOffers, &$item, $variantQuantities) {
                                static $variantIdx=0;
                                $variantOffers[$variantIdx]=['active'=>1];
                                $props=$variant->characteristics;
                                $propN=1;
                                foreach($props as $prop) {
                                    if($prop->name != 'COLOR') {
                                        $item["offers_prop{$propN}_title"]=$prop->name;
                                        $variantOffers[$variantIdx]["prop{$propN}"]=$prop->value;
                                        $propN++;
                                    }                                
                                    if($propN > 3) {
                                        break;
                                    }
                                }
                                if($propN < 4) {
                                    for($i=$propN; $i<4; $i++) {
                                        $item["offers_prop{$i}_title"]='';
                                        $variantOffers[$variantIdx]["prop{$i}"]='';
                                    }
                                }
                                $variantOffers[$variantIdx]['quantity']=A::get($variantQuantities, $variant->id, 0);
                                // @todo получение изображения торгового предложения (будет доступно с версии API 1.2)
                                $variantIdx++;
                            });
                            
                            if(!empty($offers) && !empty($variantOffers)) {
                                foreach($variantOffers as $variantOffer) {
                                    $founded=false;
                                    foreach($offers as $offer) {
                                        if(($variantOffer['prop1']==$offer['prop1']) && ($variantOffer['prop2']==$offer['prop2']) && ($variantOffer['prop3']==$offer['prop3'])) {
                                            $founded=true;
                                        }
                                    }
                                    if(!$founded) {
                                        $offers[]=$variantOffer;
                                    }
                                }
                            }
                            elseif(!empty($variantOffers)) {
                                $offers=$variantOffers;
                            }
                            $item['offers']=json_encode($offers);
                        }
                        
                        $data[]=$item;
                    }
                }
            }
            catch(\Exception $e) {
            }
        });
        
        if(!empty($data)) {
            HDb::insert(\Product::model()->tableName(), $data, '
                `category_id`=VALUES(`category_id`), 
                `exchange_exists`=1,
                `hidden`=0,
                `offers`=VALUES(`offers`)' 
                /*
                ,`title`=VALUES(`title`), 
                `main_image`=VALUES(`main_image`), 
                `price`=VALUES(`price`), 
                `quantity`=VALUES(`quantity`), 
                `unit`=VALUES(`unit`), 
                `description`=VALUES(`description`), 
                `offers_prop1_title`=VALUES(`offers_prop1_title`),
                `offers_prop2_title`=VALUES(`offers_prop2_title`),
                `offers_prop3_title`=VALUES(`offers_prop3_title`),
                `pricetype`=VALUES(`pricetype`),
                `update_time`=NOW()
                '*/, [
                'main_image'=>'',
                'price'=>0,
                'quantity'=>0,
                'description'=>'',
                'offers_prop1_type'=>2,
                'offers_prop2_type'=>2,
                'offers_prop3_type'=>2,
                'offers_prop1_title'=>'',
                'offers_prop2_title'=>'',
                'offers_prop3_title'=>'',
                'offers'=>'',
                'unit'=>'',
                'exchange_exists'=>1,
                'hidden'=>0,
                'pricetype'=>''
            ]);
        }
        
        return [
            'count' => ($products->getMeta()->offset + $products->count()),
            'size' => $products->getMeta()->size
        ];
    }
    
    /**
     * Импорт товаров (частичное обновление)
     * @param array $query дополнительный критерий выборки
     * @return [] массив вида [
     *     'count' => (integer) количество обработанных товаров,
     *     'size' => (integer) общее количество товаров
     *  ],
     */
    protected static function importProductRests($query=[])
    {
        $products=HMoySklad::getProductList($query);
        if(!($products instanceof \MoySklad\Lists\EntityList)) {
            throw new \CException('Не удалось соединиться с сервисом "Мой Склад"');
        }
       
        $existsProducts=HDb::queryAll('SELECT `moysklad_id`, `offers` FROM `product` WHERE LENGTH(`moysklad_id`) > 0');
        if(!empty($existsProducts)) {
            $_existsProducts=[];
            foreach($existsProducts as $prod) {
                $_existsProducts[$prod['moysklad_id']]=json_decode($prod['offers'], true);
            }
            $existsProducts=$_existsProducts;
            
            $quantities=HMoySklad::getQuantities($products);
            $data=[];
            $products->each(function($product, $key) use (&$data, $categories, $aliases, $quantities, $existsProducts) {
                try {
                    if($productUuId=HMoySklad::getProductUuId($product)) {
                        if(array_key_exists($productUuId, $existsProducts)) {
                            $uom=$product->loadRelation('uom');
                            $price=HMoySklad::getSalePrice($product, true);
                            $item=[
                                'moysklad_id'=>$productUuId,
                                'quantity'=>A::get($quantities, $product->id, 0),
                                'price'=>$price['price'],
                                'unit'=>$uom ? $uom->name : '',
                                'exchange_exists'=>1,
                                'hidden'=>0,
                                'pricetype'=>$price['type'],
                                'update_time'=>new \CDbExpression('NOW()')
                            ];
                            
                            $variants=[];
                            if(!empty($existsProducts[$productUuId])) {
                                $offers=$existsProducts[$productUuId];
                                if((int)$product->modificationsCount > 0) {
                                    $variantOffers=[];
                                    $variants=HMoySklad::getVariants($product->id);
                                    $variantQuantities=HMoySklad::getQuantities($variants); 
                                    $variants->each(function($variant, $key) use (&$variantOffers, &$item, $variantQuantities) {
                                        static $variantIdx=0;
                                        $variantOffers[$variantIdx]=['active'=>1];
                                        $props=$variant->characteristics;
                                        $propN=1;
                                        foreach($props as $prop) {
                                            if($prop->name != 'COLOR') {
                                                $item["offers_prop{$propN}_title"]=$prop->name;
                                                $variantOffers[$variantIdx]["prop{$propN}"]=$prop->value;
                                                $propN++;
                                            }                                
                                            if($propN > 3) {
                                                break;
                                            }
                                        }
                                        if($propN < 4) {
                                            for($i=$propN; $i<4; $i++) {
                                                $item["offers_prop{$i}_title"]='';
                                                $variantOffers[$variantIdx]["prop{$i}"]='';
                                            }
                                        }
                                        $variantOffers[$variantIdx]['quantity']=A::get($variantQuantities, $variant->id, 0);
                                        // @todo получение изображения торгового предложения (будет доступно с версии API 1.2)
                                        $variantIdx++;
                                    });
                                
                                    if(!empty($offers) && !empty($variantOffers)) {
                                        foreach($variantOffers as $variantOffer) {
                                            $founded=false;
                                            foreach($offers as $idx=>$offer) {
                                                if(($variantOffer['prop1']==$offer['prop1']) && ($variantOffer['prop2']==$offer['prop2']) && ($variantOffer['prop3']==$offer['prop3'])) {
                                                    $offers[$idx]['quantity']=$variantOffer['quantity'];
                                                    break;
                                                }
                                            }
                                        }
                                    }
                                }
                                    
                                if(is_array($offers)) {
                                    $item['offers']=json_encode($offers);
                                }
                                else {
                                    $item['offers']='';
                                }
                            }
                            
                            $data[]=$item;
                        }
                    }
                }
                catch(\Exception $e) {
                }
            });
        
            if(!empty($data)) {
                HDb::insert(\Product::model()->tableName(), $data, '
                    `price`=VALUES(`price`), 
                    `quantity`=VALUES(`quantity`), 
                    `unit`=VALUES(`unit`), 
                    `offers`=VALUES(`offers`),
                    `pricetype`=VALUES(`pricetype`),
                    `exchange_exists`=1,
                    `hidden`=0,
                    `update_time`=NOW()'
                    /* 
                    `offers_prop1_title`=VALUES(`offers_prop1_title`),
                    `offers_prop2_title`=VALUES(`offers_prop2_title`),
                    `offers_prop3_title`=VALUES(`offers_prop3_title`),
                    '*/, [
                    'price'=>0,
                    'quantity'=>0,
                    'offers_prop1_type'=>2,
                    'offers_prop2_type'=>2,
                    'offers_prop3_type'=>2,
                    'offers_prop1_title'=>'',
                    'offers_prop2_title'=>'',
                    'offers_prop3_title'=>'',
                    'offers'=>'',
                    'unit'=>'',
                    'exchange_exists'=>1,
                    'hidden'=>1,
                    'pricetype'=>''
                ]);
            }
        }
        
        return [
            'count' => ($products->getMeta()->offset + $products->count()),
            'size' => $products->getMeta()->size
        ];
    }
}