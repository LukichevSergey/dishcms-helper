<?php
namespace accounts\components\helpers;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HFile;
use common\ext\email\components\helpers\HEmail;
use settings\components\helpers\HSettings;

class HAccountEmail
{
    private static $config=null;
    private static $templates=null;
    private static $attributes=null;
    private static $attributeLabels=null;
    
    /**
     * Получить модель настроек почтовых шаблонов
     * @return \accounts\models\AccountEmailSettings
     */
    public static function settings()
    {
        return HSettings::getById('accounts_email');
    }
    
    /**
     * Получить значение атрибута из настроек почтового шаблона
     * @param string $id идентификатор почтового шаблона
     * @param string|callable $attribute сокращенное имя атрибута
     * Если имя атрибута является callable, то будет возвращен результат 
     * выполнения функции.
     * @param mixed $default значение по умолчанию
     * @return mixed
     */
    public static function value($id, $attribute, $default=null)
    {
        if($config=static::getTemplateConfig($id)) {
            if(!is_string($attribute) && is_callable($attribute)) {
                return call_user_func($attribute);
            }
            elseif(!empty($attribute)) {
                $attribute=static::getAttributeName($id, $attribute);
                return static::settings()->$attribute;
            }
        }
        
        return $default;
    }
    
    /**
     * Получить конфигурации почтовых шаблонов
     * @return []
     */
    public static function getConfig()
    {
        if(static::$config === null) {
            static::$config=HFile::includeByAlias('accounts.config.email', []);
        }
        
        return static::$config;
    }
    
    /**
     * Проверить является ли почтовый шаблон активным
     * @param string $id идентификатор почтового шаблона
     * @return bool
     */
    public static function isActiveTemplate($id)
    {
        $templates=static::getTemplates();
        
        return isset($templates[$id]);
    }
    
    /**
     * Проверить является разрешено ли отправлять почтовое сообщение
     * @param string $id идентификатор шаблона
     */
    public static function isEnable($id)
    {
        if($config=static::getTemplateConfig($id)) {
            return (bool)static::value($id, A::get($config, 'enable'), true);
        }
        
        return false;
    }
    
    /**
     * Получить заголовок почтового сообщения
     * @param string $id идентификатор шаблона
     */
    public static function getSubject($id)
    {
        if($config=static::getTemplateConfig($id)) {
            $t=Y::ct('\AccountsModule.models/accountEmailSettings', 'accounts');
            return static::value($id, A::get($config, 'subject'), $t('default.subject'));
        }
        
        return null;
    }
    
    /**
     * Получить тело почтового сообщения
     * @param string $id идентификатор шаблона
     */
    public static function getBody($id)
    {
        if($config=static::getTemplateConfig($id)) {
            return static::value($id, A::get($config, 'body'), '');
        }
        
        return null;
    }
    
    /**
     * Получить почтовые шаблоны
     * @return [] массив вида array(id=>config)
     */
    public static function getTemplates()
    {
        if(static::$templates === null) {
            static::$templates=A::get(static::getConfig(), 'templates', []);
            foreach(static::$templates as $id=>$config) {
                $active=A::get($config, 'active', true);
                if(is_callable($active)) {
                    $active=call_user_func($active);
                }
                    
                if(!$active) {
                    unset(static::$templates[$id]);
                }
            }
        }
        
        return static::$templates;
    }
    
    /**
     * Получить конфигурацию шаблона
     * @param string $id идентификатор почтового шаблона
     * @return []|null
     */
    public static function getTemplateConfig($id)
    {
        return A::get(static::getTemplates(), $id);
    }
    
    /**
     * Получить конфигурацию табов
     * @param \CActiveForm $form
     * @param \CModel $model
     * @return string[][]
     */
    public static function getTabs($form, $model)
    {
        $tabs=[];
        
        $templates=static::getTemplates();
        foreach($templates as $id=>$config) {
            $tabs[A::get($config, 'title', $id)]=[
                'id'=>'tab-' . $id,
                'content'=>Y::controller()->renderPartial('accounts.modules.admin.views.settingsEmail._tab', compact('id', 'model', 'form'), true),
            ];
        }
        
        return $tabs;
    }
    
    /**
     * Получить код поля
     * @param string $id идентификатор почтового шаблона
     * @param string $attribute сокращенное имя атрибута
     * @param \CActiveForm|null $form объект формы
     * @param \accounts\models\AccountEmailSettings|null $model модель 
     * настроек почтовых шаблонов. 
     * @return string
     */
    public static function getField($id, $attribute, $form=null, $model=null)
    {
        $content=null;
        
        if($config=static::getTemplateConfig($id)) {
            $types=A::get($config, 'types', []);
        
            $widgetClass=null;
            if(!isset($types[$attribute])) {
                $type=in_array($attribute, $types) ? 'text' : 'hidden';
                $types[$attribute]=[];
            }
            else {
                if(is_array($types[$attribute])) {
                    if(isset($types[$attribute]['class'])) {
                        $widgetClass=$types[$attribute]['class'];
                        unset($types[$attribute]['class']);
                    }
                    else {
                        if(isset($types[$attribute]['type'])) {
                            $type=$types[$attribute]['type'];
                            unset($types[$attribute]['type']);
                        }
                        else {
                            $type='text';
                        }
                    }
                }
                else {
                    $type=(string)$types[$attribute];
                    $types[$attribute]=[];
                }
            }
        
            if(empty($widgetClass)) {
                if(!empty($type)) {
                    $widgetClass='\common\widgets\form\\' . ucfirst((string)$type) . 'Field';
                    $types[$attribute]['form']=$form;
                    $types[$attribute]['model']=$model;
                    $types[$attribute]['attribute']=static::getAttributeName($id, $attribute);
                }
            }
        
            if(!empty($widgetClass)) {
                $content=Y::controller()->widget($widgetClass, $types[$attribute], true);
            }
        }
        
        return $content;
    }
    
