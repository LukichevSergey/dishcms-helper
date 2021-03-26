<?php
/**
 * CRUD: Социальные сети
 * 'socials'=>'application.config.crud.socials'
 */
use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HAjax;

$c=[
	'settingsKeyHeader'=>'socials_header_title',
	'settingsKeyFooter'=>'socials_footer_title'
];

if(in_array(A::get($_POST, 'action'), ['crud_socials_set_title_header', 'crud_socials_set_title_footer'])) {
	$ajax=HAjax::start();
	$key=(A::get($_POST, 'action') == 'crud_socials_set_title_header') ? 'settingsKeyHeader' : 'settingsKeyFooter';
	\Yii::app()->settings->set('cms_settings', $c[$key], A::get($_POST, 'title'));
	Y::cacheFlush();
	$ajax->success=true;
	$ajax->end();
}

return [
	'class'=>'\crud\models\ar\Social',
	'config'=>[
        'tablename'=>'crud_socials',
        'definitions'=>[
            'column.pk',
            'column.create_time'=>['label'=>'Дата создания'],
            'column.update_time',
            'column.published',
            'column.title',
			'column.sort',
			'icon_html'=>['type'=>'string', 'label'=>'Иконка (HTML)'],
			'icon_html_hover'=>['type'=>'string', 'label'=>'Иконка (HTML) (наведение, hover)'],
            ['name'=>'icon_svg', 'type'=>'column.file', 'label'=>'Иконка (SVG)', 'behaviorName'=>'iconSvgBehavior', 'types'=>'svg'],
            ['name'=>'icon_svg_hover', 'type'=>'column.file', 'label'=>'Иконка (SVG) (наведение, hover)', 'behaviorName'=>'iconSvgHoverBehavior', 'types'=>'svg'],
            ['name'=>'icon', 'type'=>'column.image', 'label'=>'Иконка', 'behaviorName'=>'iconBehavior'],
            ['name'=>'icon_hover', 'type'=>'column.image', 'label'=>'Иконка (наведение, hover)', 'behaviorName'=>'iconHoverBehavior'],
			'mobile_icon_html'=>['type'=>'string', 'label'=>'Иконка (мобильная версия) (HTML)'],
			'mobile_icon_html_hover'=>['type'=>'string', 'label'=>'Иконка (мобильная версия) (HTML) (наведение, hover)'],
            ['name'=>'mobile_icon_svg', 'type'=>'column.file', 'label'=>'Иконка (мобильная версия) (SVG)', 'behaviorName'=>'iconSvgMobileBehavior', 'types'=>'svg'],
            ['name'=>'mobile_icon_svg_hover', 'type'=>'column.file', 'label'=>'Иконка (мобильная версия) (SVG) (наведение, hover)', 'behaviorName'=>'iconSvgMobileHoverBehavior', 'types'=>'svg'],
            ['name'=>'mobile_icon', 'type'=>'column.image', 'label'=>'Иконка (мобильная версия)', 'behaviorName'=>'iconMobileBehavior'],
            ['name'=>'mobile_icon_hover', 'type'=>'column.image', 'label'=>'Иконка (мобильная версия) (наведение, hover)', 'behaviorName'=>'iconMobileHoverBehavior'],
            'url'=>['type'=>'string', 'label'=>'Ссылка']            
        ],
        'rules'=>[
            'safe',
            ['title', 'required'],
            ['title, icon_html, mobile_icon_html, icon_html_hover, mobile_icon_html_hover', 'length', 'max'=>255],
            ['url', 'safe'],
        ],
        'scopes'=>[
            'byDefaultOrder'=>[
	            'order'=>'`t`.`sort`, `t`.`title`, `t`.`id` DESC'
            ]
        ],
        'methods'=>[
			'public static function getHeaderTitle() {
				return \D::cms("'.$c['settingsKeyHeader'].'");
			}',
			'public static function getFooterTitle() {
				return \D::cms("'.$c['settingsKeyFooter'].'");
			}',
			'public static function hasItems() {
				static $count=null;
				if($count===null){$count=static::model()->published()->byDefaultOrder()->count();}
				return $count > 0;
			}',
            'public static function getItems() {
				$items=[];
				if($models=static::model()->published()->byDefaultOrder()->findAll()) {
					$fGetSrc=function($b) { return $b->exists() ? $b->getSrc() : null; };
					foreach($models as $model) {
						$items[]=(object)[
							"id"=>$model->id,
							"title"=>$model->title,
							"url"=>$model->url,
							"icon_html"=>trim($model->icon_html),
							"icon_html_hover"=>trim($model->icon_html_hover),
							"icon_svg"=>$fGetSrc($model->iconSvgBehavior),
							"icon_svg_hover"=>$fGetSrc($model->iconSvgHoverBehavior),
							"icon"=>$fGetSrc($model->iconBehavior),
							"icon_hover"=>$fGetSrc($model->iconHoverBehavior),
							"mobile_icon_html"=>trim($model->mobile_icon_html),
							"mobile_icon_html_hover"=>trim($model->mobile_icon_html_hover),
							"mobile_icon_svg"=>$fGetSrc($model->iconSvgMobileBehavior),
							"mobile_icon_svg_hover"=>$fGetSrc($model->iconSvgMobileHoverBehavior),
							"mobile_icon"=>$fGetSrc($model->iconMobileBehavior),
							"mobile_icon_hover"=>$fGetSrc($model->iconMobileHoverBehavior)
						];
					}
				}
				return $items;
			}',
			'public static function render($linkOptions=[]) {
				$items=static::getItems();
				foreach($items as $item) {
					if($item->url) {
						if($item->icon_html) {
							$html=$item->icon_html;
							if($item->icon_html_hover) { 
								$html.=\CHtml::tag("span", ["class"=>"social-hover", "style"=>"display:none"], $item->icon_html_hover); 
							}
							echo \CHtml::link($html, $item->url, A::m(["data-item"=>$item->id], $linkOptions));
						}
						elseif($item->icon_svg || $item->icon) {
							$src=$item->icon_svg ?: $item->icon;
							$hover=$item->icon_svg_hover ?: $item->icon_hover;
							$jsCssClass=\common\components\helpers\HHash::ujs();
							$jsCssImgClass=\common\components\helpers\HHash::ujs();
							$linkOptionsWithHover=$linkOptions;
							$linkOptionsWithHover["class"]=trim(A::get($linkOptionsWithHover, "class", "") . " " . $jsCssClass);
							\common\components\helpers\HYii::js(false, \'$(document).on("mouseenter mouseover hover", ".\'.$jsCssClass.\'", function(e){$(".\'.$jsCssImgClass.\'").attr("src", "\'.$hover.\'");});\', \CClientScript::POS_READY);
							\common\components\helpers\HYii::js(false, \'$(document).on("mouseleave mouseout blur", ".\'.$jsCssClass.\'", function(e){$(".\'.$jsCssImgClass.\'").attr("src", "\'.$src.\'");});\', \CClientScript::POS_READY);
							echo \CHtml::link(\CHtml::image($src, "", ["class"=>$jsCssImgClass]), $item->url, $linkOptionsWithHover);
						}
					}
				}
			}',
			'public static function renderMobile($linkOptions=[]) {
				$items=static::getItems();
				foreach($items as $item) {
					if($item->url) {
						if($item->mobile_icon_html) {
							$html=$item->mobile_icon_html;
							if($item->mobile_icon_html_hover) { 
								$html.=\CHtml::tag("span", ["class"=>"social-hover", "style"=>"display:none"], $item->mobile_icon_html_hover); 
							}
							echo \CHtml::link($html, $item->url, A::m(["data-item"=>$item->id], $linkOptions));
						}
						elseif($item->mobile_icon_svg || $item->mobile_icon) {
							$src=$item->mobile_icon_svg ?: $item->mobile_icon;
							$hover=$item->mobile_icon_svg_hover ?: $item->mobile_icon_hover;
							$jsCssClass=\common\components\helpers\HHash::ujs();
							$jsCssImgClass=\common\components\helpers\HHash::ujs();
							$linkOptionsWithHover=$linkOptions;
							$linkOptionsWithHover["class"]=trim(A::get($linkOptionsWithHover, "class", "") . " " . $jsCssClass);
							\common\components\helpers\HYii::js(false, \'$(document).on("mouseenter mouseover hover", ".\'.$jsCssClass.\'", function(e){$(".\'.$jsCssImgClass.\'").attr("src", "\'.$hover.\'");});\', \CClientScript::POS_READY);
							\common\components\helpers\HYii::js(false, \'$(document).on("mouseleave mouseout blur", ".\'.$jsCssClass.\'", function(e){$(".\'.$jsCssImgClass.\'").attr("src", "\'.$src.\'");});\', \CClientScript::POS_READY);
							echo \CHtml::link(\CHtml::image($src, "", ["class"=>$jsCssImgClass]), $item->url, $linkOptionsWithHover);
						}
					}
				}
			}'	
        ]
    ],
	'menu'=>[
		'backend'=>['label'=>'Социальные сети']	
	],
	'buttons'=>[
		'custom'=>function() use ($c) {
			return;
			$headerTitle=\D::cms($c['settingsKeyHeader']);
			$footerTitle=\D::cms($c['settingsKeyFooter']);
			$html=<<<EOL
			<div class="panel panel-default" style="margin-top:20px"><div class="panel-heading">Заголовок в шапке сайта</div><div class="panel-body"><div class="row">
			<div class="col-md-6"><input type="text" class="form-control w100 js-block-title-header" value="{$headerTitle}" /></div>
			<div class="col-md-2"><button class="btn btn-primary js-btn-set-block-title-header">Сохранить</button></div>
			<div class="col-md-4"><div class="alert alert-success js-block-title-alert-header" style="display:none;margin-top:4px;margin-bottom:0;font-size:13px;padding:3px 10px;text-align:center;">Заголовок успешно сохранен</div></div>
			</div></div></div>
			<div class="panel panel-default" style="margin-top:20px"><div class="panel-heading">Заголовок в подвале сайта</div><div class="panel-body"><div class="row">
			<div class="col-md-6"><input type="text" class="form-control w100 js-block-title-footer" value="{$footerTitle}" /></div>
			<div class="col-md-2"><button class="btn btn-primary js-btn-set-block-title-footer">Сохранить</button></div>
			<div class="col-md-4"><div class="alert alert-success js-block-title-alert-footer" style="display:none;margin-top:4px;margin-bottom:0;font-size:13px;padding:3px 10px;text-align:center;">Заголовок успешно сохранен</div></div>
			</div></div></div>
EOL;
			Y::js('crud_socials_header', '$(document).on("click", ".js-btn-set-block-title-header", function(e) {
				$.post(window.location.href, {action:"crud_socials_set_title_header", title:$(".js-block-title-header").val()}, function(r){
					if(r.success){$(".js-block-title-alert-header").show();setTimeout(function(){$(".js-block-title-alert-header").hide();},2000);}
				}, "json");
			});', \CClientScript::POS_READY);
			Y::js('crud_socials_footer', '$(document).on("click", ".js-btn-set-block-title-footer", function(e) {
				$.post(window.location.href, {action:"crud_socials_set_title_footer", title:$(".js-block-title-footer").val()}, function(r){
					if(r.success){$(".js-block-title-alert-footer").show();setTimeout(function(){$(".js-block-title-alert-footer").hide();},2000);}
				}, "json");
			});', \CClientScript::POS_READY);
			
			return $html;
		},
		'create'=>['label'=>'Добавить', 'htmlOptions'=>['style'=>'margin-bottom:0px']],
	],
	'crud'=>[
		'index'=>[
			'url'=>['/cp/crud/index'],
			'title'=>'Социальные сети',
			'gridView'=>[
				'dataProvider'=>[
					'sort'=>[
						'defaultOrder'=>'`t`.`sort`, `t`.`title`, `t`.`id` DESC'
					]						
				],
				'summaryText'=>'Соц. сети {start} &mdash; {end} из {count}',
				'emptyText'=>'Соц. сетей не найдено',
				'columns'=>[
                    'column.id',
                    [
                        'name'=>'icon',
                        'header'=>'Иконка',
						'type'=>'raw',
						'value'=>function($data) {
							if($data->icon_html) {
								return \CHtml::tag('span', ['class'=>'label label-info'], 'HTML');
							}
							elseif($data->iconSvgBehavior->exists()) {
								return \CHtml::image($data->iconSvgBehavior->getSrc(), '', ['width'=>30, 'height'=>30]);
							}
							elseif($data->iconBehavior->exists()) {
								return $data->iconBehavior->img(30, 30);
							}
							else {
								return 'нет';
							}
						},
                        'headerHtmlOptions'=>['style'=>'width:10%;text-align:center'],
                        'htmlOptions'=>['style'=>'text-align:center'],
                    ],
                    [
                        'name'=>'mobile_icon',
                        'header'=>'Иконка (Моб)',
						'type'=>'raw',
						'value'=>function($data) {
							if($data->mobile_icon_html) {
								return \CHtml::tag('span', ['class'=>'label label-info'], 'HTML');
							}
							elseif($data->iconSvgMobileBehavior->exists()) {
								return \CHtml::image($data->iconSvgMobileBehavior->getSrc(), '', ['width'=>30, 'height'=>30]);
							}
							elseif($data->iconMobileBehavior->exists()) {
								return $data->iconMobileBehavior->img(30, 30);
							}
							else {
								return 'нет';
							}
						},
                        'headerHtmlOptions'=>['style'=>'width:10%;white-space:nowrap;text-align:center'],
                        'htmlOptions'=>['style'=>'text-align:center'],
                    ],
                    [
                        'type'=>'column.title',
                        'header'=>'Наименование',
                        'info'=>[
                        	'Ссылка'=>'$data->url ? \CHtml::link($data->url, $data->url, ["target"=>"_blank"]) : ""',                        	
						],
						'headerHtmlOptions'=>['style'=>'width:40%;'],
                    ],
                    'common.ext.sort',
                    [
                        'name'=>'published',
                        'header'=>'Опубл.',
                        'headerHtmlOptions'=>['style'=>'width:5%;text-align:center;white-space:nowrap;'],
                        'type'=>'common.ext.published'
                    ],
                    'crud.buttons'
				]
			]
		],
		'create'=>[
			'url'=>['/cp/crud/create'],
			'title'=>'Новая соц.сеть',
		],
		'update'=>[
			'url'=>['/cp/crud/update'],
			'title'=>'Редактирование соц.сети',
		],
		'delete'=>[
			'url'=>['/cp/crud/delete'],
		],
		'form'=>[
			'htmlOptions'=>['enctype'=>'multipart/form-data'],
		],
		'tabs'=>[
			'desktop'=>[
				'title'=>'Основные',
				'attributes'=>function($model) {
					return [
						'published'=>'checkbox',
						'sort'=>[
							'type'=>'number',
							'params'=>['htmlOptions'=>['class'=>'form-control w10']]
						],
						'title'=>'text',					
						'url'=>'text',
						'icon_html'=>[
							'type'=>'text',
							'params'=>['htmlOptions'=>['class'=>'form-control w100']]
						],
						'icon_html_hover'=>[
							'type'=>'text',
							'params'=>['htmlOptions'=>['class'=>'form-control w100']]
						],
						'icon_svg'=>[
							'type'=>'common.ext.file.file',
							'behaviorName'=>'iconSvgBehavior',
							'params'=>[
								'actionDelete'=>'/common/crud/admin/default/removeFile?cid=socials&id='.$model->id.'&b=iconSvgBehavior',
								'tmbWidth'=>30,
								'tmbHeight'=>30,
							]
						],
						'icon_svg_hover'=>[
							'type'=>'common.ext.file.file',
							'behaviorName'=>'iconSvgHoverBehavior',
							'params'=>[
								'actionDelete'=>'/common/crud/admin/default/removeFile?cid=socials&id='.$model->id.'&b=iconSvgHoverBehavior',
								'tmbWidth'=>30,
								'tmbHeight'=>30,
							]
						],
						'icon'=>[
							'type'=>'common.ext.file.image',
							'behaviorName'=>'iconBehavior',
							'params'=>[
								'actionDelete'=>'/common/crud/admin/default/removeImage?cid=socials&id='.$model->id.'&b=iconBehavior',
								'tmbWidth'=>30,
								'tmbHeight'=>30,
							]
						],
						'icon_hover'=>[
							'type'=>'common.ext.file.image',
							'behaviorName'=>'iconHoverBehavior',
							'params'=>[
								'actionDelete'=>'/common/crud/admin/default/removeImage?cid=socials&id='.$model->id.'&b=iconHoverBehavior',
								'tmbWidth'=>30,
								'tmbHeight'=>30,
							]
						]
					];
				}
			],
			'mobile'=>[
				'title'=>'Мобильная версия',
				'attributes'=>function($model) {
					return [
						'published'=>'checkbox',
						'sort'=>[
							'type'=>'number',
							'params'=>['htmlOptions'=>['class'=>'form-control w10']]
						],
						'title'=>'text',					
						'url'=>'text',
						'mobile_icon_html'=>[
							'type'=>'text',
							'params'=>['htmlOptions'=>['class'=>'form-control w100']]
						],
						'mobile_icon_html_hover'=>[
							'type'=>'text',
							'params'=>['htmlOptions'=>['class'=>'form-control w100']]
						],
						'mobile_icon_svg'=>[
							'type'=>'common.ext.file.file',
							'behaviorName'=>'iconSvgMobileBehavior',
							'params'=>[
								'actionDelete'=>'/common/crud/admin/default/removeFile?cid=socials&id='.$model->id.'&b=iconSvgMobileBehavior',
								'tmbWidth'=>30,
								'tmbHeight'=>30,
							]
						],
						'mobile_icon_svg_hover'=>[
							'type'=>'common.ext.file.file',
							'behaviorName'=>'iconSvgMobileHoverBehavior',
							'params'=>[
								'actionDelete'=>'/common/crud/admin/default/removeFile?cid=socials&id='.$model->id.'&b=iconSvgMobileHoverBehavior',
								'tmbWidth'=>30,
								'tmbHeight'=>30,
							]
						],
						'mobile_icon'=>[
							'type'=>'common.ext.file.image',
							'behaviorName'=>'iconMobileBehavior',
							'params'=>[
								'actionDelete'=>'/common/crud/admin/default/removeImage?cid=socials&id='.$model->id.'&b=iconMobileBehavior',
								'tmbWidth'=>30,
								'tmbHeight'=>30,
							]
						],
						'mobile_icon_hover'=>[
							'type'=>'common.ext.file.image',
							'behaviorName'=>'iconMobileHoverBehavior',
							'params'=>[
								'actionDelete'=>'/common/crud/admin/default/removeImage?cid=socials&id='.$model->id.'&b=iconMobileHoverBehavior',
								'tmbWidth'=>30,
								'tmbHeight'=>30,
							]
						]
					];
				}
			]
		]
	],
];