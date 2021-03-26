<?php
/**
 * Формы. Конфигурации
 * 
 */
use common\components\helpers\HRequest as R;
use common\components\helpers\HFile;
use extend\modules\forms\components\helpers\HForm;
use crud\models\ar\extend\modules\forms\models\Config;

return [
    'class'=>'\crud\models\ar\extend\modules\forms\models\Config',
    'access'=>[
        ['allow', 'users'=>['@'], 'roles'=>['admin', 'sadmin']],
        ['deny', 'users'=>['*']]
    ],
    'config'=>[
        'tablename'=>HForm::FORMS_TABLENAME,
        'definitions'=>[
            'column.pk',
            'column.create_time',
            'column.update_time',
            'column.published'=>['label'=>'Активен'],
            'title'=>['type'=>'string', 'label'=>'Наименование формы'],
            'code'=>['type'=>'VARCHAR(255),KEY(`code`)', 'label'=>'Код'],
            'description'=>['type'=>'string', 'label'=>'Описание'],
            
            'is_send_mail'=>['type'=>'TINYINT(1) NOT NULL DEFAULT 0', 'label'=>'Отправлять уведомления'],
            'is_save_results'=>['type'=>'TINYINT(1) NOT NULL DEFAULT 0', 'label'=>'Просмотр результатов в разделе администрирования'],
            'allow_add_results'=>['type'=>'TINYINT(1) NOT NULL DEFAULT 0', 'label'=>'Разрешено добавлять результаты формы в разделе администрирования'],
            
            'fields'=>['type'=>'LONGTEXT', 'label'=>'Поля формы'],
            
            'model_class'=>['type'=>'string', 'label'=>'Имя класса модели формы'],
            'model_behavior'=>['type'=>'string', 'label'=>'Дополнительное поведение для модели формы'],
            
            'access_crud_roles'=>['type'=>'string', 'label'=>'Роли'],
            'access_public_roles'=>['type'=>'string', 'label'=>'Роли для публичной части'],
            
            'email_to'=>['type'=>'string', 'label'=>'Электронный адрес для почтовых уведомлений'],
            'email_subject'=>['type'=>'string', 'label'=>'Заголовок письма уведомления'],
            'email_view'=>['type'=>'string', 'label'=>'Шаблон письма уведомления'],
            
            'view'=>['type'=>'string', 'label'=>'Шаблон отображения формы по умолчанию'],
            'widget_types'=>['type'=>'LONGTEXT', 'label'=>'\common\widgets\form\ActiveForm::$types'],
            
            'styles'=>['type'=>'LONGTEXT', 'label'=>'Дополнительные CSS стили'],
            'js'=>['type'=>'LONGTEXT', 'label'=>'Дополнительные скрипты (javascript)'],
            
        ],
        'behaviors'=>[
            'configModelBehavior'=>'\extend\modules\forms\behaviors\ConfigModelBehavior',
            'fieldsBehavior'=>[
                'class'=>'\common\ext\dataAttribute\behaviors\DataAttributeBehavior',
                'attribute'=>'fields',
                'addColumn'=>false
            ],
            'widgetTypesBehavior'=>[
                'class'=>'\common\ext\dataAttribute\behaviors\DataAttributeBehavior',
                'attribute'=>'widget_types',
                'addColumn'=>false
            ],
        ],
        'consts'=>[
            'DEFAULT_FIELD_TYPE_ID'=>'str',
            'DEFAULT_VIEW'=>'active_form',
            'PRESET_DIR'=>'extend.modules.forms.data.forms'
        ],
        'methods'=>[
            'public function getFieldsInfo() { 
                $html="";$fields=$this->getFields();
                foreach($fields as $field) { 
                    if($type=\extend\modules\forms\components\helpers\HForm::type($field["type"]["id"])) {
                        $html.=\'<br/><small><b>[\' . $field["name"].\']</b> \'.$field["label"] . \' (\' . $type->getLabel() . \')</small>\';
                    }
                }
                return $html;
            }'
        ]
    ],
    'menu'=>[
        'backend'=>['label'=>'Формы']
    ],
    'buttons'=>[
        'create'=>['label'=>'Добавить форму'],
        'custom'=>function() {
            ?><button class="btn btn-info pull-right" data-toggle="modal" data-target="#modalImportFormConfig" style="margin-right:5px">Загрузить форму</button>
            <div class="modal fade" id="modalImportFormConfig" tabindex="-1" role="dialog" aria-labelledby="modalImportFormConfigLabel" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="modalImportFormConfigLabel">Загрузка конфигурации формы</h4>
                  </div>
                  <div class="modal-body">
                    <div class="alert alert-success js-import-form-success" style="display:none"></div>
                    <div class="alert alert-info js-import-form-info" style="display:none"></div>
                    <div class="alert alert-danger js-import-form-error" style="display:none"></div>
                    <input type="file" accept="application/json" class="js-import-form-file" />
                    <?php 
                    $presets=HFile::getFiles(\Yii::getPathOfAlias(Config::PRESET_DIR));
                    if(!empty($presets)):
                    ?>
                    <div class="panel panel-default" style="margin-top:20px">
                    	<div class="panel-heading">Доступные конфигурации</div>
  						<div class="panel-body"><?php 
  						    foreach($presets as $preset): 
  						        $ext=pathinfo($preset, PATHINFO_EXTENSION);
  						        if($ext=='json') {
  						            $name=pathinfo($preset, PATHINFO_FILENAME); 
      						        echo \CHtml::link($name, 'javascript:;', [
      						            'data-preset'=>$name,
      						            'class'=>'label label-default js-import-form-formfile', 
      						            'style'=>'margin-right: 5px'
      						        ]);
  						        }
  						    endforeach;
  						?></div>
  					</div>
                    <?php
                    endif;
                    ?>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                    <button type="button" class="btn btn-primary js-import-form-btn" data-loading-text="Идет загрузка конфигурации формы...">Загрузить</button>
                  </div>
                </div>
              </div>
            </div>
            <script>$(document).ready(function() {
            	function j(name){return $('.js-import-form-' + name);}
                function error(msg){if(typeof msg == 'undefined'){j('error').text('').hide();}else{j('error').text(msg).show();}}
                function info(msg){if(typeof msg == 'undefined'){j('info').text('').hide();}else{j('info').text(msg).show();}}
                function success(msg){if(typeof msg == 'undefined'){j('success').text('').hide();}else{j('success').text(msg).show();}}
                function run(file) {
                    error();
                    j('btn').button('loading');
                    j('file').hide();
                    info('Идет загрузка файла на сервер...');
                                        
                    var data=new FormData();
                    data.append('file', file);
                    data.append('cid', 'forms');
                    data.append('action', 'import');
                    $.ajax({
                        url: '/common/crud/admin/default/ajax',
                        type: 'POST',
                        data: data,
                        cache: false,
                        dataType: 'json',
                        processData: false,
                        contentType: false,
                        success: function( respond, textStatus, jqXHR ) {
                        	info();
                            if( typeof respond.error === 'undefined' ) {
                                success('Загрузка формы завершена');
                                setTimeout(function(){window.location.reload();}, 2000);
                            }
                            else {
                                error('ОШИБКИ ОТВЕТА сервера: ' + respond.error);
                            }
                        },
                        error: function( jqXHR, textStatus, errorThrown ){
                        	info();
                            error('ОШИБКИ AJAX запроса: ' + textStatus);
                        }
                    });
                }
                $(document).on('click', '.js-import-form-btn', function(e) {
                    e.stopPropagation();e.preventDefault();error();info();
                    if(j('file').prop('files').length) {
                        if(j('file').prop('files')[0].type == 'application/json') run(j('file').prop('files')[0]);
                        else error('Разрешены к загрузке только файлы с расширением *.json');
                    }
                    else {
                        var $presets=$('.js-import-form-formfile.label-primary');
                        if($presets.length > 0) {
                        	j('btn').button('loading');
                            var presets=[];
                            $presets.each(function(){presets.push($(this).data('preset'));});
                            $.post('/common/crud/admin/default/ajax', {
                                cid: 'forms',
                                action: 'importPreset',
                                presets: presets
                            }, function(response) {
                            	info();
                                if(response.success) {
                            		success('Загрузка формы завершена');
                                	setTimeout(function(){window.location.reload();}, 2000);
                                }
                                else {
                                	error('Произошла ошибка на сервере');
                                }
                            }, 'json');
                        }
                        else {
                        	error('Необходимо выбрать файл');
                        }
                    }
                });
                $(document).on('click', '.js-import-form-file', function(e) {
                	j('formfile').removeClass('label-primary');
                    j('formfile').addClass('label-default');
                });
                $(document).on('click', '.js-import-form-formfile', function(e) {
                    e.stopPropagation();e.preventDefault();j('file').val(''),$target=$(e.target);
                    if($target.hasClass('label-primary')) $target.removeClass('label-primary').addClass('label-default');
                    else $target.removeClass('label-default').addClass('label-primary');
                });                
            });</script>
            <?php 
        }
    ],
    'crud'=>[
        'controllers'=>[
            '\extend\modules\forms\behaviors\ConfigAjaxCrudControllerBehavior'
        ],
        'acontrollers'=>[
            '\extend\modules\forms\behaviors\ConfigCrudControllerBehavior'
        ],
        'onBeforeLoad'=>function(){if(!\D::isDevMode()) R::e404();},
        'index'=>[            
            'url'=>'/cp/crud/index',
            'title'=>'Формы',
            'gridView'=>[
                'dataProvider'=>[
                    'criteria'=>[                        
                    ],
		            'sort'=>['defaultOrder'=>'`title`, id DESC']
                ],
                'summaryText'=>'',
                'columns'=>[
                    'column.id',                    
                    [
                        'name'=>'code',
                        'type'=>'raw',
                        'value'=>'$data->code',
                        'headerHtmlOptions'=>['style'=>'width:15%;text-align:center;white-space:nowrap;'],
                        'htmlOptions'=>['style'=>'text-align:center'],
                    ],
                    [
                        'type'=>'column.title',
                        'header'=>'Форма',
                        'info'=>[
                            'Почтовые уведомления'=>'($data->is_send_mail ? "<span class=\"label label-success\">включены</span>" : "<span class=\"label label-danger\">отключены</span>")',
                            'Просмотр результатов'=>'($data->is_save_results ? "<span class=\"label label-success\">включен</span>" : "<span class=\"label label-danger\">отключен</span>")',
                            'Поля формы'=>'$data->getFieldsInfo()',
                            'Описание'=>'$data->description'
                        ]
                    ],
                    [
                        'name'=>'create_time',
                        'header'=>'Дата',
                        'headerHtmlOptions'=>['style'=>'width:10%;text-align:center;white-space:nowrap;'],
                        'htmlOptions'=>['style'=>'text-align:center'],
                    ],
                    [
                        'name'=>'published',
                        'header'=>'Активен',
                        'type'=>'common.ext.published',
                        'headerHtmlOptions'=>['style'=>'width:10%;text-align:center;white-space:nowrap;']
                    ],
                    'crud.buttons'=>[
                        'type'=>'crud.buttons',
                        'params'=>[
                            'template'=>'{results}{update}{delete}<br/><br/>{download}',
                            'buttons'=>[
                                'results'=>[
                                    'url'=>'$data->is_save_results ? \extend\modules\forms\components\helpers\HForm::getFormCrudIndexUrl($data->code) : "javascript:;"',
                                    'label'=>'<span class="glyphicon glyphicon-list"></span> Результаты',
                                    'options'=>['class'=>'btn btn-xs btn-info w100', 'style'=>'margin-top:2px', 'target'=>'_blank']
                                ],
                                'update'=>[
                                    'label'=>'<span class="glyphicon glyphicon-user"></span> Редактировать',
                                    'options'=>['class'=>'btn btn-xs btn-primary w100', 'style'=>'margin-top:2px']
                                ],
                                'delete'=>[
                                    'label'=>'<span class="glyphicon glyphicon-remove"></span> Удалить',
                                    'options'=>['class'=>'btn btn-xs btn-danger w100', 'style'=>'margin-top:2px']
                                ],
                                'download'=>[
                                    'url'=>'\Yii::app()->createUrl("/cp/crud/action", ["cid"=>"forms", "action"=>"save", "id"=>$data->id])',
                                    'label'=>'<span class="glyphicon glyphicon-save"></span> Сохранить',
                                    'options'=>['class'=>'btn btn-xs btn-success w100', 'style'=>'margin-top:2px', 'target'=>'_blank']
                                ]                            
                            ]
                        ]
                    ]
                ]
            ]
        ],
        'create'=>[
            'url'=>'/cp/crud/create',
            'title'=>'Новая форма',
        ],
        'update'=>[
            'url'=>['/cp/crud/update'],
            'onBeforeSetTitle'=>function($model) {
                return "Редактирование формы &laquo;{$model->title}&raquo;";
            }
        ],
        'delete'=>[
            'url'=>['/cp/crud/delete'],
        ],
        'form'=>[
            'htmlOptions'=>['enctype'=>'multipart/form-data'],
        ],
        'tabs'=>[
            'main'=>[
                'title'=>'Основные',
                'attributes'=>function(&$model) {
                    if($model->isNewRecord && !$model->view) {
                        $model->view=Config::DEFAULT_VIEW;
                    }
                    
                    return [
                        'published'=>'checkbox',
                        'title',
                        'code',
                        
                        'description'=>[
                            'type'=>'tinyMce',
                            'params'=>['full'=>false]
                        ]
                    ];
                }
            ],
            'fields'=>[
                'title'=>'Поля формы',
                'attributes'=>function(&$model) {
                    return [
                        'fields'=>[
                            'type'=>'common.ext.data',
                            'behaviorName'=>'fieldsBehavior',
                            'params'=>[
                                'wrapperOptions'=>['style'=>'width:100% !important'],
                                'header'=>[
                                    'name'=>['title'=>'Код*', 'htmlOptions'=>['style'=>'width:20%']],
                                    'label'=>'Наименование*',
                                    'type'=>['title'=>'Тип*', 'htmlOptions'=>['style'=>'width:30%']],
                                    'options'=>['title'=>'Параметры*', 'htmlOptions'=>['style'=>'width:20%']],
                                ],
                                'types'=>[
                                    'name'=>['type'=>'string','params'=>['htmlOptions'=>['style'=>'height:16px;min-height:25px;font-size:12px;padding:4px;']]],
                                    'label'=>['type'=>'string','params'=>['htmlOptions'=>['style'=>'height:16px;min-height:25px;font-size:12px;padding:4px;']]],
                                    'type'=>[
                                        'type'=>'raw',
                                        'params'=>[
                                            'ajax-tpl-url'=>'/cp/crud/ajax?cid=forms&action=getTypeField'
                                        ]
                                    ],
                                    // default, required, show, editable
                                    'options'=>['type'=>'raw', 'view'=>'extend.modules.forms.views.crud.forms._field_options'],                                    
                                ],
                                'defaultActive'=>true,
                            ]
                        ]
                    ];
                }
            ],
            'widget'=>[
                'title'=>'Параметры виджета отображения',
                'attributes'=>function(&$model) {
                    $attributes=[
                        'view'=>[
                            'type'=>'text',
                            'params'=>['htmlOptions'=>['class'=>'form-control w100']]
                        ],
                    ];
                    /*
                    $attributes['code.html.active_form']='<div class="page-header"><h1>Дополнительные параметры для виджета \common\widgets\form\ActiveForm</h1></div>';
                    $attributes['code.html.active_form_note']='<div class="alert alert-danger"><strong>Крайне не рекомендуется</strong> использовать callable-функции</div>';
                    $attributes['widget_types']=[
                        'type'=>'common.ext.data',
                        'behaviorName'=>'widgetTypesBehavior',
                        'params'=>[
                            'wrapperOptions'=>['style'=>'width:100% !important'],
                            'deleteButtonOptions'=>['class'=>'btn btn-danger btn-xs'],
                            'header'=>[
                                'attribute'=>['title'=>'Код поля', 'htmlOptions'=>['style'=>'width:20%']],
                                'value'=>[
                                    'title'=>'Значение',
                                    'htmlOptions'=>[
                                        'title'=>'\common\widgets\form\ActiveForm::$types - это методы класса \CActiveForm для отображения атрибутов модели в форме.'
                                            . "\n"
                                            . 'Mожет быть определено как callable-функция function($widget, $form, $attribute) для этого заключите программный код в {} (фигурные скобки).'
                                    ]
                                ]
                            ],
                            'types'=>[
                                'attribute'=>['type'=>'string','params'=>['htmlOptions'=>['style'=>'height:16px;min-height:25px;font-size:12px;padding:4px;']]],
                                'value'=>['type'=>'text','params'=>['htmlOptions'=>['style'=>'height:16px;min-height:25px;font-size:12px;padding:4px;']]]
                            ],
                            'defaultActive'=>true,
                        ],
                    ];
                    */
                    return $attributes;
                }
            ],
            'styles'=>[
                'title'=>'CSS',
                'attributes'=>function(&$model) {
                    return [
                        'styles'=>[
                            'type'=>'textArea',
                            'params'=>['htmlOptions'=>['style'=>'min-height:500px']]
                        ]
                    ];
                }
            ],  
            'js'=>[
                'title'=>'JavaScript',
                'attributes'=>function(&$model) {
                    return [
                        'js'=>[
                            'type'=>'textArea',
                            'params'=>['htmlOptions'=>['style'=>'min-height:500px']]
                        ]
                    ];
                }
            ],  
            'settings'=>[
                'title'=>'Дополнительные настройки',
                'attributes'=>function(&$model) {
                    if($model->isNewRecord) {
                        $model->access_crud_roles='admin';
                    }
                    $attributes=[
                        'is_send_mail'=>'checkbox',
                        'is_save_results'=>'checkbox',
                        'allow_add_results'=>'checkbox',
                        'access_crud_roles'=>[
                            'type'=>'text',
                            'params'=>[
                                'htmlOptions'=>['class'=>'form-control w100'],
                                'note'=>'Роли пользователей, через запятую, для которых разрешен доступ к управлению результатами формы'
                            ]
                        ],
                        'access_public_roles'=>[
                            'type'=>'text',
                            'params'=>[
                                'htmlOptions'=>['class'=>'form-control w100'],
                                'note'=>'Роли пользователей, через запятую, для которых разрешено отправлять форму из публичной части.'
                                    . '<br/>Если оставить поле пустым, будет разрешено отправлять форму всем пользователям'
                            ]
                        ],
                        'code.html.email'=>'<div class="page-header"><h1>Почтовые уведомления</h1></div>',
                        'email_to',
                        'email_subject'=>[
                            'type'=>'text', 
                            'params'=>['htmlOptions'=>['class'=>'form-control w100']]
                        ],
                        'email_view'=>[
                            'type'=>'text', 
                            'params'=>['htmlOptions'=>['class'=>'form-control w100']]
                        ],
                        /*
                        'code.html.model'=>'<div class="page-header"><h1>Модель формы</h1></div>',
                        'model_class'=>[
                            'type'=>'text', 
                            'params'=>['htmlOptions'=>['class'=>'form-control w100']]
                        ],
                        'model_behavior'=>[
                            'type'=>'text', 
                            'params'=>['htmlOptions'=>['class'=>'form-control w100']]
                        ],
                        */
                    ];
                    
                    return $attributes;
                }
            ]
        ]
    ]
];
