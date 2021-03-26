<?php
/** @var \cdek\models\Order $order */
use common\components\helpers\HHtml;
use cdek\components\helpers\HCdek;
use cdek\models\Tariff;
?>
<br/>
<table class="table table-striped table-bordered">
    <tr>
        <td>Статус</td>
        <td><? 
        	$title='';
        	if(($order->status == $order::STATUS_CDEK_ERROR) && ($order->comment && ($xml=simplexml_load_string($order->comment)))) {
        		$title=/*(string)$xml->Order[0]['ErrorCode'] . ' ' . */(string)$xml->Order[0]['Msg'];
        	}
        	?>
        	<?=\CHtml::tag("div", ["class"=>HCdek::getStatusCssClass($order->status, 'label label'), 'title'=>$title], $order->statusLabels($order->status));?>
        </td>
    </tr>
    <? if($order->dispatch_number): ?>
    <tr>
        <td>Номер накладной</td><td><?=$order->dispatch_number?></td>
    </tr>
    <? endif; ?>
    <tr>
        <td>Стоимость доставки</td><td><?=HHtml::price($order->delivery_price)?> руб.</td>
    </tr>
    <tr>
        <td>Стоимость доставки без наценки</td><td>~ <?=HHtml::price(((float)$order->delivery_price * 100) / (100 + (float)$order->delivery_extra_charge))?> руб.</td>
    </tr>
    <tr>
        <td>Наценка</td><td><?=$order->delivery_extra_charge?> %</td>
    </tr>
    <tr>
        <td>Тариф</td><td>#<?=$order->tariff_id?> <?=Tariff::i()->tariffLabel($order->tariff_id)?></td>
    </tr>
    <tr>
        <td>Общий вес</td><td><? if($order->package_weight) echo $order->package_weight; else echo 'менее 1'?> г</td>
    </tr>
       
    <? if($order->isPvzMode(Tariff::i()->getTariffMode($order->tariff_id))): ?>
        <tr>
            <td>Тип доставки</td><td>До пункта выдачи заказов (ПВЗ)</td>
        </tr>
        <tr>
            <td>Информация о ПВЗ</td>
            <td><?$pvz=$order->getPvzData()?>
                <b>Название</b>: <?=$pvz['Name']?><br/>
                <b>Время работы</b>: <?=$pvz['WorkTime']?><br/>
                <b>Телефон</b>: <?=$pvz['Phone']?><br/>
                <b>Полный адрес</b>: <?=$pvz['FullAddress']?>
            </td>
        </tr>
    <? else: ?>
        <tr>
            <td>Тип доставки</td><td>До адреса покупателя</td>
        </tr>
        <tr>
            <td>Адрес</td><td>г.<?=$order->rec_city_name?>, ул.<?=$order->address_street?>, <?=$order->address_house?>, <?=$order->address_flat?></td>
        </tr>
    <? endif; ?>
</table>
