<?php
/**
 * CRUD: Блок с элементами
 * 'block_items'=>'application.config.crud.block_items'
 */
use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HAjax;

$c=[
	'class'=>'\crud\models\ar\BlockItem',
	'tablename'=>'crud_block_items',
	'settingsKey'=>'why_block_items_title',
	'defaultHeading'=>'Заголовок блока'
];

if(A::get($_POST, 'action') == 'crud_block_items_set_title') {
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
			'column.image'
		],
		'scopes'=>[
			'byDefaultOrder'=>['order'=>'`sort`, `id` DESC']
		],
		'methods'=>[
			'public static function getItems($criteria=[]) {
				return static::model()->published()->byDefaultOrder()->findAll($criteria);
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
				$.post(window.location.href, {action:"crud_block_items_set_title", title:$(".js-block-title").val()}, function(r){
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
						'name'=>'image',
						'type'=>[
							'common.ext.file.image'=>[
								'behaviorName'=>'imageBehavior',
								'width'=>-1,
								'height'=>-1,
								'proportional'=>true, 
								'htmlOptions'=>[],
								'default'=>true
						]],
						'headerHtmlOptions'=>['style'=>'width:15%']
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
			'attributes'=>[
				'published'=>'checkbox',
				'sort'=>[
					'type'=>'number',
					'params'=>['htmlOptions'=>['class'=>'form-control w10 inline']]
				],
				'image'=>[
					'type'=>'common.ext.file.image',
					'params'=>[
						'tmbWidth'=>-1,
						'tmbHeight'=>-1
					]
				],
				'title'=>'tinyMceLite',
			]
		],
	],
];