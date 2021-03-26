<?php
/** @var \ecommerce\modules\robokassa\widgets\PaymentForm $this */
/** @var crud\models\ar\robokassa\models\Payment $payment */

use ecommerce\modules\robokassa\components\helpers\HRobokassa;

echo \CHtml::tag('h2', ['class'=>'paymentform__header'], HRobokassa::settings()->title_payment_form);
echo \CHtml::tag('div', ['class'=>'paymentform__text'], HRobokassa::settings()->text_payment_form);

echo \CHtml::openTag('form', ['action'=>$this->action, 'method'=>$this->method, 'class'=>'js-robokassa-payment-form']);
    if(HRobokassa::isTestMode()) {
        echo \CHtml::hiddenField('IsTest', 1);
    }
    echo \CHtml::hiddenField('MerchantLogin', HRobokassa::getMerchantLogin());
    echo \CHtml::hiddenField('OutSum', $this->payment->getSum());
    echo \CHtml::hiddenField('InvId', $this->payment->getInvoiceId());
    echo \CHtml::hiddenField('Description', $this->payment->getDescription());
    echo \CHtml::hiddenField('SignatureValue', HRobokassa::createSignature($this->payment));
    echo \CHtml::hiddenField('IncCurrLabel', $this->currency);
    echo \CHtml::hiddenField('Culture', $this->culture);
    foreach ($this->payment->getShps() as $key => $value) {
        echo \CHtml::hiddenField("Shp_{$key}", $value);
    }
    echo \CHtml::submitButton($this->submitLabel, $this->submitOptions);
echo \CHtml::closeTag('form');
if(!HRobokassa::isDebugMode()) {
    echo \CHtml::tag('script', [], 'setTimeout(function(){$(".js-robokassa-payment-form").submit();}, 500);');
}
?>