<?php
namespace extend\modules\forms\components\types;

use common\components\helpers\HArray as A;
use common\components\helpers\HEvent;

class Number extends \extend\modules\forms\components\base\Type
{
    /**
     *
     * {@inheritDoc}
     * @see \extend\modules\forms\components\base\Type::getRules()
     */
    public function getRules($attribute, $required=false)
    {
        return A::m(parent::getRules($attribute, $required), [
            [$attribute, 'numerical']
        ]);
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \extend\modules\forms\components\base\Type::getLabel()
     */
    public function getLabel()
    {
        return 'Целое число';
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \extend\modules\forms\components\base\Type::getSQLDefinition()
     */
    public function getSQLDefinition($attribute=null)
    {
        return "INT(11)";
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \extend\modules\forms\components\base\Type::getCrudType()
     */
    public function getCrudType($field=null)
    {
        return [
            'type'=>'number',
            'params'=>['htmlOptions'=>['class'=>'form-control w50']]
        ];
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \extend\modules\forms\components\base\Type::getParams()
     */
    public function getParams()
    {
        return [
            'min'=>'Минимальное значение',
            'max'=>'Максимальное значение',
            'step'=>['label'=>'Шаг', 'default'=>'1'],
        ];
    }
}