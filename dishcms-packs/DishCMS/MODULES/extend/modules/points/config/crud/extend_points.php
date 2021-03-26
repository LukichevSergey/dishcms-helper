<?php
/**
 * Точки продаж
 *
 */
use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HDb;
use common\components\helpers\HHash;
use extend\modules\points\components\helpers\HPoint;

$cid="extend_points";
$t=[
    'attribute.sort'=>'Сортировка',
    'attribute.phone'=>'Телефон',
    'attribute.address'=>'Адрес',
    'attribute.contacts'=>'Контакты',
    'attribute.worktime'=>'Время работы',
    'attribute.parking'=>'Парковки',
    'attribute.info'=>'Подробная информация',
    'attribute.lat'=>'Широта',    
    'attribute.lon'=>'Долгота',
    'menu.backend.label'=>'Магазины',
    'buttons.create.label'=>'Добавить',
    
    'crud.index.title'=>'Магазины',
    'crud.index.gridView.columns.title.header'=>'Магазин',
    'crud.index.gridView.columns.title.info.phone'=>'Телефон',
    'crud.index.gridView.columns.title.info.contacts'=>'Контакты',
    'crud.index.gridView.columns.title.info.address'=>'Адрес',
    'crud.index.gridView.columns.title.info.worktime'=>'Время работы',
    'crud.index.gridView.columns.title.info.parking'=>'Парковки',
    'crud.index.gridView.columns.title.info.info'=>'Подробная информация',
    'crud.index.gridView.columns.title.info.map'=>'Местоположение',
    'tabs.main.title'=>'Основные',
    'tabs.detail.title'=>'Контакты',
    'tabs.photos.title'=>'Фотографии',
    'tabs.map.title'=>'Карта',
    'crud.create.title'=>'Добавление магазина',
    'crud.update.title'=>'Редактирование магазина',
];

