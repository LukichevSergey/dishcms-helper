<?
/**
 * Basket
 *
 * @link https://mrcappuccino.ru/blog/post/work-with-basket-bitrix-d7
 */
namespace Kontur\Core\Sale\Ajax;

use \Kontur\Core\Sale,
    \Kontur\Core\Iblock,
    \Kontur\Core\Catalog,
    \Kontur\Core\Main\Tools;

\Bitrix\Main\Loader::includeModule('sale');

class Basket 
{
    /**
     * Добавление товара в корзину
     * @param string $paramId имя параметра идентификатор товара в $_REQUEST.
     * @param string $paramQuantity имя параметра кол-ва добавляемого товара в $_REQUEST. 
     * @param boolean $returnResult возвратить массив результата. 
     * По умолчанию (FALSE) - отравить результат в поток вывода (в JSON формате).
     */
    public static function add($paramId='id', $paramQuantity='quantity', $returnResult=false)
    {
        $arResult=['success'=>false];

        if($id=Tools\Data::get($_REQUEST, $paramId)) {
            if(Sale\Basket::add($id, (int)Tools\Data::get($_REQUEST, $paramQuantity, 1))) {
                $basket=Sale\Basket::getBasket();
                
                $arResult=[
                    'success'=>true, 
                    'data'=>[
                        'basket'=>[
                            'totalCount'=>Sale\Basket::getTotalCount($basket),
                            'totalPrice'=>$basket->getPrice(),
                            'totalBasePrice'=>$basket->getBasePrice()
                        ]
                    ]   
                ];
                
                if($productInfo=Catalog\Sku::getProductInfo($id)) {
                    $arResult['data']['product']=Iblock\ElementTable::get($productInfo['ID'], ['select'=>['IBLOCK_ID', 'ID', 'NAME']]);
                    $arResult['data']['sku']=Iblock\ElementTable::get($id, ['select'=>['ID', 'NAME']]);
                }
                else {
                    $arResult['data']['product']=Iblock\ElementTable::get($id, ['select'=>['IBLOCK_ID', 'ID', 'NAME']]);
                }
            }
            else {
                $arResult['errors']=['Товар не был добавлен в корзину.'];
            }
        }
        else {
            $arResult['errors']=['Не передан идентификатор товара.'];
        }
        
        if($returnResult) {
            return $arResult;
        }
        
        echo json_encode($arResult);
        exit;
    }
}