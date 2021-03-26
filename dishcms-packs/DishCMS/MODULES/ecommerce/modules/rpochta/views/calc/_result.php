<?php
//use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HHtml;
use common\components\helpers\HTools;
use rpochta\components\helpers\HRPochta;
use rpochta\components\RPochtaConst;
use rpochta\models\Order;

$cart=\Yii::app()->cart;
$totalPrice=HRPochta::toRuble(A::rget($result, 'result.total-rate', 0)) + HRPochta::toRuble(A::rget($result, 'result.total-vat', 0));
?>
<b>Компания-перевозчик:</b> Почта.России<br />
<b>Тип доставки:</b> До адреса покупателя<? //=Order::model()->modePublicLabels(Tariff::i()->getTariffMode($result['tariffId']));?><br />
<b>Стоимость заказа:</b> <?= HHtml::price($cart->getTotalPrice()); ?> руб.<br />
<b>Стоимость доставки:</b> <?= HHtml::price($totalPrice); ?> руб.<br />
<b>Итоговая сумма заказа c доставкой:</b> <?=HHtml::price($cart->getTotalPrice() + $totalPrice)?> руб.<br />