    /**
     * Получить полное имя атрибута модели настроек 
     * @param string $id идентификатор почтового шаблона
     * @param string $attribute сокращенное имя атрибута
     * @return string
     */
    public static function getAttributeName($id, $attribute)
    {
        return $id . '_' . $attribute;
    }
    
    /**
     * Получить значения атрибутов по умолчанию для модели настроек
     * @return []
     */
    public static function getAttributeDefaults()
    {
        $defaults=[];
        
        $templates=static::getTemplates();
        foreach($templates as $id=>$config) {
            if($values=A::get($config, 'defaults')) {
                if(!is_array($values) && is_callable($values)) {
                    $values=call_user_func($values);
                }
                
                if(is_array($values)) {
                    foreach($values as $attribute=>$value) {
                        $defaults[static::getAttributeName($id, $attribute)]=$value;
                    }
                }
            }
        }
        
        return $defaults;
    }
    
    /**
     * Получить массив имен атрибутов
     * @return [] 
     */
    public static function getAttributes()
    {
        if(static::$attributes === null) {
            static::$attributes=[];
            
            $templates=static::getTemplates();
            foreach($templates as $id=>$config) {
                $attributes=A::get($config, 'attributes', []);
                foreach($attributes as $attribute=>$label) {
                    static::$attributes[]=static::getAttributeName($id, $attribute);
                }
            }
        }
        
        return static::$attributes;
    }
    
    /**
     * Получить список подписей атрибутов
     * @return []
     */
    public static function getAttributeLabels()
    {
        if(static::$attributeLabels === null) {
            static::$attributeLabels=[];
            
            $templates=static::getTemplates();
            foreach($templates as $id=>$config) {
                $attributes=A::get($config, 'attributes', []);
                foreach($attributes as $attribute=>$label) {
                    static::$attributeLabels[static::getAttributeName($id, $attribute)]=$label;
                }
            }
        }
        
        return static::$attributeLabels;
    }
    
    /**
     * Получить конфигурацию шорткодов
     * @param string $id идентификатор почтового шаблона
     * @return []
     */
    public static function getShortCodes($id)
    {
        return A::get(A::toa(static::getTemplateConfig($id)), 'shortcodes', []);
    }
    
    /**
     * Получить подписи шорткодов
     * @param string $id идентификатор почтового шаблона
     * @return []
     */
    public static function getShortCodeLabels($id)
    {
        $labels=[];
        
        $shortcodes=static::getShortCodes($id);
        foreach($shortcodes as $code=>$config) {
            $labels[$code]=A::get($config, 'title');
        }
        
        return $labels;
    }
    
    /**
     * Получить значения (необработанные) шорткодов
     * @param string $id идентификатор почтового шаблона
     * @return []
     */
    public static function getShortCodeValues($id)
    {
        $values=[];
        
        $shortcodes=static::getShortCodes($id);
        foreach($shortcodes as $code=>$config) {
            $values[$code]=A::get($config, 'value');
        }
        
        return $values;
    }
    
     /**
     * Подставить значения в шорткоды
     * @param string $id идентификатор почтового шаблона
     * @param string $content текст
     * @param array $params дополнительные параметры, которые будут переданы
     * в шаблон и в функцию обработки шорткодов
     * @return boolean
     */
    public static function processShortCodes($id, $content, $params=[])
    {
        $shortcodes=static::getShortCodeValues($id);
        foreach($shortcodes as $code=>$value) {
            if(!is_string($value) && is_callable($value)) {
                $shortcodes[$code]=call_user_func($value, $params);
            }
        }
        
        return strtr($content, $shortcodes);
    }
    
    /**
     * Получить шаблон генерации письма
     * @param string $id идентификатор почтового шаблона
     * @return string псевдоним пути к шаблону генерации письма
     */
    public static function getEmailTemplate($id)
    {
        return 'accounts.views._email.template';
    }
    
    /**
     * Отправить сообщение
     * @param string $to адрес получателя
     * @param string $id идентификатор почтового шаблона
     * @param array $params дополнительные параметры, которые будут переданы
     * в шаблон и в функцию обработки шорткодов
     * @return boolean
     */
    public static function sendMail($to, $id, $params=[])
    {
        if(static::isEnable($id)) {
            return HEmail::cmsSend(
                $to,
                static::processShortCodes($id, static::getSubject($id), $params),
                ['content'=>static::processShortCodes($id, static::getBody($id), $params)],
                static::getEmailTemplate($id)
            );
        }
        
        return false;
    }
}