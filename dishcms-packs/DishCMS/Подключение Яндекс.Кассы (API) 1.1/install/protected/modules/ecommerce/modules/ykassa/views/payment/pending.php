<?php
/** @var \ykassa\controllers\PaymentController $this */
/** @var \crud\models\ar\ykassa\models\History $payment */
/** @var string $confirmationUrl */

use common\components\helpers\HYii as Y;
use ykassa\components\helpers\HYKassa;

if(!empty($confirmationUrl)) {
    if($styles=implode(';', array_filter(array_map('trim', explode(";", HYKassa::settings()->btn_pay_styles)), 'strlen'))) {
        Y::css('y-pay-btn-styles', ".btn-y-pay{{$styles}}.btn-y-pay:hover{text-decoration:none !important;}");
    }
}
?>
<h1>Ожидает оплаты</h1>

<div class="payment__text">
    <p>Ваш заказ ожидает оплаты.</p>
</div>

<? if(!empty($confirmationUrl)) { ?>
    <div class="payment__pay-button-wrapper">
        <?= \CHtml::link(HYKassa::settings()->btn_pay_label ?: 'Оплатить', $confirmationUrl, ['class'=>'btn-y-pay']); ?>
    </div>
<? } ?>