<?php

namespace seo\ext\sitemap\components\helpers;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HFile;

class HSitemap
{
    /**
     * Нормализация даты
     *
     * Если дата передана пустая, то будет возвращена 
     * текущая дата.
     * @param int|string|\DateTime $date дата
     * @return string дата в формате стандарта ISO 8601
     */
    public static function normalizeDate($date)
    {
        if(!$date || !preg_replace('/[^1-9]+/', '', $date)) {
            return date('c');
        }
        elseif(ctype_digit($date)) {
            return date('c', $date);
        }
        elseif(($date instanceof \DateTime) || ($date instanceof \DateTimeImmutable)) {
            return $date->format('c');
        }
        elseif($time=strtotime($date)) {
            return date('c', $time);
        }

        return date('c');
    }

    /**
     * Получить связь "meta" для модели
     *
     * @param \CActiveRecord $model
     * @return \CActiveRecord|null
     */
    public static function getMeta($model)
    {
        if($model instanceof \CActiveRecord) {
            try { 
                return $model->meta;
            }
            catch(\Exception $e) {
                return null;
            }
        }

        return null;
    }

    /**
     * Получить значение приоритета
     *
     * @param \CActiveRecord $model
     * @return string|null
     */
    public static function getPriority($model, $priority=null)
    {
        if($priority && is_numeric($priority)) {
            return $priority;
        }
        elseif($priority && !is_string($priority) && is_callable($priority)) {
            return call_user_func_array($priority, [$model]);
        }

        return ($meta=static::getMeta($model)) ? $meta->priority : null;
    }

    /**
     * Получить значение частоты обновления
     *
     * @param \CActiveRecord $model
     * @return string|null
     */
    public static function getChangeFreq($model, $changeFreq=null)
    {
        if(in_array($changeFreq, static::getChangeFreqs())) {
            return $changeFreq;
        }
        elseif($changeFreq && !is_string($changeFreq) && is_callable($changeFreq)) {
            return call_user_func_array($changeFreq, [$model]);
        }
        
        return ($meta=static::getMeta($model)) ? $meta->changefreq : null;
    }

    /**
     * Получить список доступных значения параметра частоты обновления.
     *
     * @return []
     */
    public static function getChangeFreqs()
    {
        return [
            'always'=>'always', 
            'hourly'=>'hourly', 
            'daily'=>'daily', 
            'weekly'=>'weekly', 
            'monthly'=>'monthly', 
            'yearly'=>'yearly', 
            'never'=>'never'
        ];
    }

    /**
     * Имя конфигурации карты сайты
     *
     * @param string $name
     * @return []|false возвращает false, если файл конфигурации не найден 
     * или не возвращает массив.
     */
    public static function loadConfig($name)
    {
        $filename=\Yii::getPathOfAlias('application.config.sitemaps') . "/{$name}.php";

        if(!is_file($filename)) {
            $filename=\Yii::getPathOfAlias('seo.ext.sitemap.config.sitemaps') . "/{$name}.php";
        }

        $config=HFile::includeFile($filename, false);

        return is_array($config) ? $config : false;
    }

    /**
     * Получает значение параметра типа callable.
     * 
     * @param mixed $value обрабатываемое значение
     * @param [] $params дополнительные параметры для callable функции
     * @return mixed
     */
    public static function getCallableValue($value, $params=[])
    {
        if(!is_string($value) && is_callable($value)) {
            return call_user_func_array($value, $params);
        }

        return $value;
    }

    /**
     * Получает значение параметра, который может быть определен, 
     * как имя атрибута модели.
     * 
     * @param \CActiveRecord $model модель
     * @param mixed $value обрабатываемое значение
     * @return mixed
     */
    public static function getAttributeValue($model, $value)
    {
        if(is_string($value)) {
            return $model->$value;
        }
        elseif(is_callable($value)) {
            return call_user_func_array($value, [$model]);
        }

        return $value;
    }

    
}