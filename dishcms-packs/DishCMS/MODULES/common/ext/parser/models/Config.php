<?php
namespace common\ext\parser\models;

use common\components\helpers\HArray as A;
use common\components\helpers\HFile;
use common\components\helpers\HHash;
use common\ext\parser\components\helpers\HParser;
use common\ext\parser\components\exceptions\ConfigException;
use crud\models\ar\common\ext\parser\models\Parser;

/**
 * Модель конфигурации парсера
 *
 */
class Config
{
    /**
     * Имя переменной с псевдонимом имени файла конфигурации парсера
     * @var string
     */
    const CONFIG_HASH_VAR='pch';
    
    /**
     * Имя переменной с идентификатором процесса парсера
     * @var string
     */
    const PROCESS_ID_VAR='ppid';
    
    /**
     * Текущая конфигурация
     * @var array
     */
    private $config=[];
    
    /**
     * Псевдоним файла конфигурации
     * @var string
     */
    private $filename;
    
    /**
     * Загрузка конфигурации парсера
     * 
     * @param string $path псевдоним имени файла конфигурации парсера
     * 
     * @return Config
     */
    public static function load($filename)
    {
        $config=new self;
        
        $config->setFilename($filename);
        
        $config->setConfig(HFile::includeByAlias($config->getFilename(), []));
        
        return $config;
    }
    
    /**
     * Загрузить конфигурацию по хэшу конфигурации
     * 
     * @param string $hash хэш конфигурации
     * 
     * @return Config
     */
    public static function loadByHash($hash)
    {
        $params=HHash::srDecrypt($hash, HParser::key(), true);
        
        if(is_array($params)) {
            if($filename=A::get($params, 'filename')) {
                return static::load($filename);
            }
        }
        
        throw new ConfigException('Не удалось загрузить конфигурацию парсера');
    }
    
    /**
     * Получить псевдоним файла конфигурации
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }
    
    /**
     * Получить псевдоним файла конфигурации
     * 
     * @param string $filename псевдоним файла конфигурации
     * 
     * @return Config
     */
    public function setFilename($filename)
    {
        $this->filename=$filename;
        
        return $this;
    }
    
    /**
     * Получить хэш конфигурации
     */
    public function getConfigHash()
    {
        return HHash::srEcrypt(['filename'=>$this->getFilename()], HParser::key());
    }
    
    /**
     * Установить конфигурацию парсера
     * 
     * @param array $config конфигурация парсера
     * 
     * @return Config
     */
    public function setConfig($config)
    {
        if(!is_array($config)) {
            $config=[];
        }
        
        $this->config=$config;
        
        return $this;
    }
    
    /**
     * Получить конфигурацию
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }
    
    /**
     * Получить конфигурацию итератора
     * @return array
     */
    public function getIteratorConfig()
    {
        $iteratorConfig=A::get($this->getConfig(), 'iterator', []);
        
        if(!A::existsKey($iteratorConfig, 'create')) {
            $iteratorConfig['create']=$this->getIteratorDefaultCreateHandler();
        }
        if(!A::existsKey($iteratorConfig, 'next')) {
            $iteratorConfig['next']=$this->getIteratorDefaultNextHandler();
        }
        
        return $iteratorConfig;
    }
    
    /**
     * Получить обработчик создания нового процесса парсера по умолчанию
     * @return callable обработчик, который возвращает массив вида 
     * array(Config::PROCESS_ID_VAR => идентификатор_процесса).
     */
    public function getIteratorDefaultCreateHandler()
    {
        return function($process) {
            if($parserConfigHash=$process->getDataParam(Config::CONFIG_HASH_VAR)) {
                Parser::model()->loadConfigByHash($parserConfigHash);
                
                if($processId=Parser::model()->create()) {
                    return [Config::PROCESS_ID_VAR=>$processId];
                }
            }
            
            throw new ConfigException('Не удалось запустить парсер.');
        };
    }
    
    /**
     * Получить обработчик итерации процесса парсера по умолчанию
     * @return callable обработчик, который возвращает процент завершенности процесса.
     */
    public function getIteratorDefaultNextHandler()
    {
        return function($process) {
            if($parserConfigHash=$process->getDataParam(Config::CONFIG_HASH_VAR)) {
                Parser::model()->loadConfigByHash($parserConfigHash);
                
                return Parser::model()->next($process->getDataParam(Config::PROCESS_ID_VAR));
            }
            
            throw new ConfigException('Некорректный запрос.');
        };
    }
    
    /**
     * Получить параметр конфигурации 
     * @param string $name имя параметра.
     * Может быть передан путь к вложенному параметру через разделитель "." (точка).
     * @param mixed $default значение по умолчанию.
     * @return mixed
     */
    public function getParam($name, $default=null)
    {
        return A::rget($this->getConfig(), $name, $default);
    }
    
