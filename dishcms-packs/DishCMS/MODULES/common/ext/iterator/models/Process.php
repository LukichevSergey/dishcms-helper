<?php
namespace common\ext\iterator\models;

use common\components\helpers\HArray as A;
use common\components\helpers\HHash;
use common\ext\iterator\components\exceptions\ProcessException;

/**
 * Процесс итератора
 *
 */
class Process
{
    /**
     * Конфигурация процесса
     * @var Config|null
     */
    protected $config;
    
    /**
     * Хэш процесса
     * @var string
     */
    protected $hash;
    
    /**
     * Хэш был дешефрован
     * @var string
     */
    protected $hashDecrypted=false;
    
    /**
     * Данные хэша
     * @var []
     */
    protected $hashData=[];
    
    /**
     * Дополнительные параметры процесса, которые 
     * будет возвращены на сторону клиента
     * @var array
     */
    protected $params=[];
    
    /**
     * Процент завершенности процесса
     * @var int
     */
    protected $percent=0;
    
    /**
     * Ключ шифрования
     * @var string
     */
    protected $key='';
    
    /**
     * Ошибки
     * @var []
     */
    protected $errors=[];
    
    /**
     * Получить дополнительные параметры
     * @return []
     */
    public function getParams()
    {
        return $this->params;
    }
    
    /**
     * Установить дополнительные параметры
     * @param [] $params массив дополнительных параметров вида array(name=>value).
     * @param bool $merge объединить с установленными ранее параметрами. 
     * По умолчанию (false) будет ранее установленые параметры будут заменены переданными. 
     */
    public function setParams($params, $merge=false)
    {
        if($merge) {
            $this->params=A::m($this->params, A::toa($params));
        }
        else {
            $this->params=A::toa($params);
        }
    }
    
    /**
     * Получить значение дополнительного параметра
     * @param string $name имя параметра
     * @param mixed $default значение по умолчанию
     * @param bool $decrypt расашифровать данные. 
     * По умолчанию (false) не расшифровывать.
     * @param bool $assoc возвращать ассоциативный массив при расшифровке.
     * По умолчанию (false) возвращать как есть.
     * @return mixed
     */
    public function getParam($name, $default=null, $decrypt=false, $assoc=false)
    {
        $value=A::get($this->params, $name, $default);
        if($decrypt) {
            return $this->decrypt($value, $assoc);
        }
        else {
            return $value;
        }
    }
    
    /**
     * Установить дополнительный параметр
     * @param string $name имя параметра
     * @param mixed $value значение параметра
     * @param bool $crypt зашифровать данные. 
     * По умолчанию (false) не шифровать.
     */
    public function setParam($name, $value, $crypt=false)
    {
        if($crypt) {
            $this->params[$name]=$this->crypt($value);
        }
        else {
            $this->params[$name]=$value;
        }
    }
    
    /**
     * Удалеить дополнительный параметр
     * @param string $name имя параметра
     */
    public function deleteParam($name)
    {
        if(A::existsKey($this->params, $name)) {
            unset($this->params[$name]);
        }
    }
    
    /**
     * Добавить ошибку
     * @param string $message текст ошибки
     */
    public function addError($message)
    {
        $this->errors[]=$message;
    }
    
    /**
     * Получить ошибки
     * @return []
     */
    public function getErrors()
    {
        return $this->errors;
    }
    
    /**
     * Возникли ошибки
     * @return boolean
     */
    public function hasErrors()
    {
        return (count($this->getErrors()) > 0);
    }
    
    /**
     * Получить ключ шифрования
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }
    
    /**
     * Установить ключ шифрования
     * @param string $key ключ шифрования
     */
    public function setKey($key)
    {
        $this->key=(string)$key;
    }
    
    /**
     * Установить данные хэша
     * @param [] $data данные хэша
     */
    protected function setHashData($data)
    {
        if(!is_array($data)) {
            $data=[];
        }
        
        $this->hashData=$data;
    }
    
    /**
     * Получить хэш процесса итератора
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }
    
    /**
     * Установить хэш процесса
     * @param string $hash хэш процесса
     */
    public function setHash($hash)
    {
        $this->hash=$hash;
        $this->hashDecrypted=false;
        $this->setHashData([]);
        $this->initData();
    }
    
    /**
     * Хэш уже дешефрован
     * @return bool
     */
    protected function isHashDecrypted()
    {
        return $this->hashDecrypted;
    }
    
    /**
     * Инициализация данных
     */
    public function initData()
    {
        if(!$this->isHashDecrypted()) {
            $this->setHashData($this->decrypt($this->getHash(), true));
            $this->hashDecrypted=true;
        }
    }
    
    /**
     * Получить данные из хэша
     * @param string $hash хэш процесса итератора
     */
    public function getData()
    {
        $this->initData();
        
        return $this->hashData;
    }
    
    /**
     * Получить параметра "id" из данных хэша
     * @return string|null
     */
    public function getDataId()
    {
        return A::get($this->getData(), 'id');
    }
    
    /**
     * Получить параметра "path" из данных хэша
     * @return string|null
     */
    public function getDataPath()
    {
        return A::get($this->getData(), 'path');
    }
    
    /**
     * Получить значение дополнительного параметра из данных хэша "data"
     * @param string $name имя дополнительного параметра
     * @param mixed $default значение по умолчанию
     * @return mixed
     */
    public function getDataParam($name, $default=null)
    {
        return A::get(A::get($this->getData(), 'data', []), $name, $default);
    }
    
    /**
     * Установить значение параметра в массив дополнительных данных хэша "data"
     * @param string $name имя дополнительного параметра
     * @param mixed $value значение
     * @return mixed
     */
    public function setDataParam($name, $value)
    {
        $this->initData();
        $this->hashData['data'][$name]=$value;
    }
    
