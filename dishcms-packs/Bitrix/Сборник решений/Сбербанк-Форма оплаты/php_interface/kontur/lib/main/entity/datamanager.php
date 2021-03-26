<?
/**
 * Трейт дополнительных методов для работы с таблицами.
 */
namespace Kontur\Core\Main\Entity;

use Kontur\Core\Main\Data\Cache;
use Kontur\Core\Main\Tools\Model;

trait DataManager 
{
    /**
     * Получение всех элементов
     * @param array $parameters массив параметров. См. метод getList()
     * @param integer $cacheTime время кэширования результатов. По умолчанию 60 секунд.
     * @param integer $cacheId идентификатор кэша. По умолчанию будет сгенерирован автоматически.
     * @param integer $cacheDir директория кэша. По умолчанию будет сгенерировано автоматически.
     */
    public static function getAll($parameters=[], $cacheTime=60, $cacheId=null, $cacheDir=null)
    {
        if(!$cacheId) {
            $cacheId = self::getCacheId(serialize($parameters).$cacheTime);
        }
        
        if(!$cacheDir) {
            $cacheDir = self::getCacheDir();
        }
        
        return Cache::get($cacheTime, $cacheId, $cacheDir, function ($className, $parameters) {
            return $className::getList($parameters)->fetchAll();
        }, [get_called_class(), $parameters]);
    }
    
    public static function get($id=null, $parameters=[], $cacheTime=60, $cacheId=null, $cacheDir=null)
    {
    	if($id) {
	        $parameters['filter']['=ID']=$id;
	    }
        
        if(!$cacheId) {
            $cacheId = self::getCacheId(serialize($parameters).$cacheTime);
        }
        
        if(!$cacheDir) {
            $cacheDir = self::getCacheDir();
        }        
        
        return Cache::get($cacheTime, $cacheId, $cacheDir, function ($className, $parameters) {
            return $className::getList($parameters)->fetch();
        }, [get_called_class(), $parameters]);
    }
    
    protected static function getCacheId($hash=null)
    {
        if(!$hash) {
            $hash=get_called_class();
        }
        
        return md5($hash);
    }
    
    protected static function getCacheDir()
    {
        return '/' . Model::name(get_called_class());
    }
}