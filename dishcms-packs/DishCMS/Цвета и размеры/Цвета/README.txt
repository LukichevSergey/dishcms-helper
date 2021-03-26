1) Скопировать файлы в соотвествующие папки из protected

2) Добавить настройки в /protected/config/crud.php
'product_colors'=>'application.config.crud.product_colors',

3) добавить в модель Product (/protected/models/Product.php) 
- свойство
	public $color;
	
- поведение
public function behaviors()
{
	...
	
	'colorsBehavior'=>[
        'class'=>'\common\behaviors\ARAttributeListBehavior',
		'attribute'=>'colors',
        'rel'=>'\ProductColor',
        'addColumn'=>false
     ]
     
	...
}

- подписи
public function attributeLabels()
{
	...
	
	'colors'=>'Цвета',
    'color'=>'Цвет',
	
	...
}

// корзина

4) Добавить параметры в (/protected/config/dcart.php)
return array(
	...
	'extendKeys'=>['color'],
	'cartAttributes' => ['color'], 
    'attributes' => [..., 'color']

5) отображение в заказе /protected/views/dOrder/order.php   
'mailAttributes' => array('color'),
'adminMailAttributes' => array('color')

5.2) Поправить скрипт /protected/modules/DCart/widgets/assets/js/dcart_add_to_button_widget.js
$(document).on("click", ".dcart-add-to-cart-btn", function(e) {
	e.preventDefault();
    var canBuy=typeof(window.productCanBuy)=="undefined" ? true : window.productCanBuy();
    if(canBuy) {
        DCart.add($(this).attr("href"), $(this).attr("data-dcart-attributes"), e);
    }
    else {
        e.stopImmediatePropagation();
    }
}

5.3) Добавить стиль
.product-params-filter-name.error:after {
    content: '\041D \0435 \043E \0431 \0445 \043E \0434 \0438 \043C \043E  \0432 \044B \0431 \0440 \0430 \0442 \044C  \0446 \0432 \0435 \0442 ';
    color: #f00;
    font-size: 0.8em;
    display: block;
    position: absolute;
}


6) В карточке товара

- отображение
<? if($colors=$product->colorsBehavior->getRelated()): ?>
    <div class="product-params-filter product-params-filter__color">
        <div class="product-params-filter-name">
            <strong>Выберите цвет:</strong>
        </div>
        <div class="product-params-filter-attr">
            <? foreach($colors as $color): ?>
            <div class="product-params-filter-item">
                <input type="radio" id="pf-2-<?=$color->id?>" name="product_color"  data-title="<?=$color->title?>" />
                <label for="pf-2-<?=$color->id?>"<? if($color->hexcode) echo ' style="background: '.$color->hexcode.'"'?> title="<?=$color->title?>">&nbsp;</label>
            </div>
            <? endforeach; ?>
        </div>
    </div>
<? endif; ?>

6.2) добавить скрипт
use common\components\helpers\HYii as Y;
...
<? 
$jscode='window.productCanBuy=function(){';
$jscodeReturn='';
---
если есть выбор размера
if($sizes) {
    $jscode.='var $size=$(".product-page [name=\'product_size\']");';
    $jscode.='var sizeChecked=($size.is(":checked") > 0);';
    $jscode.='var $sizeName=$size.parents(".product-params-filter:first").find(".product-params-filter-name");';
    $jscode.='if(!sizeChecked) $sizeName.addClass("error"); else $sizeName.removeClass("error");';
    $jscodeReturn.='sizeChecked';
}
---
if($colors) {
    $jscode.='var $color=$(".product-page [name=\'product_color\']");';
    $jscode.='var colorChecked=($color.is(":checked") > 0);';
    $jscode.='var $colorName=$color.parents(".product-params-filter:first").find(".product-params-filter-name");';
    $jscode.='if(!colorChecked) $colorName.addClass("error"); else $colorName.removeClass("error");';
    $jscodeReturn.=($jscodeReturn?' && ':'').'colorChecked';
}

$jscode.='return '.($jscodeReturn?:'true').';};';
Y::js(false, $jscode, \CClientScript::POS_READY);
?>



...
- кнопка
<?php $this->widget('\DCart\widgets\AddToCartButtonWidget', [
    'id' => $product->id,
    'model' => $product,
    'title'=>'<span>Купить</span>',
    'cssClass'=>'shop-button to-cart button_1 js__photo-in-cart open-cart',
    'attributes'=>[
        ['color', 'js:(function(){return $("[name=\'product_color\']:checked").data("title"); })']
    ]
]);
?>



// админка

3) добавить пункт меню (/protected/modules/admin/config/menu.php)
расскомментарить use crud\components\helpers\HCrud;

HCrud::getMenuItems(Y::controller(), 'product_colors', 'crud/index', true),

4) Добавить в форму редактирования товара (/protected/modules/admin/views/shop/_form_product.php)
<div class="panel panel-default">
        <div class="panel-heading">
            <?= $form->labelEx($model, $model->colorsBehavior->attribute); ?>
        </div>
        <div class="panel-body">
        <? $this->widget('\common\widgets\form\CheckboxListField', A::m(compact('form', 'model'), [
            'attribute'=>$model->colorsBehavior->attribute,
            'data'=>\ProductColor::model()->listData('title'),
            'hideLabel'=>true,
            'htmlOptions'=>[
                'container'=>'div', 
                'labelOptions'=>['class'=>'inline', 'style'=>'font-weight:normal'],
                'separator'=>'&nbsp;&nbsp;&nbsp;'
            ]
        ])) ?>
        </div>
    </div>


