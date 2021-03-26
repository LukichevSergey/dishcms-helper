<?
/**
 * ProductTable
 */
namespace Kontur\Core\Catalog;

\Bitrix\Main\Loader::includeModule('catalog');

class ProductTable extends \Bitrix\Catalog\ProductTable
{
    use \Kontur\Core\Main\Entity\DataManager;

    public static function getQuantity($id)
    {
    	$result=self::getAll([
            'filter'=>['ID'=>$id], 
            'select'=>['QUANTITY', 'QUANTITY_RESERVED', 'QUANTITY_TRACE', 'QUANTITY_TRACE_ORIG']
        ]);
        
        if($result && (!is_array($id) || (count($id) == 1))) {
            return $result[0];
        }
        
        return $result;
    }
}