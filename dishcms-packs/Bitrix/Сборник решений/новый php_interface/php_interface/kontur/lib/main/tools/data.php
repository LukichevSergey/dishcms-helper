<?
/**
 * Tools. Data.
 */
namespace Kontur\Core\Main\Tools;

class Data
{
    public static function getMonthRu($month)
    {
        $names=['января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'];
        
        return self::get($names, $month, false);
    }
    
	public static function get($data, $key, $default=null)
	{
		return isset($data[$key]) ? $data[$key] : $default;
	}

    public static function listData($data, $valueKey, $valueTitle, $valuePrefixTitle=false, $empty=false)
    {
        if(is_array($empty)) {
            $list=$empty;
        }
        elseif($empty) {
            $list=[''=>$empty];
        }
        else {
            $list=[];
        }
        
        if(!empty($data)) {
            $prefix='';      
            foreach($data as $item) { 
                if($valuePrefixTitle) {
                    $prefix = '[' . $item[$valuePrefixTitle] . '] ';
                }
                $list[ $item[$valueKey] ] = $prefix . $item[$valueTitle];
            }
        }

        return $list;
    }
}