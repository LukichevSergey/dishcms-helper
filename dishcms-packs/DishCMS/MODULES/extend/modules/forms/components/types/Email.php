<?php
namespace extend\modules\forms\components\types;

use common\components\helpers\HArray as A;

class Email extends \extend\modules\forms\components\base\Type
{
    /**
     * 
     * {@inheritDoc}
     * @see \extend\modules\forms\components\base\Type::getRules()
     */
    public function getRules($attribute, $required=false)
    {
        return A::m(parent::getRules($attribute, $required), [
            [$attribute, 'email']
        ]);
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \extend\modules\forms\components\base\Type::getLabel()
     */
    public function getLabel()
    {
        return 'E-Mail';
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
}