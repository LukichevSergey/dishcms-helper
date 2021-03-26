<?php
/** @var \rpochta\models\Order $order */
use common\components\helpers\HHtml;
use rpochta\components\RPochtaConst;
use rpochta\components\helpers\HRPochta;
?>
<br/>
<table class="table table-striped table-bordered">
    <tr>
        <td style="width:30%">Статус</td>
        <td><? 
        	$title='';
        	if(($order->status == $order::STATUS_RPOCHTA_ERROR) && ($order->comment && ($xml=simplexml_load_string($order->comment)))) {
        		$title=/*(string)$xml->Order[0]['ErrorCode'] . ' ' . */(string)$xml->Order[0]['Msg'];
        	}
        	?>
        	<?=\CHtml::tag("div", ["class"=>HRPochta::getStatusCssClass($order->status, 'label label'), 'title'=>$title], $order->statusLabels($order->status));?>
        </td>
    </tr>
    <? if($ids=$order->getResultIds()): ?>
    <tr>
        <td>ID заказа в сервисе</td><td><?=implode(', ', $ids)?></td>
    </tr>
    <? endif; ?>
    <tr>
        <td>Стоимость доставки</td><td><?=HHtml::price($order->delivery_price)?> руб.</td>
    </tr>
    <tr>
        <td>Стоимость доставки без наценки</td><td><?=HHtml::price($order->delivery_origin_price)?> руб.</td>
    </tr>
    <tr>
        <td>Наценка</td><td><?=$order->delivery_extra_charge?:0?> %</td>
    </tr>
    <tr>
        <td>Общий вес</td><td><? if($order->mass) echo $order->mass; else echo 'менее 1'?> г</td>
    </tr>
    
    <? if($order->payment_type): ?>
    <tr>
        <td>Тип платежа</td><td><?= RPochtaConst::i()->paymentTypeLabels($order->payment_type); ?></td>
    </tr>
    <? endif; ?>
    <? if($order->rpo_category): ?>
    <tr>
        <td>Категория РПО</td><td><?= RPochtaConst::i()->rpoCategoryLabels($order->rpo_category); ?></td>
    </tr>
    <? endif; ?>
    <? if($order->rpo_type): ?>
    <tr>
        <td>Вид РПО</td><td><?= RPochtaConst::i()->rpoTypeLabels($order->rpo_type); ?></td>
    </tr>
    <? endif; ?>    
       
    <? if($order->isOpsMode()): ?>
        <tr>
            <td>Тип доставки</td><td>До отделения Почты России (ОПС)</td>
        </tr>
        <tr>
            <td>Информация об ОПС</td>
            <td>
                <? $ops=$order->getOpsData(); ?>
                <b>Индекс</b>: <?=$ops['postal-code']?><br/>
                <b>Полный адрес</b>:<br/> <?=$order->city_name_to?>, <?=$ops['address-source']?>
            </td>
        </tr>
    <? else: ?>
        <tr>
            <td>Тип доставки</td><td>До адреса покупателя</td>
        </tr>
        <? 
        $addressData=$order->getAddressData(); 
        if(is_array($addressData)):
            ?><tr><td>Оригнальный адрес</td><td><?=$addressData['original-address'];?></td></tr><?
            ?><tr>
                <td>
                    Адрес доставки
                </td>
                <td><?
            $address=[];
            foreach(RPochtaConst::i()->addressResultFields() as $name=>$label) {
                if(isset($addressData[$name]) && trim($addressData[$name])) {
                    echo '<span style="color:#555;display:inline-block">', $label, ':</span> <b>', trim($addressData[$name]), '</b><br/>';
                }
            }
            ?></td></tr><?
        else:
            ?><tr><td>Адрес</td><td><?
                echo 'г.', $order->city_name_to, ', ул.', $order->address_street, ', ', $order->address_house, ', ', $order->address_room;
            ?></td></tr><?
        endif;
        ?>
    <? endif; ?>
</table>
