<?php
namespace extend\modules\forms\components\types;

use common\components\helpers\HArray as A;
use common\components\helpers\HEvent;

class Datalist extends \extend\modules\forms\components\base\Type
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
        return 'Список';
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
        return [
            'type'=>'dropDownList',
            'params'=>['data'=>static::getTypeData(A::rget(A::toa($field), 'type.datalist.params', []))]
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
            'type'=>['label'=>'Тип', 'default'=>'dropdown', 'type'=>'list', 'data'=>[
                'dropdown'=>'Drop Down List',
                'radio'=>'Radio Button List',
                'checkbox'=>'CheckBox List'
            ]],
            'multiple'=>['label'=>'Множественный выбор', 'default'=>0, 'type'=>'checkbox'],
            'event'=>'Событие получения списка элементов',
            'items'=>['label'=>'Элементы', 'default'=>'', 'type'=>'text'],
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
            $htmlOptions=[];
            if(A::get($params, 'multiple', 0)) {
                $htmlOptions['multiple']=true;
            }
            
            $data=static::getTypeData($params);
            switch(A::get($params, 'type')) {
                case 'radio':
                    echo $form->radioButtonList($widget->model, $attribute, $data, $htmlOptions);
                    break;
                    
                case 'checkbox':
                    echo $form->checkBoxList($widget->model, $attribute, $data, $htmlOptions);
                    break;
                
                case 'dropdown':
                default:
                    echo $form->dropDownList($widget->model, $attribute, $data, $htmlOptions);
                    break;
            }            
        };
    }
    
    /**
     * Получить данные типа
     * @param []|null $params параметры типа поля
     * @return []
     */
    public static function getTypeData($params=null)
    {
        $data=[];
        
        if($eventGetData=A::get($params, 'event') && ($event=HEvent::raise($eventGetData))) {
            $data=A::get($event->params, 'data', []);
        }
        elseif(preg_match_all('/^(.*)$/m', A::get($params, 'items', ''), $m, PREG_PATTERN_ORDER)) {
            foreach($m[1] as $val) {
                $val=trim($val);
                if(preg_match('/^\[([^]]+)\](.+?)$/', $val, $m)) {
                    $data[$m[1]]=$m[2];
                }
                else {
                    $data[$val]=$val;
                }
            }
        }
        
        return $data;
    }
}