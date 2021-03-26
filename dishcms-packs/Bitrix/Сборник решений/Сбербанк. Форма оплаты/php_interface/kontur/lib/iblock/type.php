<?
/**
 * Iblock\TypeTable
 */
namespace Kontur\Core\Iblock;

\Bitrix\Main\Loader::includeModule('iblock');

class TypeTable extends \Bitrix\Iblock\TypeTable
{
    use \Kontur\Core\Main\Entity\DataManager;
}