<?php
namespace common\ext\parser\components\helpers;

use common\components\helpers\HFile;
use common\components\helpers\HHash;
use common\ext\parser\models\Config;
use crud\models\ar\common\ext\parser\models\Parser;

class HParser
{
    /**
     * Путь к файлу с ключом шифрования парсера
     * @var string
     */
    const KEY_PATH='application.runtime';
    
    /**
     * Имя файла с ключом шифрования
     * @var string
     */
    const KEY_FILENAME='.parserkey';
    
    /**
     * Ключ шифрования парсера
     * @var string
     */
    private static $key=null;
    
    /**
     * Получить ссылку на действие запуска парсинга
     * @param string $configId идентификатор конфигурации парсера
     * 
     * @return string
     */
    public static function getRunParserUrl()
    {
        return '/common/crud/admin/default/ajax?cid=common_ext_parser_parser&action=run';
    }
    
    /**
     * Получить ключ шифрования парсера
     * 
     * @return string
     */
    public static function key()
    {   
        if(!static::$key) {
            $keyfile=HFile::path([\Yii::getPathOfAlias(self::KEY_PATH), self::KEY_FILENAME]);
            
            static::setKey(HFile::load($keyfile));
            
            if(!static::$key) {
                static::setKey(HHash::generatePassword(12));
                HFile::save($keyfile, static::$key, 0, true, 0750);
            }
        }
        
        return static::$key;
    }
    
    /**
     * Установить ключ шифрования
     * @param string $key новый ключ шифрования
     */
    public static function setKey($key)
    {
        static::$key=$key;
    }
    
    /**
     * Проверяет существует ли незавершенный процесс для указанной конфигурации или нет.
     * @param string $configFile псевдоним имени файла конфигурации
     * 
     * @return bool
     */
    public static function hasActiveProcess($configFile)
    {
        return (bool)Parser::model()
            ->setConfig(Config::load($configFile))
            ->getActiveProcess();
    }
    
    /**
     * Проверяет существует ли незавершенный периодический процесс для указанной конфигурации или нет.
     * @param string $configFile псевдоним имени файла конфигурации
     *
     * @return bool
     */
    public static function hasActivePeriodicProcess($configFile)
    {
        return (bool)Parser::model()
            ->setConfig(Config::load($configFile))
            ->getPeriodicActiveProcess();
    }
    
    public static function isPHPRunned($pid)
    {
        exec("ps -p {$pid}", $output);
        
        return (count($output) > 1);
    }
}