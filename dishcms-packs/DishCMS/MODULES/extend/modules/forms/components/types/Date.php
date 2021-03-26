<?php
namespace extend\modules\forms\components\types;

use common\components\helpers\HArray as A;
use common\components\helpers\HEvent;

class Date extends \extend\modules\forms\components\base\Type
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
        return 'Дата';
    }
    
    /**
     *
     * {@inheritDoc}
     * @see \extend\modules\forms\components\base\Type::getSQLDefinition()
     */
    public function getSQLDefinition($attribute=null)
    {
        return "VARCHAR(255)";
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \extend\modules\forms\components\base\Type::getCrudType()
     */
    public function getCrudType($field=null)
    {
        return 'text';
    }
    
    /**
     *
     * {@inheritDoc}
     * @see \extend\modules\forms\components\base\Type::getParams()
     */
    public function getParams()
    {
        return [            
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
            echo $form->textField($widget->model, $attribute);
        };
    }
}