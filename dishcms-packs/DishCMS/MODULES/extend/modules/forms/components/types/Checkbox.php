<?php
namespace extend\modules\forms\components\types;

use common\components\helpers\HArray as A;

class Checkbox extends \extend\modules\forms\components\base\Type
{
    /**
     *
     * {@inheritDoc}
     * @see \extend\modules\forms\components\base\Type::getRules()
     */
    public function getRules($attribute, $required=false)
    {
        return A::m(parent::getRules($attribute, $required), [
            [$attribute, 'boolean']
        ]);
    }
    
    /**
     *
     * {@inheritDoc}
     * @see \extend\modules\forms\components\base\Type::getLabel()
     */
    public function getLabel()
    {
        return 'CheckBox';
    }
    
    /**
     *
     * {@inheritDoc}
     * @see \extend\modules\forms\components\base\Type::getSQLDefinition()
     */
    public function getSQLDefinition()
    {
        return 'TINYINT(1)';
    }
    
    /**
     *
     * {@inheritDoc}
     * @see \extend\modules\forms\components\base\Type::getCrudType()
     */
    public function getCrudType($field=null)
    {
        return 'checkbox';
    }
}