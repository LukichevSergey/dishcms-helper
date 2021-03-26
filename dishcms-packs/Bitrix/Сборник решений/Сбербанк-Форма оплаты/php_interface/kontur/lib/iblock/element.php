<?
/**
 * ElementTable
 */
namespace Kontur\Core\Iblock;

\Bitrix\Main\Loader::includeModule('iblock');

class ElementTable extends \Bitrix\Iblock\ElementTable
{
    use \Kontur\Core\Main\Entity\DataManager;
    
    public static function getPropertyByCode($iblockId, $elmId, $code)
    {
        $rs = \CIBlockElement::GetProperty($iblockId, $elmId, ['sort'=>'asc'], ['CODE'=>$code]);
        if($prop = $rs->Fetch()) {
            return $prop;
        }
        
        return null;
    }
}