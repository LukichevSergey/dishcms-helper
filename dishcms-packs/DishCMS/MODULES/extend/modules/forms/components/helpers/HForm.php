<?php
namespace extend\modules\forms\components\helpers;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HFile;
use common\components\helpers\HDb;
use extend\modules\forms\components\FormFactory;
use crud\models\ar\extend\modules\forms\models\Config;

/**
 * Класс-помощник для форм
 *
 */
class HForm
{
    /**
     * Имя таблицы конфигураций форм
     * @var string
     */
    const FORMS_TABLENAME='forms';
    
    /**
     * Путь к стандартным типам полей формы
     * @var string
     */
    const TYPE_PATH='extend.modules.forms.components.types';
    
    /**
     * Префикс для имени атрибута по символьному коду поля 
     * @var string
     */
    const FIELD_PREFIX='field_';
    
    /**
     * Получить имя атрибута по имени поля
     * @param string $fieldName имя поля
     * @return string
     */
    public static function getAttributeName($fieldName)
    {
        return self::FIELD_PREFIX . $fieldName;
    }
    
    /**
     * Получить имя поля по имени атрибута
     * @param string $attribute имя атрибута
     * @return string
     */
    public static function getFieldName($attribute)
    {
        if(strpos($attribute, self::FIELD_PREFIX) === 0) {
            return substr($attribute, strlen(self::FIELD_PREFIX));
        }
        
        return $attribute;
    }
    
    /**
     * Отображение виджета формы
     * @param string $code код формы
     * @param array $properties дополнительные настройки 
     * для виджета \common\widgets\form\ActiveForm
     * @param bool $captureOutput возвращать вывод виджета
     * @param bool $publishJs опубликовать скрипт обработки форм
     * @param [] $data значения атрибутов модели, для переопределения 
     * вида [attribute=>value]
     * @return mixed|string|\CWidget
     */
    public static function widget($code, $properties=[], $captureOutput=false, $publishJs=true, $data=[])
    {
        if($form=static::form($code)) {
            if($config=static::config($code)) {
                if(!$config->published) {
                    return null;
                }
                
                if(!empty($config->styles)) {
                    Y::css('form__'.$code, $config->styles);
                }
                
                if(!empty($config->js)) {
                    Y::css('form__'.$code, ';'.$config->js.';', \CClientScript::POS_READY);
                }
                
                
                $default=[
                    'model'=>$form,
                    'view'=>($config->view ?: Config::DEFAULT_VIEW),
                    'attributes'=>[],
                    'formOptions'=>[
                    ]
                ];
                
                $widgetConfig=$config->getWidgetConfig();
                $fields=$config->getFields();
                foreach($fields as $field) {
                    if($config->getFieldOption($field, 'show')) {
                        $attribute=$form->getAttributeName($field['name']);
                        if($customType=A::rget($widgetConfig, 'types.'.$field['name'])) {
                            $default['attributes'][]=$attribute;
                            $default['types'][$attribute]=$customType;
                        }
                        else {
                            $typeId=A::rget($field, 'type.id', Config::DEFAULT_FIELD_TYPE_ID);
                            if($type=static::type($typeId)) {
                                $default['attributes'][]=$attribute;
                                $default['types'][$attribute]=$type->getWidgetType($config->getTypeParams($field));
                                
                                if(($form->$attribute === null) && ($defaultValue=$config->getFieldOption($field, 'default'))) {
                                    $form->$attribute=$defaultValue;
                                }
                            }
                        }
                    }
                }
                
                if($publishJs) {
                    Y::module('extend.forms')->publishJs('js/ajaxform.js');
                }
                
                $properties=A::m($default, $properties);
                
                if(!empty($data)) {
                    $model=$properties['model'];
                    if($model instanceof \CModel) {
                        foreach($data as $attribute=>$value) {
                            $model->$attribute=$value;
                        }
                        $properties['model']=$model;
                    }
                }
                
                
                return Y::controller()->widget(
                    '\common\widgets\form\ActiveForm', 
                    $properties, 
                    $captureOutput
                );
            }
        }
    }
    
