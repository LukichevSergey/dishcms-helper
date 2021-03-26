<?php
namespace Kontur\CheckPrice;

use Bitrix\Main;
use Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Query\Join;
use Bitrix\Main\ORM\Fields\Relations\OneToMany;
use Bitrix\Main\ORM\Fields\Relations\Reference;
use Bitrix\Iblock\ElementTable;
use Bitrix\Catalog\PriceTable;
use Kontur\CheckPrice\SnapProductTable;

Loc::loadMessages(__FILE__);

class SnapTable extends Main\Entity\DataManager
{
    public static function getFilePath()
    {
        return __FILE__;
    }

    public static function getTableName()
    {
        return 'kontur_checkprice_snaps';
    }

    public static function getMap()
    {
        return [
            'ID' => new Main\Entity\IntegerField('ID', [
				'primary' => true,
				'autocomplete' => true,
                'title' => 'Идентификатор снимка цен'
			]),
            'TITLE' => new Main\Entity\StringField('TITLE', [
				'title' => 'Заголовок снимка'
			]),
            'CREATE_TIME' => new Main\Entity\DatetimeField('CREATE_TIME', [
				'default_value' => function(){ return new Main\Type\DateTime(); },
                'title' => 'Время создания снимка цен',
            ]),
            'ITEMS_IBLOCK_ID' => new Main\Entity\IntegerField('ITEMS_IBLOCK_ID', [
				'required' => true,
                'title' => 'Идентификатор инфоблока товаров'
            ]),
            'OFFERS_IBLOCK_ID' => new Main\Entity\IntegerField('OFFERS_IBLOCK_ID', [
				'required' => true,
                'title' => 'Идентификатор инфоблока торговых предложений'
            ]),
            'TOTAL_PRODUCTS' => new Main\Entity\IntegerField('TOTAL_PRODUCTS', [
                'title' => 'Количество товаров'
            ]),
            'PROCESSED_PRODUCTS' => new Main\Entity\IntegerField('PROCESSED_PRODUCTS', [
                'default_value' => 0,
                'title' => 'Обработанное количество товаров'
            ]),
            'STATUS' => new Main\Entity\IntegerField('STATUS', [
                'title' => 'Статус'
            ]),
            (new OneToMany('PRODUCTS', SnapProductTable::class, 'SNAP'))
        ];
    }

    /**
     * Получение снимка по идентификатору
     *
     * @param int $id идентификатор снимка
     * @param [] $parameters дополнительные параметры для запроса
     * 
     * @return SnapTable
     */
    public static function getSnapById($id, $withProducts=false)
    {
        if(!empty($id)) {
            $parameters=[];
            
            if($withProducts) {
                $parameters=['select'=>['ID', 'PRODUCTS']];
            }

            return static::getByPrimary($id, $parameters)->fetchObject();
        }

        return null;
    }

    /**
     * Получение последнего незавершенного снимка
     *
     * @return SnapTable
     */
    public static function getLastRunnedSnap()
    {
        return static::getList([
            'filter'=>['STATUS'=>Helper::SNAP_STATUS_RUNNED],
            'order'=>['CREATE_TIME'=>'DESC'],
            'limit'=>1
        ])->fetchObject();
    }

    /**
     * Создание объекта снимка
     *
     * @return SnapTable|null
     */
    public static function createSnap()
    {
        if(Helper::checkAccessByCreateSnap(true)) {
            $newSnap = static::createObject();
            $newSnap->setTitle(date('d.m.Y H:i'));
            $newSnap->setItemsIblockId(Helper::getItemsIblockId());
            $newSnap->setOffersIblockId(Helper::getOffersIblockId());
            $newSnap->setTotalProducts(static::getTotalProducts());
            $newSnap->setStatus(Helper::SNAP_STATUS_RUNNED);
            
            if($newSnap->save()) {
                return $newSnap;
            }
        }

        return null;
    }

    /**
     * Получить общее количество товаров
     *
     * @return integer
     */
    public static function getTotalProducts()
    {
        return (int)ElementTable::getCount([
            'IBLOCK_ID'=>Helper::getItemsIblockId()
        ]);
    }

    /**
     * Action: Следующая итерация создания снимка
     *
     * @param int $snapId идентификатор снимка
     * 
     * @return void
     */
    public static function createSnapNext($snapId)
    {
        if(Helper::checkAccessByCreateSnap(true)) {
            if($snap=static::getSnapById($snapId)) {
                return static::createSnapNextBySnap($snap);
            }
        }

        return null;
    }

    public static function createSnapNextBySnap($snap)
    {
        if(Helper::checkAccessByCreateSnap(true)) {
            if($snap->getStatus() == Helper::SNAP_STATUS_RUNNED) {
                $products=static::getProducts($snap->getProcessedProducts());

                static::setProducts($snap, $products);
            }
        }

        return $snap;
    }

