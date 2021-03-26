<?php
namespace extend\modules\forms\components\types;

class Text extends \extend\modules\forms\components\base\Type
{
    /**
     * 
     * {@inheritDoc}
     * @see \extend\modules\forms\components\base\Type::getLabel()
     */
    public function getLabel()
    {
        return 'Текст';
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \extend\modules\forms\components\base\Type::getSQLDefinition()
     */
    public function getSQLDefinition()
    {
        return 'TEXT';
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \extend\modules\forms\components\base\Type::getCrudType()
     */
    public function getCrudType($field=null)
    {
        return 'textArea';
    }
    
    /**
     *
     * {@inheritDoc}
     * @see \extend\modules\forms\components\base\Type::getWidgetType()
     */
    public function getWidgetType($params=[])
    {
        return 'textArea';
    }
}