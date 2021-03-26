<?php
/**
 * Настройки почтовых шаблонов модуля "Аккаунты"
 *
 */
namespace accounts\models;

use common\components\helpers\HArray as A;
use common\components\helpers\HYii as Y;
use accounts\components\helpers\HAccountEmail;

class AccountEmailSettings extends \settings\components\base\SettingsModel
{
    public $id=1;
    
    /**
     * @var boolean для совместимости со старым виджетом
     * редактора admin.widget.EditWidget.TinyMCE
     */
    public $isNewRecord=false;
    
    /**
     * Значения атрибутов
     * @var array
     */
    private $values=[];
    
    /**
     * 
     * {@inheritDoc}
     * @see \CComponent::__get()
     */
    public function __get($name)
    {
        if(in_array($name, HAccountEmail::getAttributes())) {
            return A::get($this->values, $name);
        }
        
        return parent::__get($name);
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \CComponent::__set()
     */
    public function __set($name, $value)
    {
        if(in_array($name, HAccountEmail::getAttributes())) {
            $this->values[$name]=$value;
        }
        else {
            parent::__set($name, $value);
        }
    }
    
    /**
     *
     * {@inheritDoc}
     * @see \CFormModel::getAttributeNames()
     */
    public function attributeNames()
    {
        return HAccountEmail::getAttributes();
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \CModel::getAttributes()
     */
    public function getAttributes($names=null)
    {
        $values=[];
        
        $attributes=HAccountEmail::getAttributes();
        foreach($attributes as $attribute) {
            $values[$attribute]=$this->$attribute;
        }
        
        return $values;
    }
    
    /**
     * Для совместимости со старым виджетом
     * редактора admin.widget.EditWidget.TinyMCE
     */
    public function tableName()
    {
        return 'account_email_settings';
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \CFormModel::init()
     */
    public function init()
    {
        $this->default=HAccountEmail::getAttributeDefaults();
        
        parent::init();
    }
    
    /**
     * {@inheritDoc}
     * @see \settings\components\base\SettingsModel::rules()
     */
    public function rules()
    {
        return [
            [HAccountEmail::getAttributes(), 'safe'],
        ];
    }
    
    /**
     * {@inheritDoc}
     * @see \settings\components\base\SettingsModel::attributeLabels()
     */
    public function attributeLabels()
    {
        return HAccountEmail::getAttributeLabels();
    }
}