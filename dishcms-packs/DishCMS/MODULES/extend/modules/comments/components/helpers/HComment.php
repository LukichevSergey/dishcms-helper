<?php
namespace extend\modules\comments\components\helpers;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;

class HComment
{
    /**
     * Функция перевода
     * @var null|callable
     */
    private static $t=null;
    
    /**
     * @var null|array конфигурация модуля "Комментарии"
     */
    private static $config=null;    
    
    /**
     * Получить конфигурацию модуля "Комментарии"
     * 
     * Конфигурация может быть задана в (\Yii::params),
     * как ['extend'=>['modules'=>['comments'=>[...]]]]
     *
     * Имеет вид: array(
     *  'translate'=>'comments', // имя файла перевода для \Yii::t() (необязательно)  
     *  'models'=>[
     *      '\MyExampleModel', // имя класса модели
     *      ...
     *      'Наименование'=>'\MyExampleModel', // сокращенный вид
     *      ...
     *      'Наименование'=>[ // полный вид
     *          '\MyExampleModel', // имя класса модели (обязательно)
     *          'translate'=>'mycomments', // @todo имя файла перевода для \Yii::t() для данной конфигурации модели (необязательно)
     *          'itemTitle'=>'Наименование элемента',
     *          'attributeId'=>'id', // имя атрибута идентификатора у модели элемента (по умолчанию "id")
     *          'attributeTitle'=>'title', // имя атрибута наименования у модели элемента (по умолчанию "title")
     *          'parent'=>[ // родительская модель элемента (необязательно)
     *              '\MyParentExampleModel', // имя класса родительской модели (обязательно)
     *              'parent_id', // имя атрибута "вторичного ключа" у модели элемента (обязательно)
     *              'attributeId'=>'id', // имя атрибута идентификатора у родительской модели
     *              'attributeTitle'=>'title', // имя атрибута наименования у родительской модели
     *              'label'=>'Наименование списка родительских моделей',
     *              'itemLabel'=>'Наименование родительской модели'
     *          ]
     *      ]
     *  ]
     * )
     * 
     * @return array
     */
    public static function getConfig()
    {
        if(static::$config === null) {
            static::$config=[];
            
            $config=Y::param('extend.modules.comments', []);
            
            foreach(A::get($config, 'models', []) as $label=>$config) {
                if(is_numeric($label) && is_string($config)) {
                    $label=$config;
                }
                if(is_string($config)) {
                    $config=A::toa($config);                    
                }
                
                $config[0]=static::normalizeClass($config[0]);
                static::$config['models'][$config[0]]=[
                    'class'=>$config[0],
                    'label'=>$label,
                    'itemLabel'=>A::get($config, 'itemLabel', 'Model'),
                    'attributeId'=>A::get($config, 'attributeId', 'id'),
                    'attributeTitle'=>A::get($config, 'attributeTitle', 'title')
                ];
                
                $parent=A::get($config, 'parent');
                if(!empty($parent)) {
                    if(is_array($parent) && !empty($parent[0]) && !empty($parent[1])) {
                        $parent[0]=static::normalizeClass($parent[0]);
                        static::$config['models'][$config[0]]['parent']=[
                            'class'=>$parent[0],
                            'attributeParentId'=>$parent[1],
                            'attributeId'=>A::get($parent, 'attributeId', 'id'),
                            'attributeTitle'=>A::get($parent, 'attributeTitle', 'title'),
                            'label'=>A::get($parent, 'label', 'Parent'),
                            'itemLabel'=>A::get($parent, 'itemLabel', 'Parent')
                        ];
                    }
                }
            }
        }
        
        return static::$config;
    }
    
    public static function normalizeClass($class)
    {
        return '\\' . trim($class, '\\');
    }
    
    /**
     * Получить конфигурацию моделей
     * @return array
     */
    public static function getConfigModels()
    {
        return A::get(static::getConfig(), 'models', []);
    }
    
    /**
     * Получить конфигурацию модели
     * @var $class string имя класса модели
     * @return array
     */
    public static function getConfigModel($class)
    {
        return A::get(static::getConfigModels(), static::normalizeClass($class), []);
    }
    
    public static function getParents()
    {
        $parents=[];
        
        $modelsConfig=HComment::getConfigModels();
        foreach($modelsConfig as $modelClass=>$cfg) {
            if($parent=A::get($cfg, 'parent')) {
                $parents[$modelClass]=$parent;
            }
        }
        
        return $parents;
    }
    
    public static function getParentHashByModel($modelClass)
    {
        return md5(static::normalizeClass($modelClass));
    }
    
    public static function getParentHash($modelConfig)
    {
        return static::getParentHashByModel($modelConfig['class']);
    }
    
    public static function getConfigByParentHash($hash)
    {
        $parents=static::getParents();        
        foreach($parents as $modelClass=>$cfg) {
            if(md5($modelClass) === $hash) {
                return static::getConfigModel($modelClass);
            }
        }
        
        return null;
    }
    
    public static function getConfigByHash($hash)
    {
        $models=static::getConfigModels();
        foreach($models as $modelClass=>$cfg) {
            if(md5($modelClass) === $hash) {
                return $cfg;
            }
        }
        
        return null;
    }
    
    /**
     * Получить функцию перевода
     * @return null|callable
     */
    public static function t()
    {
        if(static::$t === null) {
            static::$t=Y::ct(Y::param('extend.modules.comments.translate', '\extend\modules\comments\CommentsModule.main'), 'extend.comments');
        }
        
        return static::$t;
    }
}