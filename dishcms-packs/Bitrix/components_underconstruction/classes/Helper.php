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

	public static function GetProperty($PROPERTY_ID, $DEFAULT_VALUE=false) {     
		global $APPLICATION;     
		return $APPLICATION->AddBufferContent(Array(&$APPLICATION, "GetProperty"), $PROPERTY_ID, $DEFAULT_VALUE); 
	} 

	public static function GetPageProperty($PROPERTY_ID, $DEFAULT_VALUE=false) {
        global $APPLICATION;
        return $APPLICATION->AddBufferContent(Array(&$APPLICATION, "GetPageProperty"), $PROPERTY_ID, $DEFAULT_VALUE);
    }

	public static function GetDateDiff($date, $date2="now", $format='%a')
	{
		$dateTime = new \DateTime($date);
		$dateTime2 = new \DateTime($date2);
		return $dateTime->diff($dateTime2)->format($format);
	}

	public static function GetDateSub($interval, $date="now", $format=false)
	{
		$diffDate=new \DateInterval($interval);
		$dateTime=new \DateTime($date);
		$dateTime->sub($diffDate);
		return $format ? $dateTime->format($format) : $dateTime;
	}
}