    /**
     * Получить идентификатор CRUD конфигурации формы
     * @param string $code символьный идентификатор формы
     * @return string
     */
    public static function getCrudConfigId($code)
    {
        return 'form__' . preg_replace('/[^a-z0-9]/i', '_', $code);
    }
    
    /**
     * Получить имя таблицы базы данных результатов формы 
     * @param string $code символьный идентификатор формы
     * @return string
     */
    public static function getTableName($code)
    {
        return 'form_' . preg_replace('/[^a-z0-9]/i', '_', $code);
    }
    
    public static function getClassName($code)
    {
        $config=static::config($code);
        
        if(!empty($config->model_class)) {
            $className=$config->model_class;
        }
        else {
            $className='\crud\models\ar\extend\modules\forms\models\Form' . ucfirst(preg_replace('/[^a-z0-9]/i', '_', $code));
        }
        
        return $className;
    }
    
    /**
     * Получить модель конфигурации формы по идентификатору CRUD конфигурации
     * @param string $cid
     * @param \CDbCriteria|[] $criteria дополнительный критерий выборки
     * @return Config|null
     */
    public static function getConfigByCrudConfigId($cid, $criteria=[])
    {
        if($configs=Config::model()->findAll($criteria)) {
            foreach($configs as $config) {
                if($cid === static::getCrudConfigId($config->code)) {
                    return $config;
                }
            }
        }
        
        return null;
    }
    
    /**
     * Получить модель формы по идентификатору CRUD конфигурации
     * @param string $cid
     * @return \common\components\base\ActiveRecord|null
     */
    public static function getFormByCrudConfigId($cid)
    {
        if($configs=Config::model()->findAll(['select'=>'code'])) {
            foreach($configs as $config) {
                if($cid === static::getCrudConfigId($config->code)) {
                    return static::form($config->code);
                }
            }
        }
        
        return null;
    }
    
    /**
     * Получить ссылку на страницу списка результатов (CRUD)
     * @param string $code символьный идентификатор формы
     * @return string
     */
    public static function getFormCrudIndexUrl($code)
    {
        return \Yii::app()->createUrl('/cp/crud/index', ['cid'=>static::getCrudConfigId($code)]);
    }
    
    /**
     * Получить модель конфигурации формы
     * @param string $code код формы
     * @return \crud\models\ar\extend\modules\forms\models\Config|null
     */
    public static function config($code)
    {
        return Config::model()->byCode($code)->find();
    }
    
    /**
     * Получить все доступные конфигурации форм
     * @param boolean $active получать только активные. 
     * По умолчанию (true) получать только активные.
     * @param \CDbCriteria|[] дополнительный критерий выборки
     * @return []
     */
    public static function configs($active=true, $criteria=[])
    {
        if($active) {
            return Config::model()->published()->findAll($criteria);
        }
        
        return Config::model()->findAll($criteria);
    }
    
    /**
     * Получить модель формы
     * @param string $code код формы
     * @return \common\components\base\ActiveRecord|null
     */
    public static function form($code)
    {
        return FormFactory::factory($code);
    }
    
    /**
     * Получить список доступных стандартных типов
     * @return [] массив стандартных типов вида [typeId=>typeLabel]
     */
    public static function types()
    {
        $types=[];
        
        $files=HFile::getFiles(\Yii::getPathOfAlias(self::TYPE_PATH));
        foreach($files as $file) {
            $id=lcfirst(str_replace('.php', '', $file));
            if($type=static::type($id)) {
                $types[$id]=$type->getLabel();
            }
        }
        
        asort($types, SORT_NATURAL);
        
        return $types;
    }
        
    /**
     * Получить объект типа поля формы
     * @param string $id идетификатор типа поля
     * @return \extend\modules\forms\components\base\Type|null
     */
    public static function type($id)
    {
        $class='\extend\modules\forms\components\types\\' . ucfirst($id);
        if(class_exists($class)) {
            $type=new $class;
            if($type instanceof \extend\modules\forms\components\base\Type) {
                return $type;
            }
        }
        
        return null;
    }
}
