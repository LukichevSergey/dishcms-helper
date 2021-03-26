<?
/**
 * Sku
 */
namespace Kontur\Core\Catalog;

\Bitrix\Main\Loader::includeModule('catalog');

class Sku 
{
    /**
     * Получить данные о товаре предложения.
     * @param integer $id идентификатор предложения.
     *
     * @return FALSE|array результат. Если товар не является торговым предложеним, 
     * возвращается FALSE. Если товар является торговым предложением,то возвращается 
     * массив вида:
     * ID (ID товара, к которому привязано предложение);
     * IBLOCK_ID (ID инфоблока товаров);
     * OFFER_IBLOCK_ID (ID инфоблока торговых предложений);
     * SKU_PROPERTY_ID (ID свойства привязки торговых предложений к товарам);
     */
    public static function getProductInfo($id)
    {
        if($info=\CCatalogSKU::getProductList([$id])) {
            return array_shift($info);
        }
        return false;
    }
}