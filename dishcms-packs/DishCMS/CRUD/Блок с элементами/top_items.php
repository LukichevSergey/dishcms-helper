<?php
/**
 * CRUD: Блок с элементами
 * 'top_items'=>'application.config.crud.top_items'
 */
use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HAjax;

$c=[
	'class'=>'\crud\models\ar\TopItem',
	'tablename'=>'crud_top_items',
	'settingsKey'=>'top_items_title',
	'defaultHeading'=>'Иконки на главной'
];

if(A::get($_POST, 'action') == 'crud_top_items_set_title') {
	$ajax=HAjax::start();
	\Yii::app()->settings->set('cms_settings', $c['settingsKey'], A::get($_POST, 'title'));
	Y::cacheFlush();
	$ajax->success=true;
	$ajax->end();
}

return [
	'class'=>$c['class'],
	'config'=>[
        'tablename'=>$c['tablename'],
        'definitions'=>[
            'column.pk',
            'column.create_time'=>['label'=>'Дата создания'],
            'column.update_time',
            'column.published',
            'column.title'=>['label'=>'Подпись'],
            'column.sort',
			'column.image',
			['name'=>'svg', 'type'=>'column.file', 'label'=>'Изображение (SVG)', 'behaviorName'=>'svgBehavior', 'types'=>'svg'],
		],
		'scopes'=>[
			'byDefaultOrder'=>['order'=>'`sort`, `id` DESC']
		],
		'methods'=>[
			'public static function getItems($criteria=[]) {
				return static::model()->published()->byDefaultOrder()->findAll($criteria);
			}',
			'public function getImageSrc() {
				if($this->svgBehavior->exists()) { return $this->svgBehavior->getSrc(); }
				elseif($this->imageBehavior->exists()) { return $this->imageBehavior->getSrc(); }
				return null;
			}'
		]
    ],
	'menu'=>[
		'backend'=>['label'=>'Блок "' . \D::cms($c['settingsKey'], $c['defaultHeading']) . '"']	
	],
	'buttons'=>[
		'custom'=>function() use ($c) {
			$title=\D::cms($c['settingsKey']);
			$html=<<<EOL
			<div class="panel panel-default" style="margin-top:20px"><div class="panel-heading">Заголовок блока</div><div class="panel-body"><div class="row">
			<div class="col-md-6"><input type="text" class="form-control w100 js-block-title" value="{$title}" /></div>
			<div class="col-md-2"><button class="btn btn-primary js-btn-set-block-title">Сохранить</button></div>
			<div class="col-md-4"><div class="alert alert-success js-block-title-alert" style="display:none;margin-top:4px;margin-bottom:0;font-size:13px;padding:3px 10px;text-align:center;">Заголовок успешно сохранен</div></div>
			</div></div></div>			
EOL;
			Y::js('crud_block_items', '$(document).on("click", ".js-btn-set-block-title", function(e) {
				$.post(window.location.href, {action:"crud_top_items_set_title", title:$(".js-block-title").val()}, function(r){
					if(r.success){$(".js-block-title-alert").show();setTimeout(function(){$(".js-block-title-alert").hide();},2000);}
				}, "json");
			});', \CClientScript::POS_READY);
			
			return $html;
		},
		'create'=>['label'=>'Добавить', 'htmlOptions'=>['style'=>'margin-bottom:0px']],
	],
	'crud'=>[
		'index'=>[
			'url'=>['/cp/crud/index'],
			'title'=>'Блок "' . \D::cms($c['settingsKey'], $c['defaultHeading']) . '"',
			'gridView'=>[
				'dataProvider'=>[
					'sort'=>['defaultOrder'=>'`sort`, `id` DESC']
				],
				'columns'=>[
					[
						'name'=>'id',
						'header'=>'#',
						'headerHtmlOptions'=>['style'=>'width:5%']
					],
					[
                        'name'=>'icon',
                        'header'=>'Иконка',
						'type'=>'raw',
						'value'=>function($data) {
							if($data->svgBehavior->exists()) {
								return \CHtml::image($data->svgBehavior->getSrc(), '', ['width'=>120, 'height'=>120]);
							}
							elseif($data->imageBehavior->exists()) {
								return $data->imageBehavior->img(120, 120);
							}
							else {
								return 'нет';
							}
						},
                        'headerHtmlOptions'=>['style'=>'width:15%;text-align:center'],
                        'htmlOptions'=>['style'=>'text-align:center'],
                    ],
					[
						'name'=>'title',
						'header'=>'Подпись',
						'type'=>'raw'
					],
					'common.ext.sort',
                    [
                        'name'=>'published',
                        'header'=>'Опубл.',
                        'headerHtmlOptions'=>['style'=>'width:5%;text-align:center;white-space:nowrap;'],
                        'type'=>'common.ext.published'
                    ],
					'crud.buttons',
				]
			]
		],
		'create'=>[
			'url'=>['/cp/crud/create'],
			'title'=>'Новый элемент',
		],
		'update'=>[
			'url'=>['/cp/crud/update'],
			'title'=>'Редактирование элемента',
		],
		'delete'=>[
			'url'=>['/cp/crud/delete'],
		],
		'form'=>[
			'htmlOptions'=>['enctype'=>'multipart/form-data'],
			'attributes'=>function($model) {
				return [
					'published'=>'checkbox',
					'sort'=>[
						'type'=>'number',
						'params'=>['htmlOptions'=>['class'=>'form-control w10 inline']]
					],
					'svg'=>[
						'type'=>'common.ext.file.file',
						'behaviorName'=>'svgBehavior',
						'params'=>[
							'actionDelete'=>'/common/crud/admin/default/removeFile?cid=top_items&id='.$model->id.'&b=svgBehavior',
							'tmbWidth'=>120,
							'tmbHeight'=>120,
						]
					],
					'image'=>[
						'type'=>'common.ext.file.image',
						'params'=>[
							'tmbWidth'=>-1,
							'tmbHeight'=>-1
						]
					],
					'title'=>'tinyMceLite',
				];
			}
		],
	],
];