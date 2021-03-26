<?
/**
 * \Kontur\Core\Iblock\Tools\Db
 * 
 */ 
namespace Kontur\Core\Iblock\Tools;

class Db
{
    /**
     * Получить список элементов выборки.
     * @param \CDBResult объект результата выполнения запроса.
     * @param string|FALSE имя свойства, которое будет использовано в качестве ключа.
     * По умолчанию (FALSE) - не задано.  
     * @return array
     */
	public static function fetchAll($rs, $key=false)
	{
		$result = array();
		
		while($item = $rs->GetNext()) {
			if($key && isset($item[$key])) {
				$result[$item[$key]] = $item;				
			}
			else {
            	$result[] = $item;
			}
        }

        return $result;
	}
}