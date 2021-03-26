<?php
namespace Kontur\Calculator;

use Bitrix\Main;
use Bitrix\Main\Web\Json;

class Helper
{
    /**
     * @var string имя опции с идентификатором инфоблока 
     * настроек калькулятора
     */
    const SETTINGS_IBLOCK_ID_OPTION_NAME='settings_iblock_id';

    /**
     * @var string имя опции с идентификатором инфоблока 
     * заявок из калькулятора
     */
    const REQUESTS_IBLOCK_ID_OPTION_NAME='requests_iblock_id';

    /**
     * @var string имя опции с идентификатором свойства 
     * настроек полей формы калькулятора
     */
    const PROP_FORM_FIELDS_OPTION_NAME='form_fields_prop_id';

    /**
     * @var string имя опции с идентификатором свойства 
     * настроек формул калькулятора
     */
    const PROP_CALC_FORMULAS_OPTION_NAME='calc_formulas_prop_id';

    const PROP_FORM_FIELD_TYPE_INT=100;
    const PROP_FORM_FIELD_TYPE_FLOAT=200;
    const PROP_FORM_FIELD_TYPE_ENUM=400;

    /**
     * Получить идентификатор инфоблока настроек калькулятора
     *
     * @return int
     */
    public static function getSettingsIblockId()
    {
        return (int)\COption::GetOptionString('kontur.calculator', Helper::SETTINGS_IBLOCK_ID_OPTION_NAME, 35); // , 35
    }

    /**
     * Получить идентификатор инфоблока заявок из калькулятора
     *
     * @return int
     */
    public static function getRequestsIblockId()
    {
        return (int)\COption::GetOptionString('kontur.calculator', Helper::REQUESTS_IBLOCK_ID_OPTION_NAME, 36); // , 36
    }

    /**
     * Получить идентификатор свойства настройки полей формы
     *
     * @return int
     */
    public static function getFormFieldPropertyId()
    {
        return (int)\COption::GetOptionString('kontur.calculator', Helper::PROP_FORM_FIELDS_OPTION_NAME); // , 218
    }

    /**
     * Получить идентификатор свойства настройки формул
     *
     * @return int
     */
    public static function getCalcFormulasPropertyId()
    {
        return (int)\COption::GetOptionString('kontur.calculator', Helper::PROP_CALC_FORMULAS_OPTION_NAME); // , 219
    }

    public static function jsonDecode($str)
    {
        $array=@json_decode($str?:'{}', true);
        return (json_last_error() === JSON_ERROR_NONE) ? $array : [];
    }

    /**
     * Получить настройки полей калькулятора
     *
     * @param int $elementId идентификатор элемента, для которого 
     * получаются настройки
     * @param []|null $customValues значения для переопределения настроек
     * 
     * @return []
     */
    public static function getFormFields($elementId, $customValues=null)
    {
        if($customValues && is_string($customValues)) {
            return static::jsonDecode($customValues);
        }
        elseif($elementId > 0) {
            $prop=\CIBlockElement::GetProperty(static::getSettingsIblockId(), $elementId, [], ['ID'=>static::getFormFieldPropertyId()])->Fetch();
            return static::jsonDecode($prop['VALUE']??null);
        }

        return [];
    }

    /**
     * Получить настройки формул калькулятора
     *
     * @param int $elementId идентификатор элемента, для которого 
     * получаются настройки
     * @param []|null $customValues значения для переопределения настроек
     * 
     * @return []
     */
    public static function getCalcFormulas($elementId, $customValues=null)
    {
        if($customValues && is_string($customValues)) {
            return static::jsonDecode($customValues);
        }
        elseif($elementId > 0) {
            $prop=\CIBlockElement::GetProperty(static::getSettingsIblockId(), $elementId, [], ['ID'=>static::getCalcFormulasPropertyId()])->Fetch();
            return static::jsonDecode($prop['VALUE']??null);
        }

        return [];
    }

    /**
     * Нормализация строки
     *
     * @param string $str
     * @return string
     */
    public static function normalizeString($str)
    {
        return preg_replace('/\s+/', ' ', trim($str));
    }

    /**
     * Нормализация выражения
     *
     * @param string $expression выражение
     * @return string
     */
    public static function normalizeExpression($expression)
    {
        return preg_replace(
            ['/\s+/', '/,/', '/[^A-Z0-9.+-\/*()\s]/'], 
            [' ', '.', ''], 
            trim($expression)
        );
    }

    /**
     * Получить хэш значения
     *
     * @param mixed $value
     * @return string
     */
    public static function getValueHash($value)
    {
        return md5($value);
    }

    /**
     * Сравнить значение по хэшу
     *
     * @param string $hash хэш значения
     * @param mixed $value сравниваемое значение
     * @return bool
     */
    public static function isValue($hash, $value)
    {
        return ($hash === static::getValueHash($value));
    }

    /**
     * Разбор значений полей формы типа ENUM
     *
     * @param string|[] $values строка или массив значений
     * @return []
     */
    public static function parseEnumValues($values)
    {
        if(is_string($values)) {
            $values=trim($values);
            if(preg_match('/^[0-9.,\-\s]+$/', $values)) {
                $values=explode(' ', preg_replace(['/,/', '/\s+/'], ['.', ' '], $values));
            }
            elseif(preg_match('/^[0-9.,\-\s;]+$/', $values)) {
                $values=explode(';', preg_replace(['/,/', '/;+/', '/\s+/'], ['.', ';', ''], $values));
            }
            else {
                $values=explode(';', preg_replace(['/\s+/', '/;+/'], [' ', ';'], $values));
                array_walk($values, 'trim');
            }
        }
        else {
            $values=is_array($values) ? $values : [];
        }

        return $values;
    } 

    public static function treeGetControlGroupIdx(&$currentValues, $controlId, $create=true)
    {
        foreach($currentValues['children']??[] as $idx=>$child) {
            if(($child['controlId']??null) == $controlId) {
                return $idx;
            }
        }

        if($create) {
            $currentValues['children'][]=[
                'id'=>$controlId,
                'controlId'=>$controlId,
                'values'=>[],
                'children'=>[]
            ];

            return static::treeGetControlGroupIdx($currentValues, $controlId, false);
        }

        return null;
    }

    public static function treeSetControlValues(&$currentValues, $groupControlId, $controlId, $values, $customIdx=null) {
        static $idx=1;

        if(!empty($values) && is_array($values)) {
            $child=[
                'id'=>"{$controlId}_" . ($customIdx ?: $idx),
                'controlId'=>$controlId,
                'values'=>[]
            ];

            foreach($values as $key=>$value) {
                if($value) {
                    $child['values'][$key]=$value;
                }
            }

            if(!empty($child['values'])) {
                $groupControlIdx=(int)static::treeGetControlGroupIdx($currentValues, $groupControlId, true);
                $currentValues['children'][$groupControlIdx]['children'][]=$child;
                $idx++;
            }
        }
    }

    public static function treeSetControlOptions(&$currentValues, $controlId, $options=[])
    {
        foreach($currentValues['children']??[] as $idx=>$child) {
            if(($child['controlId']??null) == $controlId) {
                foreach($options as $key=>$val) {
                    $currentValues['children'][$idx][$key]=$val;
                }
            }

            if(!empty($child['children'])) {
                static::treeSetControlOptions($currentValues['children'][$idx], $controlId, $options);
            }
        }
    }
}