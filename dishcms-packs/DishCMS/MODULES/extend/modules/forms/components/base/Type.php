<?php
/**
 * Базовый класс для типа поля формы
 */
namespace extend\modules\forms\components\base;

abstract class Type
{
    /**
     * Получить символьный идетификатор типа
     * @return string
     */
    public function getId()
    {
        return strtolower(preg_replace('/^(.*?)([^\\\\]+)$/', '$2', get_called_class()));
    }
    
    /**
     * Правила валидации поля
     * @param string $attribute имя атрибута модели формы
     * @param bool $required является обязательным
     */
    public function getRules($attribute, $required=false)
    {
        $rules=[[$attribute, 'safe']];
        
        if($required) {
            $rules[]=[$attribute, 'required', 'message'=>'Заполните поле'];
        }
        
        return $rules;
    }
    
    /**
     * Получить наименование типа
     * @return string
     */
    public function getLabel()
    {
        return 'Unknow type';
    }
    
    /**
     * Получить SQL определение типа поля формы
     * @param string $attribute имя атрибута модели формы
     * @return string
     */
    public function getSQLDefinition($attribute=null)
    {
        return 'VARCHAR(255)';
    }
    
    /**
     * Получить конфигурацию типа для CRUD редактирования
     * @param []|null $field данные поля 
     */
    public function getCrudType($field=null)
    {
        return 'text';
    }
    
    /**
     * Получить дополнительные параметры для типа
     * @return [] массив вида [paramName=>paramLabel], либо
     * [paramName=>[
     *   "label"=>подпись, 
     *   "default"=>значение по умолчанию,
     *   "type"=>тип (string, checkbox, number, list, text), по умолчанию string
     *   "data"=>[] дополнительные данные для типа,
     *   "htmlOptions"=>[] дополнительные HTML атрибуты для поля 
     * ]]
     */
    public function getParams()
    {
        return [
            
        ];
    }
    
    /**
     * Получить тип поля для виджета отображения формы
     * \common\widgets\form\ActiveForm
     * @param array $params массив параметров
     * @return string
     */
    public function getWidgetType($params=[])
    {
        return 'textField';
    }
}