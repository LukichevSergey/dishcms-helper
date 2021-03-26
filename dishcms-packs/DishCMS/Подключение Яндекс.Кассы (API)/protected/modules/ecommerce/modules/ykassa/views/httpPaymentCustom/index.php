<?php
/** @var \ykassa\controllers\HttpPaymentCustomController */
/** @var \crud\models\ar\ykassa\models\CustomPayment $payment */
use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HHash;
use common\components\helpers\HHtml;
use common\components\helpers\HTools;
use ykassa\components\helpers\HYKassa;

$t=Y::ct('\YkassaModule.controllers/httpPayment', 'ecommerce.ykassa');
$formId=HHash::u('ymhttp');
if(!$payment->sum){$payment->sum=null;}
?>
<h1><?= HYKassa::settings()->title_payment_form; ?></h1>
<?
$this->beginWidget('\common\widgets\form\ActiveForm', [
    'id'=>$formId,
    'model'=>$payment,
    'attributes'=>['name', 'phone', 'email', 'sum', 'payment_type'],
    'types'=>[
        'phone'=>'phone',
        'comment'=>function($widget, $form, $attribute) {
            $widget->renderRowOpenTag(['class'=>'choose-time-form__item choose-time-form__textarea'], 'comment');
            $widget->renderAttributeLabel($form, $attribute);
            echo $form->textArea($widget->model, $attribute, $widget->getAttributeHtmlOptions($attribute));
            $widget->renderAttributeError($form, $attribute);
            $widget->renderRowCloseTag();
        },
        'payment_type'=>function($widget, $form, $attribute) {
            $types=['AC'];
            // $types=A::toa(HYKassa::settings()->payment_type);
            if(empty($types)) { $types[]='AC'; }
            if(count($types) === 1) {
                $widget->model->payment_type=reset($types);
                echo $form->hiddenField($widget->model, $attribute, $widget->getAttributeHtmlOptions($attribute));
                $widget->renderAttributeError($form, $attribute);
            }
        }
    ],
    'errorSummary'=>false,
    'formOptions'=>[
        'enableAjaxValidation'=>true,
        'clientOptions'=>[
            'hideErrorMessage'=>true,
            'afterValidate'=>'js:function(form, data, hasError){if(!hasError&&(typeof data.yfid!="undefined")&&(typeof data.yf!="undefined")){'
                . '$("#'.$formId.'").html(data.yftxt);$("#'.$formId.'").append(data.yf);let submitted=0;'
                . 'setInterval(function(){if($("#"+data.yfid).length&&!submitted){submitted=1;$("#ybtn"+data.yfid).click();}},50);}return false;}'
        ],
    ],
    'htmlOptions'=>[
        'class'=>'choose-time-form',
        'rowTag'=>'div',
        'rowOptions'=>['class'=>'choose-time-form__item'],
        'attributeOptions'=>[
            'name'=>['input'=>['class'=>'choose-time-form__text', 'placeholder'=>'Имя*'], 'label'=>false],
            'phone'=>['input'=>['class'=>'choose-time-form__text', 'placeholder'=>'Номер телефона*'], 'label'=>false],
            'email'=>['input'=>['class'=>'choose-time-form__text', 'placeholder'=>'Email*'], 'label'=>false],
            'sum'=>['input'=>['class'=>'choose-time-form__text', 'placeholder'=>'Сумма'], 'label'=>false],
            'comment'=>['input'=>['class'=>'choose-time-textarea', 'placeholder'=>'Комментарий'], 'label'=>false],
        ]
        
    ],
    'submitLabel'=>function($widget, $form) {
        ?>
        <div class="choose-time-form__item choose-time-form__item_btn2">
            <input type="submit" value="Оплатить" class="btn choose-btn">
        </div>
        <?php $widget->renderAttribute($form, 'comment'); ?>
        <div class="choose-time-form__item choose-time-form__lie">Ваши персональные данные не передаются третьим лицам</div>
        <?php
    }        
]);
?>
<div class="time-block">
	<div class="time-block__in">
		<div class="time-block__line time-block__line_2">
			#BEGINFORM#
			<div class="choose-time-form__in">
				#FORM#
			</div>
			#ENDFORM#
    	</div>
  	</div>
</div>
<?php $this->endWidget(); ?>

<div class="payment__description" style="margin-top:20px">
	<?= HYKassa::settings()->text_payment_form; ?>
</div>