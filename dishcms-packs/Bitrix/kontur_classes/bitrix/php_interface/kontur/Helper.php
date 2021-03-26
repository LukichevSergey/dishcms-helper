<?php
/**
 * Класс помощник
 */
namespace Kontur;

class Helper
{
    /**
     * Получить значение элемента массива
     * Пример получения значения для $arResult: getArrayValue($arResult, 'SECTION.NAME');
     * @param array $array массив
     * @param mixed $key ключ массива, может быть передан также массив ключей в глубину, либо строка ключей разделенных точкой.
     * @param mixed $default значение возвращаемое по умолчанию, если элемент пуст или не найден.
     * @param string $delimiter символ разделения ключей в строке. По умолчанию ".".
     */
    public static function getArrayValue($array, $key, $default=null, $delimiter='.') 
    {
        if(is_string($key) && (strpos($key, $delimiter) !== false)) {
            $key=explode($delimiter, $key);
        }
        
        if(is_array($key)) {
            $k=array_shift($key);
            if(is_array($array[$k])) 
                return getArrayValue($array[$k], $key, $default);
            $key=$k;
        }
        
        return array_key_exists($key, $array) ? $array[$key] : $default;
    }

    /**
     * Получение пользовательского свойства
     * @param string $IBLOCK_ID идентификатор инфоблока
     * @param string $SECTIN_ID идентификатор раздела
     * @param string $PROPERTY_NAME имя пользовательского свойства без префикса "UF_".
     * @return mixed Если свойство не найдено, возвращается NULL.
     */
    public static function getUFProperty($IBLOCK_ID, $SECTIN_ID, $PROPERTY_NAME)
    {
        $dbSections=CIBlockSection::GetList(Array("SORT"=>"ASC"), Array("IBLOCK_ID"=>$IBLOCK_ID, "ID"=>$SECTIN_ID),false, Array("UF_{$PROPERTY_NAME}"));
        return ($arSection=$dbSections->GetNext()) ? $arSection["UF_{$PROPERTY_NAME}"] : null;
    }
}