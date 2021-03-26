<?php
//use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HHtml;
use common\components\helpers\HTools;
use cdek\components\helpers\HCdek;
use cdek\models\Tariff;

$cart=\Yii::app()->cart;
$totalWeight=A::get($result, 'totalWeight');
$totalVolume=A::get($result, 'totalVolume');
$result=$result['result']['result'];
?>
<b>Компания-перевозчик:</b> СДЭК<br />
<b>Тип доставки:</b> <?=Tariff::i()->modePublicLabels(Tariff::i()->getTariffMode($result['tariffId']));?><br />
<b>Стоимость заказа:</b> <?=HHtml::price($cart->getTotalPrice())?> руб.<br />
<b>Стоимость доставки:</b> <?=HHtml::price($result['price'])?> руб.<br />
<? /* ?>
<span class="secondary"><b>Тариф #<?=$result['tariffId']?>:</b> <?=Tariff::i()->tariffLabel($result['tariffId'])?></span><br />
<span class="secondary"><b>Срок доставки:</b> <?=$result['deliveryPeriodMin']?>-<?=$result['deliveryPeriodMax']?> дн.</span><br />
<span class="secondary"><b>Планируемая дата доставки:</b> c <?=$result['deliveryDateMin']?> по <?=$result['deliveryDateMax']?></span><br />
            
<span class="secondary"><b>Общий вес товаров:</b> <?=$totalWeight?> кг</span><br />
<span class="secondary"><b>Общий объем товаров:</b> <?=$totalVolume?> м<sup>3</sup></span><br />
<? /**/ ?>
<b>Итоговая сумма заказа c доставкой:</b> <?=HHtml::price($cart->getTotalPrice() + (float)$result['price'])?> руб.<br />
            
<? /* if($cashOnDelivery=A::get($result, 'cashOnDelivery')): ?>
    Ограничение оплаты наличными, от (руб): <?=$result['cashOnDelivery']?><br />
<? endif; */ ?>
