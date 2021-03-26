<?php
namespace extend\modules\forms\components\types;

use common\components\helpers\HArray as A;
use common\components\helpers\HEvent;

class Rel extends \extend\modules\forms\components\base\Type
{
    /**
     *
     * {@inheritDoc}
     * @see \extend\modules\forms\components\base\Type::getRules()
     */
    public function getRules($attribute, $required=false)
    {
        return A::m(parent::getRules($attribute, $required), [
        ]);
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \extend\modules\forms\components\base\Type::getLabel()
     */
    public function getLabel()
    {
        return 'Связка';
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \extend\modules\forms\components\base\Type::getSQLDefinition()
     */
    public function getSQLDefinition($attribute=null)
    {
        return "INT(11), KEY(`{$attribute}`)";
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \extend\modules\forms\components\base\Type::getCrudType()
     */
    public function getCrudType($field=null)
    {
        return 'hidden';
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \extend\modules\forms\components\base\Type::getParams()
     */
    public function getParams()
    {
        return [
            'event'=>'Событие получения значения поля',
            'model'=>'Связанная модель',
            'model_id'=>['label'=>'Имя аттрибута ID связной модели', 'default'=>'id'],
            'model_value'=>['label'=>'Имя аттрибута получения значения', 'default'=>'title'],
        ];
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \extend\modules\forms\components\base\Type::getWidgetType()
     */
    public function getWidgetType($params=[])
    {
        return function($widget, $form, $attribute) use ($params) {            
            if(!$widget->model->$attribute && ($eventGetValue=A::get($params, 'event'))) {
                if($event=HEvent::raise($eventGetValue)) {
                    $widget->model->$attribute=A::get($event->params, 'id');
                }
            }
            echo $form->hiddenField($widget->model, $attribute);
        };
    }
}