    /**
     * Зашифровать данные
     * @param mixed $data данные для шифрования
     */
    public function crypt($data)
    {
        return HHash::srEcrypt($data, $this->getKey());
    }
    
    /**
     * Расшифровать данные
     * @param mixed $str зашифрованная строка
     * @param bool $assoc возвращать ассоциативный массив.
     * По умолчанию (false) возвращать как есть.
     */
    public function decrypt($str, $assoc=false)
    {
        return HHash::srDecrypt($str, $this->getKey(), $assoc);
    }
    
    /**
     * Создание хэша процесса
     * @param array $data дополнительные данные для хэша
     */
    protected function createHash($data=[])
    {
        if(!is_array($data)) {
            $data=[];
        }
        
        $this->setHash($this->crypt([
            'id'=>$this->getConfig()->getId(),
            'path'=>$this->getConfig()->getPath(),
            'data'=>$data
        ]));
    }
    
    /**
     * Получить процент завершенности процесса
     * @return float
     */
    public function getPercent()
    {
        return $this->percent;
    }
    
    /**
     * Установить процент завершенности процесса
     * @param float $percent процент
     */
    public function setPercent($percent)
    {
        $this->percent=(float)$percent;
    }
    
    /**
     * Получить конфигурацию процесса
     * @return Config|null
     */
    public function getConfig()
    {
        return $this->config;
    }
    
    /**
     * Установить конфигурацию процесса
     * @param Config|[] $config конфигурация процесса
     */
    public function setConfig($config)
    {
        if(is_array($config)) {
            $iteratorConfig=new Config;
            
            $iteratorConfig->setConfig($config);
            
            $config=$iteratorConfig;
        }
        
        if(!($config instanceof Config)) {
            $config=null;
        }
        else {
            if(!$this->getKey()) {
                $this->setKey($config->getSecure());
            }
        }
        
        $this->config=$config;
    }
    
    /**
     * Загрузить конфигурацию
     * @param string $configId идентификатор конфигурации
     * Идетификатор конфигурации определяется как:
     * "псевдоним пути к файлу конфигурации" + "внутренний идентификатор конфигурации"
     * Разделителем является символ "." (точки). 
     * @return bool
     */
    public function loadConfig($configId)
    {
        try {
            $config=new Config;
            
            if($config->load($configId)) {
                $this->setConfig($config);
                
                return true;
            }
        }
        catch(\Exception $e) {
            $this->addErrorByException($e);
        }
        
        return false;
    }
    
    /**
     * Загрузить конфигурацию по хэшу конфигурации
     * @param string $configHash хэш конфигурации
     */
    public function loadConfigByConfigHash($configHash)
    {
        try {
            $config=new Config;
            
            if($config->loadByHash($configHash)) {
                $this->setConfig($config);
                
                return true;
            }
        }
        catch(\Exception $e) {
            $this->addErrorByException($e);
        }
        
        return false;
    }
    
    /**
     * Загрузить конфигурацию по хэшу процесса
     * @param string $hash хэш процесса
     */
    public function loadConfigByHash($hash)
    {
        $this->setHash($hash);
        
        $this->loadConfig($this->getDataPath() . '.' . $this->getDataId());
    }
    
    /**
     * Создание нового процесса
     * @param array $params дополнительные параметры для
     * обработчика создания нового процесса
     * @throws ProcessException
     * @return bool 
     */
    public function create($params=[])
    {
        try {
            $hCreate=$this->getConfig()->get('create');
            
            if(is_string($hCreate) || !is_callable($hCreate)) {
                throw new ProcessException('Параметр "create" не найден.');
            }
            
            array_unshift($params, $this);
            
            $data=call_user_func_array($hCreate, $params);
            $this->createHash($data);
            
            $this->raiseEvent('onAfterCreate');
            
            return true;
        }
        catch(\Exception $e) {
            $this->addErrorByException($e);
        }
        
        return false;
    }
    
    /**
     * Запуск следующей итерации
     * @param array $params дополнительные параметры для
     * обработчика запуска следующей итерации
     * @throws ProcessException
     * @return bool
     */
    public function next($params=[])
    {
        try {
            $hNext=$this->getConfig()->get('next');
            
            if(is_string($hNext) || !is_callable($hNext)) {
                throw new ProcessException('Параметр "next" не найден.');
            }
            
            array_unshift($params, $this);
            
            $this->raiseEvent('onBeforeNext');
            
            $percent=call_user_func_array($hNext, $params);
            
            if($percent >= 100) {
                $percent=100;
                
                $this->raiseEvent('onAfterDone');
            }
            
            $this->setPercent($percent);
                
            return true;
        }
        catch(\Exception $e) {
            $this->addErrorByException($e);
        }
        
        return false;
    }
    
    /**
     * Запуск события
     * @param string $name имя события
     * @param array $params дополнительные параметры для события
     */
    public function raiseEvent($name, $params=[])
    {
        try {
            if($hEvent=$this->getConfig()->getEvent($name)) {
                if(!is_string($hEvent) && is_callable($hEvent)) {
                    array_unshift($params, $this);
                    
                    return call_user_func_array($callback, $params);
                }
            }
        }
        catch(\Exception $e) {
            $this->addErrorByException($e);
        }
    }
    
    /**
     * Добавить ошибку
     * @param \Exception $exception
     */
    protected function addErrorByException($exception)
    {
        $error=$exception->getMessage();
        
        if($exception->getCode()) {
            $error=$exception->getCode() . ': ' . $error;
        }
        
        $this->errors[]=$error;
    }
}
