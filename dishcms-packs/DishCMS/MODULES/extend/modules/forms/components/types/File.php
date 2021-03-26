<?php
namespace extend\modules\forms\components\types;

use common\components\helpers\HArray as A;
use common\components\helpers\HEvent;

class File extends \extend\modules\forms\components\base\Type
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
        return 'Документ / Файл';
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
        return 'file';
    }
    
    /**
     *
     * {@inheritDoc}
     * @see \extend\modules\forms\components\base\Type::getParams()
     */
    public function getParams()
    {
        return [
            'types'=>['label'=>'Расширения', 'default'=>'doc,pdf,xls,rtf'],
            'limit'=>['label'=>'Максимальное кол-во файлов', 'default'=>1, 'type'=>'number', 'htmlOptions'=>['max'=>255, 'min'=>1]],
            'maxsize'=>['label'=>'Максимальный размер (байт)', 'default'=>10485760, 'type'=>'number', 'htmlOptions'=>['step'=>1048576, 'min'=>0]],
            'upload_dir'=>['label'=>'Папка загрузки <span style="font-size:10px">(абсолютный псевдоним)</span>', 'default'=>'webroot.files.uploader']
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
            echo $form->fileField($widget->model, $attribute);
        };
    }
}