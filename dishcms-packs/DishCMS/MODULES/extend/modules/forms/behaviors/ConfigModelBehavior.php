<?php
namespace extend\modules\forms\behaviors;

use common\components\helpers\HArray as A;
use common\components\helpers\HDb;
use extend\modules\forms\components\helpers\HForm;

class ConfigModelBehavior extends \CBehavior
{
    /**
     * Правила валидации модели
     * @return []
     */
    public function rules()
    {
         return [
             ['title, code', 'required'],
             ['code', 'match', 'pattern'=>'/^[a-z][a-z0-9.-_]+$/'],
             ['description', 'safe'],
             ['access_crud_roles', 'safe'],
             ['access_public_roles', 'safe'],
             ['model_class, model_behavior', 'safe'],
             ['email_to, email_subject, email_view', 'safe'],
             ['view', 'safe'],
             ['styles, js', 'safe'],
             ['is_send_mail, is_save_results, allow_add_results', 'boolean']
         ];
    }
    
    /**
     * Условия выборки
     * @return []
     */
    public function scopes()
    {
        return [
            'isSaveResults'=>['condition'=>'is_save_results=1']
        ];
    }
    
    /**
     * Scope: по коду форму
     * @param string $code код формы
     * @return \common\components\base\ActiveRecord
     */
    public function byCode($code)
    {
        $c=HDb::criteria();
        
        $c->addColumnCondition(['code'=>$code]);
        
        $this->getOwner()->getDbCriteria()->mergeWith($c);
        
        return $this->getOwner();
    }
    
    /**
     * Получить список доступных полей.
     * Возвращаются активные и валидные только поля.
     * @return [] 
     */
    public function getFields()
    {
        $fields=[];
        
        $config=HForm::config($this->owner->code);
        $_fields=$config->fieldsBehavior->get(true);
        foreach($_fields as $field) {
            if(!empty($field['name']) && !empty($field['type']) && !empty($field['label'])) {
                $fields[]=$field;
            }
        }
        
        return $fields;
    }
    
    /**
     * Получить поля для почтового уведомления
     * @return []
     */
    public function getEmailFields()
    {
        $fields=[];
        
        $config=HForm::config($this->owner->code);
        $_fields=$this->getFields();
        foreach($_fields as $field) {
            if($config->getFieldOption($field, 'email')) {
                $fields[]=$field;
            }
        }
        
        return $fields;
    }
    
    /**
     * Получить параметры типа
     * @param array $field конфигурация поля
     * @return []
     */
    public function getTypeParams($field)
    {
        return A::toa(A::rget($field, 'type.' . A::rget($field, 'type.id', 'unknow') . '.params'));
    }
    
    /**
     * Получить значение параметра типа
     * @param array $field конфигурация поля
     * @param string $name имя параметра
     * @return []
     */
    public function getTypeParam($field, $name)
    {
        $value=A::get($this->getTypeParams($field), $name);
        
        if($value === null) {
            if($typeId=A::rget($field, 'type.id')) {
                if($type=HForm::type($typeId)) {
                    $params=$type->getParams();
                    return A::rget($params, "{$name}.default");
                }
            }
        }
        
        return $value;
    }
    
    /**
     * Получить параметры поля
     * @param array $field конфигурация поля
     * @return []
     */
    public function getFieldOptions($field)
    {
        return A::get($field, 'options', []);
    }
    
    /**
     * Получить значение параметра поля
     * @param array $field конфигурация поля
     * @param string $name имя параметра
     * @param mixed $default значение по умолчанию
     * @return []
     */
    public function getFieldOption($field, $name, $default=null)
    {
        return A::get($this->getFieldOptions($field), $name, $default);
    }
    
    /**
     * Получить данные поля по имени атрибута
     * @param string $attribute имя атрибута
     * @return []|null
     */
    public function getFieldByAttribute($attribute)
    {
        $fields=$this->getFields();
        foreach($fields as $field) {
            if($attribute === HForm::getAttributeName($field['name'])) {
                return $field;
            }
        }
        
        return null;
    }
    
    /**
     * Получить значение параметра типа поля по имени атрибута
     * 
     * @param string $attribute имя атрибута
     * @param string $name имя параметра
     * @return mixed|null
     */
    public function getTypeParamByAttribute($attribute, $name)
    {
        if($field=$this->getFieldByAttribute($attribute)) {
            return $this->getTypeParam($field, $name);
        }
        
        return null;
    }
    
    /**
     * Получить значение параметра поля по имени атрибута
     *
     * @param string $attribute имя атрибута
     * @param string $name имя параметра
     * @param mixed $default значение по умолчанию
     * @return mixed|null
     */
    public function getFieldOptionByAttribute($attribute, $name, $default=null)
    {
        if($field=$this->getFieldByAttribute($attribute)) {
            return $this->getFieldOption($field, $name, $default);
        }
        
        return $default;
    }
    
    /**
     * 
     * @return []
     */
    public function getWidgetConfig()
    {
        $config=[];
        
        $config['view']=$this->owner->view;
        
        $config['types']=[];
        $types=$this->owner->widgetTypesBehavior->get(true);
        foreach($types as $type) {
            if(!empty($type['attribute']) && !empty($type['value'])) {
                if(preg_match('/^\s*{(.*?)\}\s*$/sim', $type['value'], $m)) {
                    $config['types'][$type['attribute']]=function ($widget, $form, $attribute) use ($m){
                        return eval(trim($m[1]));
                    };
                }
                else {
                    $config['types'][$type['attribute']]=$type['value'];
                }
            }
        }
        
        return $config;
    }
}