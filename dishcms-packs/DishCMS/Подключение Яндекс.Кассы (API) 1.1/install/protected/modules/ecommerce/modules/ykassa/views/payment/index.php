<?php
/** @var \ykassa\controllers\PaymentController $this */
/** @var \crud\models\ar\ykassa\models\History $payment */
/** @var string $confirmationUrl */

use common\components\helpers\HYii as Y;
use common\components\helpers\HHash;
use ykassa\components\helpers\HYKassa;

$payBtnId=HHash::ujs();

if($styles=implode(';', array_filter(array_map('trim', explode(";", HYKassa::settings()->btn_pay_styles)), 'strlen'))) {
    Y::css('y-pay-btn-styles', ".btn-y-pay{{$styles}}.btn-y-pay:hover{text-decoration:none !important;}");
}

if(!HYKassa::isDebugMode()) {
    Y::js('y-pay-btn', ';setTimeout(function(){window.location.href=$("#' . $payBtnId . '").attr("href");},50);', \CClientScript::POS_READY);
}
?>
<h1><?= HYKassa::settings()->page_payment_title; ?></h1>

<div class="payment__text">
    <?= HYKassa::settings()->page_payment_text; ?>
</div>

<div class="payment__pay-button-wrapper">
    <?= \CHtml::link(HYKassa::settings()->btn_pay_label ?: 'Оплатить', $confirmationUrl, ['class'=>'btn-y-pay', 'id'=>$payBtnId]); ?>
</div>