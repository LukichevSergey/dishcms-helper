<?
/**
 * Iblock\PropertyTable
 */
namespace Kontur\Core\Iblock;

\Bitrix\Main\Loader::includeModule('iblock');

class PropertyTable extends \Bitrix\Iblock\PropertyTable
{
    use \Kontur\Core\Main\Entity\DataManager;
}