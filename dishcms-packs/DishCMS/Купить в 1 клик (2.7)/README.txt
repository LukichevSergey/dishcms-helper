1) Поправить шаблон поля Hidden /protected/modules/feedback/widgets/types/views/hidden.php

<?php
/** @var \feedback\widgets\types\TextTypeWidget $this */
/** @var FeedbackFactory $factory */
/** @var string $name attribute name. */
use common\components\helpers\HArray as A;

$htmlOptions=A::m(A::m([
    
], $factory->getOption("attributes.{$name}.htmlOptions", [])), A::get($this->params, 'htmlOptions', []));
?>

<?php echo $form->hiddenField($factory->getModelFactory()->getModel(), $name, $htmlOptions); ?>


2) Добавить в стили формы где нужно
styles.less 
[id^="buy1click"] или #buy1click
...
.cbSubHead {
    text-align: center;
}

#buy1click .feedback-body.successed {
    text-align: center;
    margin: 20px;
    font-size: 24px;
}


3) Добавить виджет и кнопку (пример для шаблона product.php)
<div class="one-click"> 
	<a href="javascript:;" class="btn product-more" data-src="#form-buy1click" data-fancybox>Купить в 1 клик</a>
</div>

<div style="display: none;">
    <div id="form-buy1click">
        <div class="popup-info">
            <?php $this->widget('\feedback\widgets\FeedbackWidget', array(
                'id' => 'buy1click', 
                'view'=>'buy1click', 
                'title'=>'Купить в 1 клик', 
                'params'=>['product_id'=>$product->id, 'product_title'=>$product->title]
            )); ?>
        </div>
    </div>
</div>