    /**
     * Получить список основных групп
     * @return array
     */
    public function getGroups()
    {
        return A::get($this->getConfig(), 'groups', []);
    }
    
    /**
     * Получить конфигурацию групп по пути
     * @param string $path путь к конфигурации группы (включая символьный код самой группы) 
     * для которой получается конфигурация.
     * Путь задается как символьные коды групп разделенные символом "." (точки).
     */
    public function getGroupsByPath($path)
    {
        $groups=[];
        
        if(!empty($path)) {
            $groups=$this->getParam('groups.' . str_replace('.', '.groups.', $path), []);
        }
        
        return $groups;
    }
    
    /**
     * Получить конфигурацию подгрупп по пути
     * @param string $path путь к конфигурации группы (включая символьный код самой группы) 
     * для которой получаются подгруппы.
     * Путь задается как символьные коды групп разделенные символом "." (точки).
     */
    public function getSubGroupsByPath($path)
    {
        return A::get($this->getGroupsByPath($path), 'groups', []);
    }
    
    /**
     * Получить основной домен
     * @return string
     */
    public function getDomain()
    {
        return A::get($this->getConfig(), 'domain', '');
    }
    
    /**
     * Получить URL входа
     * @return string
     */
    public function getEntry()
    {
        return A::get($this->getConfig(), 'entry');
    }
    
    /**
     * Получить лимит количества обрабатываемых страниц за итерацию
     * @return int
     */
    public function getLimit()
    {
        return (int)A::get($this->getConfig(), 'limit', 10);
    }
    
    /**
     * Получить задержку между запросами к серверу, с которого происходит парсинг
     * @return int
     */
    public function getDelay()
    {
        return (int)A::get($this->getConfig(), 'delay', 0);
    }
    
    /**
     * Получить общий обработчик сохранения сохранения 
     * @return callable|null обработчик сохранения полученных данных.
     * Определяется как function($group, $type, $data) { return boolean }, где
     *   param $group (\crud\models\ar\common\ext\parser\models\Group) модель текущей группы для которой были получены данные.
     *   param $type тип данных. Может принимать следующие значения:
     *     \common\ext\parser\components\helpers\HParser::TYPE_CONTENT - данные для сохранения в базу данных
     *     \common\ext\parser\components\helpers\HParser::TYPE_LINK - данные являются ссылками на внутреннине страницы
     *     \common\ext\parser\components\helpers\HParser::TYPE_PAGINATION - данные являются ссылками пагинатора. 
     *   param $data (array) полученный массив данных вида:
     *      array(
     *          array(attribute=>value, attribute=>value, ...)
     *          array(attribute=>value, attribute=>value, ...)
     *          ...
     *      )
     *   return boolean функция должна возвращать true в случае успешного сохранения данных 
     */
    public function getSaveHandler()
    {
        return A::get($this->getConfig(), 'save');
    }
   
    /**
     * Получить имя таблицы для записи данных группы
     * @param array $groupConfig конфигурация группы
     * @return string
     */
    public function getGroupContentTableName($groupConfig)
    {
        if($contentConfig=$this->getGroupContent($groupConfig)) {
            $tableName=A::get($contentConfig, 'tablename');
            if(!$tableName) {
                if($model=A::get($contentConfig, 'model')) {
                    $tableName=$model::model()->tableName();
                }
            }
            
            return $tableName;
        }
        
        return null;
    }
    
    /**
     * Получить параметр "recursive" для группы
     * @param array $groupConfig конфигурация группы
     * @return mixed
     */
    public function isGroupRecursive($groupConfig)
    {
        return (bool)A::get($groupConfig, 'recursive', false);        
    }
    
    /**
     * Получить параметр "precontent" для группы
     * @param array $groupConfig конфигурация группы
     * @return mixed
     */
    public function getGroupPreContent($groupConfig)
    {
        return A::get($groupConfig, 'precontent');        
    }
    
    /**
     * Получить конфигурацию секции "content" для группы
     * @param array $groupConfig конфигурация группы
     * @return mixed
     */
    public function getGroupContent($groupConfig)
    {
        return A::get($groupConfig, 'content');        
    }
    
    /**
     * Получить имя поля хэша синхронизации для секции "content" группы
     * @param array $groupConfig конфигурация группы
     * @return string
     */
    public function getGroupContentSyncAttribute($groupConfig)
    {
        return A::get($this->getGroupContent($groupConfig), 'sync_attribute', 'parser_sync_hash');
    }
    
    /**
     * Получить атрибуты/обработчик для генерации уникального хэша синхронизации для секции "content" группы
     *
     * @param array $groupConfig конфигурация группы
     *
     * @return []
     */
    public function getGroupContentSyncAttributes($groupConfig)
    {
        return A::get($this->getGroupContent($groupConfig), 'syncs', []);
    }
    