    /**
     * Получение товаров с торговыми предложениями и ценами
     *
     * @param int $offset количество товаров, с которого начинать 
     * выборку. По умолчанию 0 (нуль).
     * 
     * @return []
     */
    protected static function getProducts($offset=0)
    {
        // @var [] $products массив товаров вида array(productId=>array(
        //      "ID",
        //      "PRICE",
        //      "OFFERS" => array(
        //          offerId => array("ID"=>offerId, "PRICE"=>price),
        //      ),
        // ))
        $products=[];

        // @var [] $ids идентификаторы товаров вида array(offerId=>productId, productId=>productId)
        $ids=[];

        // получение товаров
        $rs=ElementTable::getList([
            'filter'=>['IBLOCK_ID'=>Helper::getItemsIblockId()],
            'select'=>['ID'],
            'limit'=>Helper::getSnapLimit(),
            'offset'=>$offset
        ]);
        while($product=$rs->fetch()) {
            $id=(int)$product['ID'];
            $ids[$id]=$id;
            $products[$id]=$product;
            // $products[$id]['PRICE']=0; // для получения цен без скидок
            $products[$id]['PRICE']=Helper::getOptimalPrice($id);
            $products[$id]['OFFERS']=[];
        }

        if(!empty($products)) {
            $ids=array_combine(array_keys($products), array_keys($products));
            // получение торговых предложений для товаров
            $offers=\CCatalogSKU::getOffersList($ids);
            if(!empty($offers)) {
                foreach($offers as $productId=>$productOffers) {
                    unset($ids[(int)$productId]);
                    foreach($productOffers as $offerId=>$offer) {
                        $ids[(int)$offerId]=(int)$productId;
                        $products[(int)$productId]['OFFERS'][(int)$offerId]=[
                            'ID'=>$offerId,
                            // 'PRICE'=>0 // для получения цен без скидок
                            'PRICE'=>Helper::getOptimalPrice($offerId)
                        ];
                    }
                }
            }

            // получение цен (без учета скидок)
            /*
            $rs=PriceTable::getList([
                'filter'=>['PRODUCT_ID'=>array_keys($ids), 'CATALOG_GROUP_ID'=>Helper::getCatalogGroupId()],
                'select'=>['PRODUCT_ID', 'PRICE'],
                'order'=>['PRICE'=>'asc']
            ]);
            while($price=$rs->fetch()) {
                $offerId=(int)$price['PRODUCT_ID'];
                $productId=$ids[$offerId];
                if($productId === $offerId) {
                    // товар без торговых предложений
                    $products[$productId]['PRICE']=$price['PRICE'];
                }
                else {
                    // торговое предложение
                    $products[$productId]['OFFERS'][$offerId]['PRICE']=$price['PRICE'];
                }
            }
            /**/
        }

        return $products;
    }

    protected static function setProducts($snap, $products)
    {
        if(empty($products)) {
            $snap->setTotalProducts($snap->getProcessedProducts());
            $snap->setStatus(Helper::SNAP_STATUS_DONE);
            $snap->save();
        }
        else {
            $processedCount=(int)$snap->getProcessedProducts() + count($products);
            if($processedCount > $snap->getTotalProducts()) {
                $snap->setTotalProducts($processedCount);
                $snap->setStatus(Helper::SNAP_STATUS_DONE);
            }
            $snap->setProcessedProducts($processedCount);
            $snap->save();

            $newProducts=SnapProductTable::createCollection();
            foreach($products as $productId=>$product) {
                if(empty($product['OFFERS'])) {
                    $newSnapProduct=SnapProductTable::createObject();
                    $newSnapProduct->setSnapId($snap->getId());
                    $newSnapProduct->setProductId($productId);
                    $newSnapProduct->setPrice($product['PRICE']);

                    $newProducts[]=$newSnapProduct;
                }
                else {
                    foreach($product['OFFERS'] as $offerId=>$offer) {
                        $newSnapProduct=SnapProductTable::createObject();
                        $newSnapProduct->setSnapId($snap->getId());
                        $newSnapProduct->setProductId($productId);
                        $newSnapProduct->setOfferId($offerId);
                        $newSnapProduct->setPrice($offer['PRICE']);

                        $newProducts[]=$newSnapProduct;
                    }
                }
            }

            // В версии 18.1.8 ошибка, которая исправлена в более поздней версии 20.x
            // $newProducts->save(true);
            static::addSnapProducts($newProducts);
        }
    }

    protected static function addSnapProducts($products)
    {
        $values=[];
        foreach($products as $product) {
            $values[]=(int)$product->getSnapId() 
            . ',' . (int)$product->getProductId()
            . ',' . (int)$product->getOfferId()
            . ",'" . sprintf('%0.2f', (float)$product->getPrice()) . "'";
        }
        
        if(!empty($values)) {
            $query='INSERT INTO `' . (new SnapProductTable)->getTableName() . '` (`SNAP_ID`, `PRODUCT_ID`, `OFFER_ID`, `PRICE`)'
                .' VALUES (' . join('),(', $values) . ')';
            
            $connection=Application::getConnection();
            $connection->query($query);
        }
    }
}