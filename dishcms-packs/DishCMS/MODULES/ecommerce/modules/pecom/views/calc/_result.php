<?php
/**
 * @var array $result
 * @var boolean isOversized
 */
//use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HHtml;
use pecom\components\helpers\HPecom;

$cart=\Yii::app()->cart;

$totalPrice=HPecom::getCalcTotalPrice($result, $isOversized);
?>
<? if($periodsDays=A::get($result, 'periods_days')): ?>
    <b>Количество суток в пути:</b> <?=$periodsDays?><br />
<? endif; ?>
<b>Стоимость заказа:</b> <?= HHtml::price($cart->getTotalPrice()); ?> руб.<br />
<b>Стоимость доставки:</b> <?= HHtml::price($totalPrice); ?> руб.<br />
<b>Итоговая сумма заказа c доставкой:</b> <?=HHtml::price($cart->getTotalPrice() + $totalPrice)?> руб.<br />
