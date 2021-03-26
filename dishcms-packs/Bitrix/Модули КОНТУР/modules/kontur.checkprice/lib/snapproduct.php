<?php
namespace Kontur\CheckPrice;

use Bitrix\Main;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Fields\Relations\Reference;
use Bitrix\Main\ORM\Query\Join;
use Bitrix\Catalog\Product;
use Kontur\CheckPrice\SnapTable;

Loc::loadMessages(__FILE__);

class SnapProductTable extends Main\Entity\DataManager
{
    public static function getFilePath()
    {
        return __FILE__;
    }

    public static function getTableName()
    {
        return 'kontur_checkprice_snap_products';
    }

    public static function getMap()
    {
        return [
            'ID' => new Main\Entity\IntegerField('ID', [
				'primary' => true,
				'autocomplete' => true,
                'title' => 'Идентификатор записи'
			]),
            'SNAP_ID' => new Main\Entity\IntegerField('SNAP_ID', [
				'title' => 'Идентификатор снимка цен'
			]),
            'PRODUCT_ID' => new Main\Entity\IntegerField('PRODUCT_ID', [
                'required' => true,
                'title' => 'Идентификатор товара'
			]),
            'OFFER_ID' => new Main\Entity\IntegerField('OFFER_ID', [
                'title' => 'Идентификатор торгового предложения'
			]),
            'PRICE' => new Main\Entity\FloatField('PRICE', [
				'title' => 'Цена'
            ]),
            (new Reference(
                'SNAP',
                SnapTable::class,
                Join::on('this.SNAP_ID', 'ref.ID')
            )),
            (new Reference(
                'PRODUCT',
                Product::class,
                Join::on('this.PRODUCT_ID', 'ref.ID')
            )),   
        ];
    }    
}