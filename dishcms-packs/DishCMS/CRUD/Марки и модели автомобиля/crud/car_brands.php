<?php
/**
 * Файл настроек модели Марки автотранспорта
 * 
 */

return [
    'class'=>'\crud\models\ar\CarBrand',
    'config'=>[
        'tablename'=>'car_brands',
        'definitions'=>[
            'column.pk',
            'column.title',
            'column.published',
        ],
        'rules'=>[
            'safe',
            ['title', 'required'],
        ],
        'methods'=>[
            function() {
                $code='public static function formFields($form, $model, $attributeBrandId, $attributeModelId) {';
                
                $code.='\common\components\helpers\HYii::controller()->widget("\common\widgets\\\\form\DropDownListField", A::m(compact("form", "model"), [
	               "attribute"=>$attributeBrandId,
	               "data"=>static::model()->listData("title",["order"=>"`title` ASC"]),
	               "htmlOptions"=>["class"=>"form-control w50 js-car-brand", "empty"=>"Не указана"]
                ]));';

                $code.='\common\components\helpers\HYii::controller()->widget("\common\widgets\\\\form\DropDownListField", A::m(compact("form", "model"), [
	               "attribute"=>$attributeModelId,
	               "data"=>[],
                       "tagOptions"=>["style"=>"display:none"],
	               "htmlOptions"=>["class"=>"form-control w50 js-car-model", "empty"=>"Не указана"]
                ]));';
                
                $code.='$carModelsData=[];$cm=new \crud\models\ar\CarModel;if($carModels=$cm->findAll(["order"=>"`title` ASC"])){foreach($carModels as $carModel){$carModelsData[]=(object)["model"=>(int)$carModel->id, "brand"=>$carModel->brand_id, "title"=>$carModel->title];}}';
                $code.='\common\components\helpers\HYii::js(false,"window.productCarModelsData=".json_encode($carModelsData).";",\CClientScript::POS_READY);';
                $code.='\common\components\helpers\HYii::js(false,"window.productCarModelId=".(int)$model->$attributeModelId.";",\CClientScript::POS_READY);';
                
                $jsCode='(function(){';
                $jsCode.=';$(document).on("change",".js-car-brand", function(e){let brand=$(e.target).val(),hide=true,first="",found=false;'
                    . 'if(brand){$(".js-car-model option").each(function(){let v=$(this).attr("value");if($(this).attr("data-brand") != brand){'
                    . '$(this).hide();}else{if(first==""){first=v;}if(v==window.productCarModelId){found=true;$(".js-car-model").val(v);}hide=false;$(this).show();}});'
                    . '}if(!found){$(".js-car-model").val(first);}if(hide){$(".js-car-model").parent().hide();}else{$(".js-car-model").parent().show();}'
                    . '});let modelOptions="",data=window.productCarModelsData;'
                    . 'for(let idx in data){modelOptions+="<option value=\""+data[idx].model+"\" data-brand=\""+data[idx].brand+"\">"+data[idx].title+"</option>";};'
                    . '$(".js-car-model").html(modelOptions);$(".js-car-brand").trigger("change");';
                $jsCode.='})();';
                
                $code.='\common\components\helpers\HYii::js(false,\''.$jsCode.'\',\CClientScript::POS_READY);';
                
                $code.='}';
                
                return $code;
            }            
        ]
    ],
	'menu'=>[
		'backend'=>['label'=>'Марки автотранспорта']
	],
	'buttons'=>[
		'create'=>['label'=>'Добавить марку']
	],	
	'crud'=>[		
		'index'=>[
			'url'=>'/cp/crud/index',
			'title'=>'Марки автотранспорта',
			'gridView'=>[
			    'dataProvider'=>[
			        'sort'=>['defaultOrder'=>'`title` ASC']			        
			    ],
				'columns'=>[
					'id'=>[
						'name'=>'id',
						'header'=>'#',
						'headerHtmlOptions'=>['style'=>'width:5%;text-align:center'],
					],
					'title'=>[
						'name'=>'title',
						'header'=>'Наименование',
						'type'=>'raw',
						'value'=>'"<strong>".CHtml::link($data->title,["/cp/crud/index", "cid"=>"car_models", "brand"=>$data->id])."</strong>"'
					],
				    [
				        'name'=>'published',
				        'header'=>'Опубликовать',
				        'headerHtmlOptions'=>['style'=>'text-align:center;width:15%'],
				        'type'=>'common.ext.published'
				    ],
					'crud.buttons'=>[
						'type'=>'crud.buttons',
						'params'=>[
							'template'=>'{models}&nbsp;&nbsp;{update}{delete}',
							'buttons'=>[
								'models' => [
									'label'=>'<span class="glyphicon glyphicon-list-alt"></span>',
									'url'=>'\Yii::app()->createUrl("/cp/crud/index", ["cid"=>"car_models", "brand"=>$data->id])',
									'options'=>['title'=>'Модели бренда'],
								],
							],
							'headerHtmlOptions'=>['style'=>'width:10%']
						]
					]					
				]
			]
		],
		'create'=>[
			'url'=>'/cp/crud/create',
			'title'=>'Добавить марку'
		],
		'update'=>[
			'url'=>'/cp/crud/update',
			'title'=>'Редактирование марки'
		],
		'delete'=>[
			'url'=>'/cp/crud/delete'
		],
		'form'=>[
			'attributes'=>[
				'published'=>'checkbox',
				'title',
			],
		]
	]
];
