<?php
namespace extend\modules\forms\components;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HRequest as R;
use common\components\helpers\HEvent;
use crud\components\helpers\HCrud;
use extend\modules\forms\components\helpers\HForm;
use crud\models\ar\extend\modules\forms\models\Config;

class FormFactory extends \CComponent
{
    use \common\traits\Singleton;
    
    /**
     * Фабрика форм
     * @param string $code символьный идентификатор формы
     * @return \extend\modules\forms\models\Form
     */
    public static function factory($code)
    {
        $crudConfigId=HForm::getCrudConfigId($code);
        
        $module=Y::module('common.crud');
        if(empty($module->config[$crudConfigId])) {
            if($crud=static::getCrudConfig($code)) {
                $module->config[$crudConfigId]=$crud;
                HCrud::resetConfigPrepared();
            }
        }
        
        if(!empty($module->config[$crudConfigId])) {
            return new $module->config[$crudConfigId]['class'];
        }
        
        return null;
    }
    
    protected static function getCrudConfig($code)
    {
        if($config=HForm::config($code)) {
            $roles=A::toa(explode(',', preg_replace('/[^0-9a-z_,]+/', '', $config->access_crud_roles)));
            $roles[]='sadmin';
            
            $crudConfigId=HForm::getCrudConfigId($code);
            
            $crud=[
                'class'=>HForm::getClassName($code),
                'access'=>[
                    ['allow', 'users'=>['@'], 'roles'=>$roles],
                    ['deny', 'users'=>['*']]
                ],
                'config'=>[
                    'tablename'=>HForm::getTableName($code),
                    'definitions'=>[
                        'column.pk',
                        'column.create_time',
                        'column.published'=>['label'=>'Сообщение обработано']
                    ],
                    'rules'=>[
                        'safe',
                        ['create_time', 'unsafe']
                    ],
                    'behaviors'=>[
                        'formModelBehavior'=>'\extend\modules\forms\behaviors\FormModelBehavior',
                    ],
                    'methods'=>[
                        'public function getCrudId() { return "'.$crudConfigId.'"; }',
                        'public function getCrudInfoTitle() { return "Сообщение #{$this->id}"; }',
                        'public function getAttributeName($fieldName) { return \extend\modules\forms\components\helpers\HForm::getAttributeName($fieldName); }',
                        'public function getAjaxUrl($action){return "/common/crud/default/ajax?cid='.$crudConfigId.'&action=".$action;}',
                        'public function getAjaxSubmitButton($action="send",$label="Отправить",$htmlOptions=[]){
                            $htmlOptions=A::m(["ajax"=>[
                                "type"=>"POST","dataType"=>"json","url"=>$this->getAjaxUrl($action),
                                "success"=>\'function(r){$(window).trigger("onExtendFormsFormAjaxSuccess",[r]);}\'
                            ]],$htmlOptions);
                            return \CHtml::submitButton($label,$htmlOptions);
                        }'                       
                    ]
                ],
                'buttons'=>[
                    'create'=>['label'=>$config->allow_add_results ? 'Добавить сообщение' : '']
                ],
                'crud'=>[
                    'onBeforeLoad'=>function(){
                        if($cid=R::get('cid')) {
                            if($config=HForm::getConfigByCrudConfigId($cid, ['scopes'=>'published'])) {
                                if($config->is_save_results) {
                                    return true;
                                }
                            }
                        }
                        R::e404();
                    },
                    'index'=>[
                        'url'=>'/cp/crud/index',
                        'title'=>'Форма &laquo;'.$config->title.'&raquo;',
                        'gridView'=>[
                            'dataProvider'=>[
                                'criteria'=>[
                                    'order'=>'create_time DESC'
                                ],
                            ],
                            'emptyText'=>'Сообщений не найдено',
                            'columns'=>[
                                'column.id',
                                'info'=>[
                                    'header'=>'Информация',
                                    'type'=>'column.title',
                                    'attributeTitle'=>'crudInfoTitle',
                                    'info'=>[]
                                ],
                                [
                                    'name'=>'create_time',
                                    'header'=>'Дата',
                                    'headerHtmlOptions'=>['style'=>'width:10%;text-align:center;white-space:nowrap;'],
                                    'htmlOptions'=>['style'=>'text-align:center'],
                                ],
                                [
                                    'name'=>'published',
                                    'header'=>'Обработано',
                                    'type'=>'common.ext.published',
                                    'headerHtmlOptions'=>['style'=>'width:10%;text-align:center;white-space:nowrap;']
                                ],
                                'crud.buttons'=>[
                                    'type'=>'crud.buttons',
                                    'params'=>[
                                        'template'=>'{update}{delete}',
                                        'buttons'=>[
                                            'update'=>[
                                                'label'=>'<span class="glyphicon glyphicon-user"></span> Редактировать',
                                                'options'=>['class'=>'btn btn-xs btn-primary w100', 'style'=>'margin-top:2px']
                                            ],
                                            'delete'=>[
                                                'label'=>'<span class="glyphicon glyphicon-remove"></span> Удалить',
                                                'options'=>['class'=>'btn btn-xs btn-danger w100', 'style'=>'margin-top:2px']
                                            ],
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'create'=>[
                        'url'=>'/cp/crud/create',
                        'title'=>'Добавление записи'
                    ],
                    'update'=>[
                        'url'=>'/cp/crud/update',
                        'title'=>'Редактирование записи'
                    ],
                    'delete'=>[
                        'url'=>'/cp/crud/delete'
                    ],
                    'form'=>[
                        'htmlOptions'=>['enctype'=>'multipart/form-data'],
                        'attributes'=>[
                            'published'=>'checkbox'
                        ]
                    ]
                ],
                'public'=>[
                    'access'=>[
                        ['deny', 'users'=>'*']
                    ],
                    'controllers'=>[
                        '\extend\modules\forms\behaviors\FormPublicAjaxControllerBehavior'
                    ]
                ]
            ];
            
            $publicRoles=A::toa(explode(',', preg_replace('/[^0-9a-z_,]+/', '', $config->access_public_roles)));
            if(!empty($publicRoles)) {
                $crud['public']['access']=[
                    ['allow', 'users'=>'@', 'roles'=>$publicRoles],
                    ['deny', 'users'=>'*']                    
                ];
            }
            else {
                $crud['public']['access']=[['allow', 'users'=>'*']];
            }
            
            if(\D::isDevMode()) {
                $crud['crud']['breadcrumbs']['Формы']='/cp/crud/index?cid=forms';
            }
            
            foreach($config->getFields() as $field) {
                $typeId=A::rget($field, 'type.id', Config::DEFAULT_FIELD_TYPE_ID);
                if($type=HForm::type($typeId)) {
                    $attribute=HForm::getAttributeName($field['name']);
                    
                    $crud['params']['attributes'][$attribute]['type']=A::get($field, 'type');
                    $crud['params']['attributes'][$attribute]['type']['id']=$typeId;
                    
                    $crud['config']['definitions'][$attribute]=[
                        'type'=>$type->getSQLDefinition($attribute),
                        'label'=>$field['label']
                    ];
                    
                    $crud['config']['rules']=A::m($crud['config']['rules'], $type->getRules($attribute, (bool)$config->getFieldOption($field, 'required')));
                    
                    $crud['crud']['form']['attributes'][$attribute]=$type->getCrudType($field);
                    $crud['crud']['index']['gridView']['columns']['info']['info'][$field['label']]='$data->' . $attribute;
                }
                
            }
            
            if(!empty($config->model_behavior)) {
                $behaviorName=lcfirst(preg_replace('/^(.*)([^\\\\]+)$/', '$2', $config->model_behavior));
                $crud['config']['behaviors']=[$behaviorName=>$config->model_behavior];
            }
            
            HEvent::raise('onExtendFormsFormFactoryAfterCrud', [
                'cid'=>$crudConfigId,
                'crud'=>&$crud
            ]);
            
            return $crud;
        }
        
        return null;
    }
}