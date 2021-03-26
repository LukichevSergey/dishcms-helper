<?php
/** 
 * @var \DOrder\models\DOrder $order
 **/
use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HHash;
use common\components\helpers\HHtml;
use common\components\helpers\HTools;

$t=Y::ct('\YkassaModule.controllers/httpPayment', 'ecommerce.ykassa');

$taxSystem=1;
$customerData=$order->getCustomerData();
$orderData=$order->getOrderData();

$sum=$order->getTotalPrice();

// @var float $deliveryPrice стоимость доставки
$deliveryPrice=(float)A::rget($customerData, 'delivery_price.value', 0);
// @FIXME: Жесткая привязка к доставке СДЭК
//if($order->isCdekDeliveryType()) {
//    $deliveryPrice=(float)$order->cdek->delivery_price;
//}

$totalSum=$sum + $deliveryPrice;

$phone=preg_replace('/[^+0-9]+/', '', A::rget($customerData, 'phone.value'));
$email=A::rget($customerData, 'email.value');
$customerContact=$phone ?: ($email ?: false);

if(!$customerContact) {
	echo 'Не задан номер телефона или E-Mail покупателя. Оплата через Яндекс.Кассу невозможен.';
}
else {

$modeChooseTypes=!(Y::param('payment.ymhttp.types') === false);

if($modeChooseTypes) {
    echo \CHtml::tag('p', [], $t('note.choose_types'));
}
else {
    echo \CHtml::tag('p', [], $t('note.type_default'));
}

$formId=HHash::u('ymhttp');
echo \CHtml::form(Y::param('payment.ymhttp.action'), 'post', ['id'=>$formId]);
    echo \CHtml::hiddenField('shopId', Y::param('payment.ymhttp.shopId'));
    echo \CHtml::hiddenField('scid', Y::param('payment.ymhttp.scid'));
    echo \CHtml::hiddenField('customerNumber', 'Заказ #'.$order->id);
    echo \CHtml::hiddenField('sum', $totalSum);
    
    echo \CHtml::hiddenField('orderNumber', $order->hash);
    
    if($phone) {
        echo \CHtml::hiddenField('cps_phone', $phone);
    }
    
    if($email) {
        echo \CHtml::hiddenField('cps_email', $email);
    }
    
    if($modeChooseTypes) {
    	$this->widget('\ykassa\widgets\PaymentTypeField', [
    		'types'=>Y::param('payment.ymhttp.types', true),
    		'default'=>Y::param('payment.ymhttp.paymentType', false),
    		'jSubmit'=>'.payment-ym-button'
    	]);
    }
    else {
    	echo \CHtml::hiddenField('paymentType', A::rget($customerData, 'paymentType.value', 'AC'));
    }
    
    // данные для чека
    // https://tech.yandex.ru/money/doc/payment-solution/payment-form/payment-form-receipt-docpage/
    $ymMerchantReceiptItems=[];
    foreach($orderData as $orderItem) {
        $ymMerchantReceiptItems[]=[
            'quantity'=>(int)A::rget($orderItem, 'count.value', 1),
            'price'=>[
                'amount'=>(float)A::rget($orderItem, 'price.value', 0),
            ],
            'currency'=>'RUB',
            // Ставка НДС. Возможные значения — число от 1 до 6:
            // 1 — без НДС;
            // 2 — НДС по ставке 0%;
            // 3 — НДС чека по ставке 10%;
            // 4 — НДС чека по ставке 18%;
            // 5 — НДС чека по расчетной ставке 10/110;
            // 6 — НДС чека по расчетной ставке 18/118.
            'tax'=>A::rget($orderItem, 'ymtax.value', $taxSystem),
            'text'=>mb_substr(A::rget($orderItem, 'title.value'), 0, 127) //HTools::cyr2lat(A::rget($orderItem, 'title.value'))
        ];
	}
	
    if($deliveryPrice > 0) {
        $ymMerchantReceiptItems[]=[
            'quantity'=>1,
            'price'=>[
                'amount'=>$deliveryPrice,
            ],
            'currency'=>'RUB',
            // Ставка НДС. Возможные значения — число от 1 до 6:
            // 1 — без НДС;
            // 2 — НДС по ставке 0%;
            // 3 — НДС чека по ставке 10%;
            // 4 — НДС чека по ставке 18%;
            // 5 — НДС чека по расчетной ставке 10/110;
            // 6 — НДС чека по расчетной ставке 18/118.
            'tax'=>Y::param('payment.ymhttp.delivery.tax', $taxSystem),
            'text'=>Y::param('payment.ymhttp.delivery.title', $t('delivery.title'))//HTools::cyr2lat(Y::param('payment.ymhttp.delivery.title', $t('delivery.title')))
        ];
    }
    
    echo \CHtml::textArea('ym_merchant_receipt', json_encode([
    	'customerContact'=>$customerContact,
		// taxSystem 
        // 1 — общая СН;
        // 2 — упрощенная СН (доходы);
        // 3 — упрощенная СН (доходы минус расходы);
        // 4 — единый налог на вмененный доход;
        // 5 — единый сельскохозяйственный налог;
        // 6 — патентная СН.
        'taxSystem'=>$taxSystem,
    	'items'=>$ymMerchantReceiptItems
    ], JSON_UNESCAPED_UNICODE), ['style'=>'display:none !important']);
    
    if($customerName=A::rget($customerData, 'name.value')) {
        echo \CHtml::hiddenField('custName', $customerName);
    }
    
    if($customerName=A::rget($customerData, 'address.value')) {
        echo \CHtml::hiddenField('custAddr', $customerName);
    }
    
    echo \CHtml::submitButton($t('btn.pay'), ['class'=>'payment-ym-button', 'style'=>($modeChooseTypes?'display:none':'')]);
echo \CHtml::endForm();

if(!$modeChooseTypes) {
    Y::js(false, 'setTimeout(function(){$("#'.$formId.'").submit();},5000);');
}

}
?>
