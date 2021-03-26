<?php
namespace Kontur\Ident;

use Bitrix\Main;

/**
 * Класс конфигурации
 */
class Config
{
    /**
     * Идентификатор конфигурации
     *
     * @var string
     */
    private $id=null;

    /**
     * Временный кэш конфигурации
     * По умолчанию (null) не инициализирован.
     * @var []|null
     */
    private $config=null;
    
    /**
     * Статические экземляры конфигураций
     *
     * @var []
     */
    private static $instances=[];
    
    /**
     * Получить объект конифигурации
     *
     * @param string $id идентфикатор конфигурации.
     * @param boolean $reload перезагрузить конфигурацию. По умолчанию 
     * (false) не перезагружать.
     * @return Config
     */
    public static function getInstance($id, $reload=false)
    {
        if($reload || !isset(static::$instances[$id])) {
            static::$instances[$id]=new static($id);
            static::$instances[$id]->load($reload);
        }

        return static::$instances[$id];
    }

    /**
     * Конструктор класса
     *
     * @param string $id идентфикатор конфигурации.
     */
    public function __construct($id)
    {
        $this->setId($id);
    }

    /**
     * Получить идентификатор конфигурации
     *
     * @return string|null идентфикатор конфигурации
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Установить идентификатор конфигурации
     *
     * @param string $id идентфикатор конфигурации
     * @return void
     */
    private function setId($id)
    {
        $this->id=$id;
    }

    /**
     * Получить файл конфигурации
     *
     * @return string|false если файла конфигурации не существует, 
     * будет возвращено false.
     */
    public function getFileName()
    {
        if($this->getId()) {
            $filename=dirname(__FILE__) . "/../config/{$this->getId()}.php";            
            return is_file($filename) ? $filename : false;
        }

        return false;
    }

    /**
     * Загрузить конфигурацию
     *
     * @param boolean $reload перезагрузить конфигурацию. По умолчанию 
     * (false) не перезагружать.
     * @return bool возвращает true, если загрузить конфигуцию удалось
     */
    public function load($reload=false)
    {
        if($filename=$this->getFileName()) {
            if($reload || ($this->config === null)) {
                $this->config=@include($filename);
                if(is_array($this->config)) {
                    return true;
                }            
                $this->config=null;    
            }
        }

        return false;
    }

    /**
     * Получить значение параметра конфигурации
     *
     * @param string $name имя параметра
     * @param mixed|null $default значение по умолчанию. По умолчанию null.
     * @return mixed
     */
    public function get($name, $default=null)
    {
        return $this->config[$name] ?? $default;
    }
}