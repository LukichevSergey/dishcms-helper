<?
/**
 * Кэширование
 */
namespace Kontur\Core\Main\Data;

class Cache extends \Bitrix\Main\Data\Cache
{
	const HOUR=3600;
	const DAY=86400;
	const WEEK=604800;
	const MONTH=2592000;

    /**
     * Получить данные
     *
     * @param integer $cacheTime время кэширования в секундах. 
     * Может быть передано 0(нуль), в этом случае будет возвращен 
     * результат выполнения функции заданной в параметре $hGetResults. 
     * Другими словами, это получение результата не из кэша.
     * Может быть перед параметр -1, в этом случае кэш будет перезаписан, а 
     * возвращен будет результат выполнения функции заданной 
     * в параметре $hGetResults. 
     *
     * @param string $cacheId ID кэша
     
     * @param string $cacheDir директория кэша. По умолчанию '/' - доступна всем.
     
     * @param callable|mixed $hGetResults callable переменная получения результата, 
     * который будет сохранен в кэш и возвращен. По умолчанию NULL.
     * Если передано значение не callable типа, то оно будет сохранено в кэше и возвращено.
     * Вызов производится методом call_user_func_array().
     * 
     * @param array $parameters массив параметров для функции/метода получения результата. 
     * По умолчанию пустой массив.
     *
     * @return mixed
     */
    public static function get($cacheTime, $cacheId, $cacheDir='/', $hGetResults=null, $parameters=array())
    {
        $cacheTime=(int)$cacheTime;
        
        if($cacheTime === 0) {
            if(is_callable($hGetResults)) {
                $result = call_user_func_array($hGetResults, $parameters);
            }
            else {
                $result = $hGetResults;
            }
                       
            return $result;            
        }
        
        $cache = \Bitrix\Main\Data\Cache::createInstance();
        
        if($cacheTime < 0) {
            $_forceRewriting=$cache->forceRewriting;
            $cache->forceRewriting=true;
        }        
        
        if ($cache->initCache($cacheTime, $cacheId, $cacheDir)) {
            $result = $cache->getVars();
        }
        elseif ($cache->startDataCache()) {
            try {
                if(is_callable($hGetResults)) {
                    $result = call_user_func_array($hGetResults, $parameters);
                }
                else {
                    $result = $hGetResults;
                }
            }
            catch(\Exception $e) {
                $cache->abortDataCache();
                
                $result = null;
            }
                
            $cache->endDataCache($result);
        }
        
        if($cacheTime < 0) {
            $cache->forceRewriting=$_forceRewriting;
        }        
        
        return $result;
    }    
}