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
	const YEAR=946080000;

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
     * @param string|array|FALSE $tag имя или массив тэгов для тэгирования кэша. Может быть передано 
     * специальное значение "iblock", в таком случае будет добавлено тэгирование инфоблока, с очистой 
     * при изменении элементов и при добавлении нового элемента. Идентификатор инфоблока необходимо передать,  
     * - либо явно "iblock"=>id, 
     * - либо через параметр "iblock"=>callable, где в callable(получение идентификатора инфоблока) будет 
     * передан параметр $result. Могут быть использованы методы: 
     * - Cache::getIblockIdByList() для списка элементов (может быть передано сокращенной формой "iblock"=>"byList")
     * - Cache::getIblockIdByElement() для одиночного элемента (может быть передано сокращенной формой "iblock"=>"byElement")
     * Если идентификатор инфоблока не получен, тэгирование по инфоблоку применено не будет.
     * По умолчанию (FALSE) тэгирование не используется.
     * @see https://dev.1c-bitrix.ru/learning/course/?COURSE_ID=43&LESSON_ID=2978 
     *
     * @param string|FALSE $tagCacheDir имя директории хранения данных тэгированного кэша. 
     * По умолчанию (FALSE) будет использована основная категория + постфикс "_tag".
     *
     * @return mixed
     */
    public static function get($cacheTime, $cacheId, $cacheDir='/', $hGetResults=null, $parameters=array(), $tag=false, $tagCacheDir=false)
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
                
                if($tag !== false) {
                	if(!$tagCacheDir) {
                		$tagCacheDir = $cacheDir . '_tag'; 
                	}
                	$GLOBALS['CACHE_MANAGER']->StartTagCache($tagCacheDir);
                	foreach($tag as $name=>$value) {
                		if($name === (int)$name) {
                			$name=$value;
                			$value=false;
                		}
                		
                		if($name == 'iblock') {
                			if(is_numeric($value)) {
                				$iblockId = $value;
                			}
                			elseif(is_callable($value)) {
                				$iblockId = (int)call_user_func_array($value, array($result));
                			}
                			elseif($value == 'byList') {
                				$iblockId = self::getIblockIdByList($result);
                			}
                			elseif($value == 'byElement') {
                				$iblockId = self::getIblockIdByElement($result);
                			}
                			else {
                				$iblock = false;
                			}
                			if($iblockId) {
                				$GLOBALS['CACHE_MANAGER']->RegisterTag('iblock_id_'.$iblockId);
                				$GLOBALS['CACHE_MANAGER']->RegisterTag('iblock_id_new');
                			}
                		}
                		else {
                			$GLOBALS['CACHE_MANAGER']->RegisterTag($name);
                		}                		
                	}
                	$GLOBALS['CACHE_MANAGER']->EndTagCache();
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
    
    /**
     * Получение идентификатора инфоблока из массива элементов.
     * @param array $elements массив элементов.
     * @return integer|boolean иденификатор инфоблока, либо FALSE, если
     * идентификатор инфоблока не получен.
     */
    public static function getIblockIdByList($elements)
    {
    	$iblockId=false;
    	
    	if(!empty($elements)) {
    		foreach($elements as $elm) {
    			if(isset($elm['IBLOCK_ID'])) {
    				return (int)$elm['IBLOCK_ID'];
    			}
    		}
    	}
    	
    	return $iblockId;
    }
    
    /**
     * Получение идентификатора инфоблока из массива данных элемента.
     * @param array $element массив данных элемента.
     * @return integer|boolean иденификатор инфоблока, либо FALSE, если 
     * идентификатор инфоблока не получен.
     */
    public static function getIblockIdByElement($element)
    {
    	if(isset($element['IBLOCK_ID'])) {
    		return (int)$element['IBLOCK_ID'];
    	}
    	
    	return false;
    }
    
    /**
     * Очистка кэша по тэгу.
     * 
     * @param string|array $tag имя тэга или массив имен тэгов.
     */
    public static function clearByTag($tag)
    {
    	if(is_array($tag)) {
    		foreach($tag as $tagName) {
		    	$GLOBALS['CACHE_MANAGER']->ClearByTag($tagName);
    		}
    	}
    	else {
    		$GLOBALS['CACHE_MANAGER']->ClearByTag($tag);
    	}
    }
}