<?
/**
 * IblockTable
 */
namespace Kontur\Core\Iblock;

\Bitrix\Main\Loader::includeModule('iblock');

class IblockTable extends \Bitrix\Iblock\IblockTable
{
    use \Kontur\Core\Main\Entity\DataManager;
}