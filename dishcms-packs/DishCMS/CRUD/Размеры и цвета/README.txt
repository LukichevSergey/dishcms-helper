1) Добавить в /protected/config/crud.php
return [
	...
    'product_colors'=>'application.config.crud.product_colors',
    'product_sizes'=>'application.config.crud.product_sizes',
    'product_tastes'=>'application.config.crud.product_tastes'
];

2) Добавить пункты меню в раздел администрирования /protected/modules/admin/config/menu.php
	'catalog'=>[
		...
		['label'=>'', 'itemOptions'=>['class'=>'divider'], 'visible'=>'divider'],
	    HCrud::getMenuItems(Y::controller(), 'product_colors', 'crud/index', true),
	    HCrud::getMenuItems(Y::controller(), 'product_sizes', 'crud/index', true),
	    HCrud::getMenuItems(Y::controller(), 'product_tastes', 'crud/index', true),
		...


3) Добавить атрибуты и поведение в модель Product
	public $size;
	public $color;
	...
	public function behaviors()
	{
		return A::m(parent::behaviors(), [
			'colorsBehavior'=>[
                'class'=>'\common\behaviors\ARAttributeListBehavior',
                'attribute'=>'colors',
				'attributeLabel'=>'Цвета',
                'rel'=>'\crud\models\ar\ProductColor'
            ],
            'sizesBehavior'=>[
                'class'=>'\common\behaviors\ARAttributeListBehavior',
                'attribute'=>'sizes',
				'attributeLabel'=>'Размеры',
                'rel'=>'\crud\models\ar\ProductSize'
            ],
		]);
	}
	...
	public function attributeLables()
	{
		...
		'size'=>'Размер',
		'color'=>'Цвет'
		...
	}

4) Добавить в форму редактирования товара /admin/views/shop/_form_product.php
<div class="panel panel-default">
    <div class="panel-heading">
        <?= $form->labelEx($model, $model->colorsBehavior->attribute); ?>
    </div>
    <div class="panel-body">
    <? $this->widget('\common\widgets\form\CheckboxListField', A::m(compact('form', 'model'), [
        'attribute'=>$model->colorsBehavior->attribute,
        'data'=>\crud\models\ar\ProductColor::model()->listData('title'),
        'hideLabel'=>true,
        'htmlOptions'=>[
            'container'=>'div', 
            'labelOptions'=>['class'=>'inline', 'style'=>'font-weight:normal'],
            'separator'=>'&nbsp;&nbsp;&nbsp;'
        ]
    ])) ?>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <?= $form->labelEx($model, $model->sizesBehavior->attribute); ?>
    </div>
    <div class="panel-body">
    <? $this->widget('\common\widgets\form\CheckboxListField', A::m(compact('form', 'model'), [
        'attribute'=>$model->sizesBehavior->attribute,
        'data'=>\crud\models\ar\ProductSize::model()->listData('title'),
        'hideLabel'=>true,
        'htmlOptions'=>[
            'container'=>'div', 
            'labelOptions'=>['class'=>'inline', 'style'=>'font-weight:normal'],
            'separator'=>'&nbsp;&nbsp;&nbsp;'
        ]
    ])) ?>
    </div>
</div>

