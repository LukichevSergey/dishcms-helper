<?php
namespace common\ext\iterator\models;

use common\components\helpers\HArray as A;
use common\components\helpers\HFile;
use common\components\helpers\HHash;
use common\ext\iterator\components\exceptions\ConfigException;
use common\components\helpers\HEvent;

/**
 * Модель конфигурации итератора.
 *
 */
class Config
{
    /**
     * Имя переменной в которой передается хэш конфигурации.
     * @var string
     */
    CONST CONFIG_HASH_VAR='ich';
    
    /**
     * Имя переменной в которой передается хэш процесса 
     * По умолчанию "iph".
     * @var string
     */
    protected $hashVar='iph';
    
    /**
     * Имя переменной в которой передаются дополнительные данные 
     * По умолчанию "ipm".
     * @var string
     */
    protected $paramsVar='ipm';
    
    /**
     * Внутренний идетификатор конфигурации. 
     * @var string
     */
    protected $id;
    
    /**
     * Псевдоним пути к файлу конфигурации.
     * Разделителем является символ "." (точки). 
     * @var string
     */
    protected $path;
        
    /**
     * Параметры конфигурации
     * @var []
     */
    protected $config=[];
    
    /**
     * Дополнительный ключ шифрования данных
     * @var string
     */
    protected $secure='';
    
    /**
     * Инициализация
     */
    public function init()
    {
        $this->setHashVar($this->get('var_hash', 'iph'));
        $this->setParamsVar($this->get('var_params', 'ipm'));
        $this->setSecure($this->get('secure', ''));
    }
    
    /**
     * Получить имя переменной в которой передается хэш процесса.
     * @return string
     */
    public function getHashVar()
    {
        return $this->hashVar;
    }
    
    /**
     * Установить имя переменной в которой передается хэш процесса.
     * @param string $name имя переменной в которой передается хэш процесса.
     */
    protected function setHashVar($name)
    {
        $this->hashVar=$name;
    }
    
    /**
     * Установить дополнительный ключ шифрования
     * @param string|callable $secure дополнительный ключ шифрования
     */
    protected function setSecure($secure)
    {
        if(!is_string($secure) && is_callable($secure)) {
            $this->secure=(string)call_user_func($secure);
        }
        else {
            $this->secure=(string)$secure;
        }
    }
    
    /**
     * Получить дополнительный ключ шифрования
     * @return string
     */
    public function getSecure()
    {
        return $this->secure;
    }
    
    /**
     * Получить имя переменной в которой передаются дополнительные данные.
     * @return string
     */
    public function getParamsVar()
    {
        return $this->paramsVar;
    }
    
    /**
     * Установить имя переменной в которой передаются дополнительные данные.
     * @param string $name имя переменной в которой передаются дополнительные данные.
     */
    protected function setParamsVar($name)
    {
        $this->paramsVar=$name;
    }
    
    /**
     * Получить внутреннний идентификатор конфигурации
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Установить внутренний идентификатор конфигурации
     * @param string $id внутренний идетификатор конфигурации.
     */
    public function setId($id)
    {
        $this->id=$id;
    }
    
    /**
     * Получить псевдоним пути к файлу конфигурации
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }
    
    /**
     * Получить хэш конфигурации
     */
    public function getConfigHash()
    {
        return HHash::srEcrypt(['path'=>$this->getPath(), 'id'=>$this->getId()], $this->getSecure());
    }
    
    /**
     * Загрузить конфигурацию по хэшу конфигурации
     * @param string $hash хэш конфигурации
     * @return boolean
     */
    public function loadByHash($hash)
    {
        $params=HHash::srDecrypt($hash, $this->getSecure(), true);
        if(!is_array($params)) {
            $event=HEvent::raise('onCommonExtIteratorGetSecureKeys');
            if($secures=A::get($event->params, 'secures', [])) {
                foreach($secures as $secure) {
                    $params=HHash::srDecrypt($hash, $secure, true);
                    if(is_array($params)) {
                        break;
                    }
                }
            }
        }
        
        if(is_array($params)) {
            $id=A::get($params, 'id');
            $path=A::get($params, 'path');
            if($id && $path) {
                return $this->load("{$path}.{$id}");
            }
        }
        
        return false;
    }
    
    /**
     * Установить идентификатор конфигурации
     * @param string $path псевдоним пути к файлу конфигурации.
     * Разделителем является символ "." (точки).
     */
    public function setPath($path)
    {
        $this->path=$path;
    }
    
    /**
     * Получить конфигурацию
     * @return []
     */
    public function getConfig()
    {
        return $this->config;
    }
    
    /**
     * Установить конфигурацию
     * @param []|string $config конфигурация.
     * Может быть передан псевдоним пути к файлу конфигурации
     */
    public function setConfig($config)
    {
        if(is_string($config)) {
            $config=HFile::includeByAlias($config);
        }
        
        if(!is_array($config)) {
            $this->config=[];
        }
        
        $this->config=$config;
        
        $this->init();
    }
    
    /**
     * Получить значение параметра конфигурации
     * @param string $name имя параметра 
     * @param mixed $default значение по умолчанию
     * @return mixed
     */
    public function get($name, $default=null)
    {
        return A::get($this->getConfig(), $name, $default);
    }
    
    /**
     * Установить значение параметра конфигурации
     * @param string $name имя параметра 
     * @param mixed $value значение
     */
    public function set($name, $value)
    {
        return $this->config[$name]=$value;
    }
    
    /**
     * Получить событие из конфигурации
     * @param string $name имя события
     * @param mixed $default значение по умолчанию
     * @return mixed
     */
    public function getEvent($name, $default=null)
    {
        return A::rget($this->getConfig(), "events.{$name}", $default);
    }
    
    /**
     * Загрузка конфигурации из файла
     * @param string $id идетификатор конфигурации.
     * Идетификатор конфигурации определяется как:
     * "псевдоним пути к файлу конфигурации" + "внутренний идентификатор конфигурации"
     * Разделителем является символ "." (точки).
     * @throws ConfigException
     * @return true
     */
    public function load($id)
    {
        $lastDotPos=strrpos($id, '.');
        if($lastDotPos > 0) {
            $innerConfigId=substr($id, $lastDotPos+1);
            $configPath=substr($id, 0, $lastDotPos);
            if($configs=HFile::includeByAlias($configPath)) {
                if($config=A::get($configs, $innerConfigId)) {
                    $this->setId($innerConfigId);
                    $this->setPath($configPath);
                    $this->setConfig($config);
                    $this->init();
                    return true;
                }
            }
        }
        
        throw new ConfigException("Конфигурация {$id} не найдена.");
    }
}