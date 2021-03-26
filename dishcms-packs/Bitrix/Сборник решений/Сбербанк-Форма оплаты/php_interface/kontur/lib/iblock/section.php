<?
/**
 * SectionTable
 */
namespace Kontur\Core\Iblock;

\Bitrix\Main\Loader::includeModule('iblock');

class SectionTable extends \Bitrix\Iblock\SectionTable
{
    use \Kontur\Core\Main\Entity\DataManager;
        
    /**
     * Получение разделов инфоблока в виде дерева
     */
    public static function getTree($iblockId, $parameters=[])
    {
        $parameters['filter']['=IBLOCK_ID']=$iblockId;
        if(empty($parameters['order'])) {
            $parameters['order']=['NAME'=>'ASC'];
        }
        array_unshift($parameters['order'], ['LEFT_MARGIN'=>'ASC']);
        
        return self::getAll($parameters);
    }
}