5) Добавить конфигурацию в /config/dcart.php
return [
	...
	'extendKeys'=>['color', 'size'],
    'cartAttributes' => ['color', 'size'], // аттрибуты которые будут отображены дополнительно в виджете корзины
    'attributes' => [..., 'color', 'size'] // аттрибуты, которые будут сохранены для заказа    

6) Добавить на страницу товара в публичной части /views/product.php
use common\components\helpers\HYii as Y;
$sizes=$product->sizesBehavior->getRelated(false, ['scopes'=>['published', 'scopeSort'=>['product_sizes', null, false, '\crud\models\ar\ProductSize']]]);
$colors=$product->colorsBehavior->getRelated(false, ['scopes'=>['published', 'scopeSort'=>['product_colors', null, false, '\crud\models\ar\ProductColor']]]);
$hasOffers=!empty($sizes)||!empty($colors);
...
				<? if($hasOffers): ?>
					<div class="product-filter-block">
                        <? if($colors): ?>
                        <div class="product-params-filter product-params-filter__color">
                            <div class="product-params-filter-name">
                                <strong>Цвета:</strong>
                            </div>
                            <div class="product-params-filter-attr">
                                <? foreach($colors as $color): ?>
                                    <div class="product-params-filter-item">
                                        <input type="radio" id="pf-1-<?=$color->id?>" name="product_color" data-title="<?=$color->title?>" />
                                        <label style="background-color: <?=$color->getHex('#ccc')?>" for="pf-1-<?=$color->id?>" title="<?=$color->title?>"><? if(!$color->getHex()) echo $color->title?></label></div>
                                <? endforeach; ?>
                            </div>
                        </div>
                        <? endif; ?>
                        <? if($sizes): ?>
                        <div class="product-params-filter product-params-filter__size">
                            <div class="product-params-filter-name">
                                <strong>Выберите размеры:</strong>
                            </div>
                            <div class="product-params-filter-attr">
                                <? foreach($sizes as $size): ?>
                                <div class="product-params-filter-item">
                                    <input type="radio" id="pf-2-<?=$size->id?>" name="product_size"  data-title="<?=$size->title?>" />
                                    <label for="pf-2-<?=$size->id?>"><?=$size->title?></label>
                                </div>
                                <? endforeach; ?>
                            </div>
                        </div>
                        <? endif; ?>
                        <? 
                        $jscode='$(document).on("onBeforeAddToCart",".js__photo-in-cart",function(e, result){';
                        $jscodeReturn='';
                        if($sizes) {
                            $jscode.='var $size=$(".product-page [name=\'product_size\']");';
                            $jscode.='var sizeChecked=($size.is(":checked") > 0);';
                            $jscode.='var $sizeName=$size.parents(".product-params-filter:first").find(".product-params-filter-name");';
                            $jscode.='if(!sizeChecked) $sizeName.addClass("error"); else $sizeName.removeClass("error");';
                            $jscodeReturn.='sizeChecked';
                        }
                        if($colors) {
                            $jscode.='var $color=$(".product-page [name=\'product_color\']");';
                            $jscode.='var colorChecked=($color.is(":checked") > 0);';
                            $jscode.='var $colorName=$color.parents(".product-params-filter:first").find(".product-params-filter-name");';
                            $jscode.='if(!colorChecked) $colorName.addClass("error"); else $colorName.removeClass("error");';
                            $jscodeReturn.=($jscodeReturn?' && ':'').'colorChecked';
                        }
						$jscode.='result.valid='.$jscodeReturn.';if(result.valid){$(document).trigger("onAddToCart");}e.preventDefault();return false;});';
                        Y::js(false, $jscode, \CClientScript::POS_READY);
                        ?>
                    </div>
				<? endif; ?>

					... в кнопку корзины добавить атрибуты ...
					$this->widget('\DCart\widgets\AddToCartButtonWidget', array(
						...
						'attributes'=>[
							...
							['color', 'js:(function(){return $("[name=\'product_color\']:checked").data("title"); })'],
							['size', 'js:(function(){return $("[name=\'product_size\']:checked").data("title"); })']
						]

Если у кнопки в корзину есть анимация при добавлении, то при торговых предложениях убрать CSS класс вызывающий анимацию, и добавить обработку на событие onAddToCart, напр.:
$(document).on('onAddToCart', function(){
	toCartAnimation($(".js__main-photo"));
	$(this).addClass('in-cart-active');
});

7) Добавить стиль отображения ошибки
.product-page .product-params-filter-name {
    position: relative; 
}
.product-params-filter-name.error:after {
    content: '\041D \0435 \043E \0431 \0445 \043E \0434 \0438 \043C \043E  \0432 \044B \0431 \0440 \0430 \0442 \044C  \0437 \043D \0430 \0447 \0435 \043D \0438 \0435 ';
    color: #f00;
    font-size: 0.8em;
    display: block;
	position: absolute;
    top: -24px;
    width: 300px;
}
.product-params-filter__size .product-params-filter-name.error:after {
    content: '\041D \0435 \043E \0431 \0445 \043E \0434 \0438 \043C \043E  \0432 \044B \0431 \0440 \0430 \0442 \044C  \0440 \0430 \0437 \043C \0435 \0440 ';
}
.product-params-filter__color .product-params-filter-name.error:after {
    content: '\041D \0435 \043E \0431 \0445 \043E \0434 \0438 \043C \043E  \0432 \044B \0431 \0440 \0430 \0442 \044C  \0446 \0432 \0435 \0442 ';
}

8) В модель \Product добавить в scope "cardColumns" (в "select") атрибуты `t`.`colors`, `t`.`sizes`

9) В шаблон публичной части _products.php добавить
	<?php elseif($data->sizes || $data->colors): ?>
		<?= \CHtml::link('Подробнее', ['/shop/product', 'id'=>$data->id], ['class'=>'shop-button']); ?>
	<?php else: ?>
		... кнопка добавления в корзину ...