return [
    'class'=>'\crud\models\ar\extend\points\models\Point',
    'access'=>[
        ['allow', 'users'=>['@'], 'roles'=>['admin', 'sadmin', 'crud_sale_pages_manager']],
        ['deny', 'users'=>['*']],
    ],
    'config'=>[
        'tablename'=>'extend_points',
        'definitions'=>[
            'column.pk',
            'column.create_time',
            'column.update_time',
            'column.published',
            'column.title',
            'phone'=>['type'=>'VARCHAR(255)', 'label'=>$t['attribute.phone']],
            'address'=>['type'=>'TEXT', 'label'=>$t['attribute.address']],
            'contacts'=>['type'=>'TEXT', 'label'=>$t['attribute.contacts']],            
            'worktime'=>['type'=>'VARCHAR(255)', 'label'=>$t['attribute.worktime']],
            'parking'=>['type'=>'VARCHAR(255)', 'label'=>$t['attribute.parking']],
            'info'=>['type'=>'LONGTEXT', 'label'=>$t['attribute.info']],
            'lon'=>['type'=>'DECIMAL(11,7)', 'label'=>$t['attribute.lon']],
            'lat'=>['type'=>'DECIMAL(10,7)', 'label'=>$t['attribute.lat']],
            'sort'=>['type'=>'INT(11), KEY(`sort`)', 'label'=>$t['attribute.sort']],
            'photohash'=>['type'=>'VARCHAR(255)']
        ],
        'behaviors'=>[
            'pointBehavior'=>'\extend\modules\points\behaviors\PointModelBehavior',            
        ],
        'consts'=>[
            'ROLE_MANAGER'=>'crud_extend_points_manager'
        ],
    ],
    'settings'=>[
        // @todo необходимо добавить в общие настройки приложения /config/settings.php
        'points'=>[
            'class'=>'\accounts\models\AccountSettings',
            'breadcrumbs'=>['Магазины'=>'/cp/crud/index?cid=extend_points'],
            'title'=>'Настройки',
            'viewForm'=>'extend.modules.points.modules.admin.views.settings._point_settings'
        ]
    ],
    'menu'=>[
        'backend'=>['label'=>$t['menu.backend.label']]
    ],
    'buttons'=>[
        'create'=>['label'=>$t['buttons.create.label']],
        'settings'=>['label'=>'Настройки']
    ],    
    'crud'=>[
        'index'=>[
            'url'=>'/cp/crud/index',
            'title'=>$t['crud.index.title'],
            'gridView'=>[
                'id'=>'pointsGridViewId',
                'dataProvider'=>[
                    'sort'=>['defaultOrder'=>'`t`.`sort` DESC, `t`.`create_time` DESC, `t`.`id` DESC'],
                ],
                'columns'=>[
                    'column.id',
                    [
                        'type'=>'column.title',
                        'header'=>$t['crud.index.gridView.columns.title.header'],
                        'headerHtmlOptions'=>['style'=>'width:70%;'],
                        'info'=>[
                            // $t['crud.index.gridView.columns.title.info.phone']=>'\common\components\helpers\HTools::formatPhone($data->phone)',
                            // $t['crud.index.gridView.columns.title.info.contacts']=>'$data->contacts',
                            $t['crud.index.gridView.columns.title.info.address']=>'$data->address',
                            // $t['crud.index.gridView.columns.title.info.worktime']=>'$data->worktime',
                            // $t['crud.index.gridView.columns.title.info.parking']=>'$data->parking',
                            // $t['crud.index.gridView.columns.title.info.info']=>'$data->info',
                            $t['crud.index.gridView.columns.title.info.map']=>'$data->lat . ", " . $data->lon'
                            . ' . \common\components\helpers\HYii::controller()->widget(\'\common\ext\ymap\widgets\YMap\', ['
                            . ' "apikey"=>\extend\modules\points\components\helpers\HPoint::settings()->apikey,"options"=>'
                            . ' ["x"=>$data->lat, "y"=>$data->lon, "controls"=>["zoomControl"], "placemarkOptions"=>\extend\modules\points\components\helpers\HPoint::getPlacemarkOptions(),'
                            . ' "onAfterInit"=>"js:function(map) { map.behaviors.disable([\"scrollZoom\"]); }"],'
                            . ' "htmlOptions"=>["style"=>"width:100%;height:250px"]], true)'
                        ]
                    ],
                    [
                        'name'=>'sort',
                        'header'=>'Сорт.',
                        'headerHtmlOptions'=>['style'=>'width:5%;text-align:center;white-space:nowrap;'],
                        'htmlOptions'=>['style'=>'text-align:center'],
                    ],
                    [
                        'name'=>'update_time',
                        'header'=>'Обновлено',
                        'headerHtmlOptions'=>['style'=>'width:10%;text-align:center'],
                        'htmlOptions'=>['style'=>'text-align:center'],
                    ],
                    [
                        'name'=>'published',
                        'header'=>'Опубл.',
                        'headerHtmlOptions'=>['style'=>'width:5%;text-align:center;white-space:nowrap;'],
                        'type'=>'common.ext.published'
                    ],
                    'crud.buttons'=>[
                        'type'=>'crud.buttons',
                        'params'=>[
                            'template'=>'{update}{delete}',
                            'buttons'=>[
                                'update'=>[
                                    'label'=>'<span class="glyphicon glyphicon-pencil"></span> Редактировать',
                                    'options'=>['class'=>'btn btn-xs btn-primary w100', 'style'=>'margin-top:2px']
                                ],
                                'delete'=>[
                                    'label'=>'<span class="glyphicon glyphicon-remove"></span> Удалить',
                                    'options'=>['class'=>'btn btn-xs btn-danger w100', 'style'=>'margin-top:2px']
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ],
        'create'=>[
            'scenario'=>'insert',
            'url'=>'/cp/crud/create',
            'title'=>$t['crud.create.title'],
        ],
        'update'=>[
            'url'=>['/cp/crud/update'],
            'title'=>$t['crud.update.title'],
        ],
        'delete'=>[
            'url'=>['/cp/crud/delete'],
        ],
        'form'=>[
            'htmlOptions'=>['enctype'=>'multipart/form-data'],
        ],
        'tabs'=>[
            'main'=>[
                'title'=>$t['tabs.main.title'],
                'attributes'=>[
                    'published'=>'checkbox',
                    'sort'=>[
                        'type'=>'number',
                        'params'=>['htmlOptions'=>['class'=>'form-control w10']]
                    ],
                    'title',      
                    'address'=>[
                        'type'=>'textArea',
                        'params'=>['htmlOptions'=>['class'=>'form-control w100']]
                    ],
                    // 'phone'=>'phone',
                    // 'contacts'=>[
                    //    'type'=>'textArea',
                    //    'params'=>['htmlOptions'=>['class'=>'form-control w50']]
                    // ],
                    // 'worktime',
                    // 'parking',
                    
                ]
            ],
            'detail'=>[
                'title'=>$t['tabs.detail.title'],
                'attributes'=>[                    
                    'info'=>['type'=>'tinyMce', 'params'=>['full'=>true]]
                ]
            ],
            'photos'=>[
                'title'=>$t['tabs.photos.title'],
                'attributes'=>function(&$model) {
                    if(!$model->photohash) {
                        $model->photohash=HHash::u();
                    }
                    $form=new \CActiveForm;
                    return [
                        'photohash'=>'hidden',
                        'code.html.upload'=>Y::controller()->widget('\ext\uploader\widgets\UploadField', [
                            'form'=>$form,
                            'model'=>$model,
                            'attribute'=>'photohash',
                            'uploadUrl'=>'/extend/points/admin/crud/uploadFile',
                            'deleteUrl'=>'/extend/points/admin/crud/deleteFile',
                        ], true),
                        'code.html.list'=>Y::controller()->widget('\ext\uploader\widgets\FileList', [
                            'hash'=>$model->photohash,
                            'path'=>'webroot.images.uploader.extend_points',
                            'deleteUrl'=>'/extend/points/admin/crud/deleteFile'
                        ], true)
                    ];
                }
            ],
            'map'=>[
                'title'=>$t['tabs.map.title'],
                'attributes'=>function(&$model) {
                    return [
                        'lat'=>[
                            'type'=>'text',
                            'params'=>['htmlOptions'=>['class'=>'form-control w25', 'readonly'=>true]]
                        ],
                        'lon'=>[
                            'type'=>'text',
                            'params'=>['htmlOptions'=>['class'=>'form-control w25', 'readonly'=>true]]
                        ],
                        'code.html.map'=>function($model) {
                            return Y::controller()->widget('\common\ext\ymap\widgets\YMap', [
                                'apikey'=>HPoint::settings()->apikey,
                                'options'=>[
                                    'x'=>$model->lat ?: 55.028888,
                                    'y'=>$model->lon ?: 82.926484,
                                    'controls'=>['zoomControl', 'searchControl', 'geolocationControl', 'fullscreenControl'],
                                    'placemarkOptions'=>HPoint::getPlacemarkOptions(),
                                    'onAfterInit'=>'js:function(map) {
                                        var $lat=$("#crud_models_ar_extend_points_models_Point_lat");
                                        var $lon=$("#crud_models_ar_extend_points_models_Point_lon");
                                        var geo=map.geoObjects.get(0);
                                        map.behaviors.disable(["dblClickZoom"]);
                                        geo.options.set({draggable: true});
                                        geo.events.add("dragend",function(e){var ll=e.originalEvent.target.geometry.getCoordinates();$lat.val(ll[0]);$lon.val(ll[1]);});
                                        map.events.add("dblclick",function(e){$lat.val(e.get("coords")[0]);$lon.val(e.get("coords")[1]);e.originalEvent.map.geoObjects.get(0).geometry.setCoordinates(e.get("coords"));});
                                        if(!$lat.val())$lat.val(geo.geometry.getCoordinates()[0]);
                                        if(!$lon.val())$lon.val(geo.geometry.getCoordinates()[1]);
                                    }'
                            ], 'htmlOptions'=>['style'=>'width:100%;height:400px']], true);
                        }
                    ];
                }
            ]
        ]
    ]
];