<?php
/**
 * CRUD: Блоки на главной
 * 'main_blocks'=>'application.config.crud.main_blocks'
 */
use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;

$c=[
	'class'=>'\crud\models\ar\MainBlock',
	'tablename'=>'crud_main_blocks',	
	'menuLabel'=>'Блоки на главной',
	'title'=>'Блоки на главной',
	'createTitle'=>'Новый блок',
	'updateTitle'=>'Редактирование блока'
];

return [
	'class'=>$c['class'],
	'config'=>[
        'tablename'=>$c['tablename'],
        'definitions'=>[
            'column.pk',
            'column.create_time'=>['label'=>'Дата создания'],
            'column.update_time',
            'column.published',
            'column.title'=>['label'=>'Заголовок'],
            'column.sort',
			['name'=>'image_1', 'type'=>'column.image', 'label'=>'Изображение #1', 'behaviorName'=>'image1Behavior', 'types'=>'jpg, jpeg, png'],
			['name'=>'image_2', 'type'=>'column.image', 'label'=>'Изображение #2', 'behaviorName'=>'image2Behavior', 'types'=>'jpg, jpeg, png'],
			'show_question_form'=>['type'=>'boolean', 'label'=>'Показать форму "Задать вопрос"'],
			'show_partner_slider'=>['type'=>'boolean', 'label'=>'Показать слайдер "Партнеры"'],
			'partner_slider_title'=>['type'=>'string', 'label'=>'Заголовок слайдера "Партнеры"'],
			'show_license_slider'=>['type'=>'boolean', 'label'=>'Показать слайдер "Лицензии"'],
			'license_slider_title'=>['type'=>'string', 'label'=>'Заголовок слайдера "Лицензии"'],
			'text'=>['type'=>'LONGTEXT', 'label'=>'Основной текст блока'],
		],
		'rules'=>[
			'safe',
			['show_question_form, show_partner_slider, show_license_slider', 'boolean'],
			['partner_slider_title, license_slider_title, text', 'safe']
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
		'backend'=>['label'=>$c['menuLabel']]	
	],
	'buttons'=>[
		'create'=>['label'=>'Добавить', 'htmlOptions'=>['style'=>'margin-bottom:0px']],
	],
	'crud'=>[
		'index'=>[
			'url'=>['/cp/crud/index'],
			'title'=>$c['title'],
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
                        'name'=>'image_1',
                        'header'=>'',
						'type'=>['common.ext.file.image'=>['behaviorName'=>'image1Behavior']],
						'headerHtmlOptions'=>['style'=>'width:5%;text-align:center'],
                        'htmlOptions'=>['style'=>'text-align:center'],
					],
					[
                        'name'=>'image_2',
                        'header'=>'',
						'type'=>['common.ext.file.image'=>['behaviorName'=>'image2Behavior']],
						'headerHtmlOptions'=>['style'=>'width:5%;text-align:center'],
                        'htmlOptions'=>['style'=>'text-align:center'],
					],
					[
						'type'=>'column.title',
						'info'=>[
							'Отображать слайдер \"Лицензии\"'=>'((($is=$data->show_license_slider)?"":"") . \CHtml::tag("span", ["class"=>"label label-" . ($is?"success":"danger")], $is?"да":"нет"))',
							'Заголовок слайдера \"Лицензии\"'=>'($data->show_license_slider ? $data->license_slider_title : "")',
							'Отображать слайдер \"Партнеры\"'=>'((($is=$data->show_partner_slider)?"":"") . \CHtml::tag("span", ["class"=>"label label-" . ($is?"success":"danger")], $is?"да":"нет"))',
							'Заголовок слайдера \"Партнеры\"'=>'($data->show_partner_slider ? $data->partner_slider_title : "")',
							'Отображать форму \"Задать вопрос\"'=>'((($is=$data->show_question_form)?"":"") . \CHtml::tag("span", ["class"=>"label label-" . ($is?"success":"danger")], $is?"да":"нет"))',							
						]
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
			'title'=>$c['createTitle'],
		],
		'update'=>[
			'url'=>['/cp/crud/update'],
			'title'=>$c['updateTitle'],
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
					'title'=>'textArea',
					'text'=>'tinyMce',
					'show_license_slider'=>'checkbox',
					'license_slider_title',
					'show_partner_slider'=>'checkbox',
					'partner_slider_title',
					'show_question_form'=>'checkbox',
					'image_1'=>[
						'type'=>'common.ext.file.image',
						'behaviorName'=>'image1Behavior',
						'params'=>[
							'tmbWidth'=>-1,
							'tmbHeight'=>-1
						]
					],
					'image_2'=>[
						'type'=>'common.ext.file.image',
						'behaviorName'=>'image2Behavior',
						'params'=>[
							'tmbWidth'=>-1,
							'tmbHeight'=>-1
						]
					],
				];
			}
		],
	],
];