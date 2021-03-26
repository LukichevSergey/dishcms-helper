<?php
namespace ykassa\components;

use common\components\helpers\HArray as A;
use common\components\helpers\HFile;

/**
 * Конфигурация Яндекс.Кассы
 */
class ApiConfig
{
    const CUSTOM_PATH='application.config.ykassa.';
    const DEFAULT_PATH='ykassa.config.api.';

    private $data=[];

    public static function load($id)
    {
        $data=HFile::includeByAlias(self::CUSTOM_PATH . (string)$id);
        if(empty($data)) {
            $data=HFile::includeByAlias(self::DEFAULT_PATH . (string)$id);
        }
        
        if(!empty($data)) {
            return new self($data);
        }

        return false;
    }

    public function __construct($data=[])
    {
        $this->data=$data;
    }

    public function get($name, $params=[], $defaultParams=[])
    {
        if($callback=A::get($this->data, $name)) {
            if(!is_string($callback) && is_callable($callback)) {
                return call_user_func($callback, A::m($defaultParams, $params));
            }
        }

        return null;
    }
}