<?php
namespace extend\modules\forms\components\types;

use common\components\helpers\HArray as A;

class Str extends \extend\modules\forms\components\base\Type
{
    /**
     *
     * {@inheritDoc}
     * @see \extend\modules\forms\components\base\Type::getRules()
     */
    public function getRules($attribute, $required=false)
    {
        return A::m(parent::getRules($attribute, $required), [
            [$attribute, 'length', 'max'=>255]
        ]);
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \extend\modules\forms\components\base\Type::getLabel()
     */
    public function getLabel()
    {
        return 'Строка';
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \extend\modules\forms\components\base\Type::getSQLDefinition()
     */
    public function getSQLDefinition()
    {
        return 'VARCHAR(255)';
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \extend\modules\forms\components\base\Type::getParams()
     */
    public function getParams()
    {
        return [
            'length'=>['label'=>'Максимальная длина', 'default'=>255, 'type'=>'number', 'htmlOptions'=>['max'=>255, 'min'=>0]]
        ];
    }
}