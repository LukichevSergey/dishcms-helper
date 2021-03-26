<?
/**
 * Tools. Model.
 */
namespace Kontur\Core\Main\Tools;

class Model
{
    public static function name($className)
    {
        return preg_replace('/[^a-z0-9]+/i', '_', $className);
    }
}