    /**
     * Получить параметр "precontent" для секции "content" группы
     * @param array $groupConfig конфигурация группы
     * @return mixed
     */
    public function getGroupContentPreContent($groupConfig)
    {
        return A::get($this->getGroupContent($groupConfig), 'precontent');        
    }
    
    /**
     * Получить параметр "pattern" для секции "content" группы 
     * @param array $groupConfig конфигурация группы  
     * @return mixed
     */
    public function getGroupContentPattern($groupConfig)
    {
        return A::get($this->getGroupContent($groupConfig), 'pattern');        
    }
    
    /**
     * Получить параметр "attributes" для секции "content" группы
     * @param array $groupConfig конфигурация группы
     * @return mixed
     */
    public function getGroupContentAttributes($groupConfig)
    {
        return A::get($this->getGroupContent($groupConfig), 'attributes');        
    }
    
    /**
     * Получить параметр "required" для секции "content" группы
     * @param array $groupConfig конфигурация группы
     * @return mixed
     */
    public function getGroupContentRequired($groupConfig)
    {
        return A::get($this->getGroupContent($groupConfig), 'required');
    }
    
    /**
     * Получить параметр "save" для секции "content" группы 
     * @param array $groupConfig конфигурация группы  
     * @return mixed
     */
    public function getGroupContentSaveHandler($groupConfig)
    {
        return A::get($this->getGroupContent($groupConfig), 'save');        
    }
    
    /**
     * Получить параметр "onDublicateSQL" для секции "content" группы 
     * @param array $groupConfig конфигурация группы  
     * @return mixed
     */
    public function getGroupContentOnDublicateSQL($groupConfig)
    {
        return A::get($this->getGroupContent($groupConfig), 'onDublicateSQL');        
    }
    
    /**
     * Получить параметр "beforeSave" для секции "content" группы 
     * @param array $groupConfig конфигурация группы  
     * @return mixed
     */
    public function getGroupContentBeforeSaveHandler($groupConfig)
    {
        return A::get($this->getGroupContent($groupConfig), 'beforeSave');        
    }
    
    /**
     * Получить параметр "links" для группы
     * @param array $groupConfig конфигурация группы
     * @return mixed
     */
    public function getGroupLinks($groupConfig)
    {
        return A::get($groupConfig, 'links');
    }
    
    /**
     * Получить параметр "precontent" для секции "links" группы
     * @param array $groupConfig конфигурация группы
     * @return mixed
     */
    public function getGroupLinksPreContent($groupConfig)
    {
        return A::get($this->getGroupLinks($groupConfig), 'precontent');
    }
    
    /**
     * Получить параметр "pattern" для секции "links" группы
     * @param array $groupConfig конфигурация группы
     * @return mixed
     */
    public function getGroupLinksPattern($groupConfig)
    {
        return A::get($this->getGroupLinks($groupConfig), 'pattern');
    }
    
    /**
     * Получить параметр "save" для секции "links" группы
     * @param array $groupConfig конфигурация группы
     * @return mixed
     */
    public function getGroupLinksSaveHandler($groupConfig)
    {
        return A::get($this->getGroupLinks($groupConfig), 'save');
    }
    
    /**
     * Получить параметр "beforeSave" для секции "links" группы
     * @param array $groupConfig конфигурация группы
     * @return mixed
     */
    public function getGroupLinksBeforeSaveHandler($groupConfig)
    {
        return A::get($this->getGroupLinks($groupConfig), 'beforeSave');
    }
    
    /**
     * Получить параметр "pagination" для группы
     * @param array $groupConfig конфигурация группы
     * @return mixed
     */
    public function getGroupPagination($groupConfig)
    {
        return A::get($groupConfig, 'pagination');
    }
    
    /**
     * Получить параметр "precontent" для секции "pagination" группы
     * @param array $groupConfig конфигурация группы
     * @return mixed
     */
    public function getGroupPaginationPreContent($groupConfig)
    {
        return A::get($this->getGroupPagination($groupConfig), 'precontent');
    }
    
    /**
     * Получить параметр "pattern" для секции "pagination" группы
     * @param array $groupConfig конфигурация группы
     * @return mixed
     */
    public function getGroupPaginationPattern($groupConfig)
    {
        return A::get($this->getGroupPagination($groupConfig), 'pattern');
    }
    
    /**
     * Получить параметр "save" для секции "pagination" группы
     * @param array $groupConfig конфигурация группы
     * @return mixed
     */
    public function getGroupPaginationSaveHandler($groupConfig)
    {
        return A::get($this->getGroupPagination($groupConfig), 'save');
    }
    
    /**
     * Получить параметр "beforeSave" для секции "pagination" группы
     * @param array $groupConfig конфигурация группы
     * @return mixed
     */
    public function getGroupPaginationBeforeSaveHandler($groupConfig)
    {
        return A::get($this->getGroupPagination($groupConfig), 'beforeSave');
    }
}