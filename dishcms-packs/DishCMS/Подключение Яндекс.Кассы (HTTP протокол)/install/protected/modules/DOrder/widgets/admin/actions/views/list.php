<?php
/** @var \DOrder\widgets\admin\actions\ListWidget $this */
/** @var \DOrder\models\DOrder[] $model */
/** @var \CPagination $pages */
use common\components\helpers\HArray as A;

$modelProduct=Product::model();
?>
<h1>Заказы</h1>

<table id="orders" class="dorder-admin-list">
    <tr class="head">
        <td>№</td>
        <td>ФИО, контакты</td>
        <td></td>
        <td>Сумма</td>
        <td>Дата</td>
        <td>Статус</td>
		<td style="width:30px">Оплачен</td>
        <td></td>
    </tr>

    <?foreach($model as $item):?>
        <tr class="order<?=D::c(($item->paid == 1), ' payment_complete')?> dorder-list-item" data-item="<?=$item->id?>">
            <td class="number"><?=$item->id?>.</td>
            <td class="info" colspan="2">
                <?=\CHtml::link('Заказ №' . $item->id, 'javascript:void()', array('class' => 'orderuser', 'data-item' => $item->id))?>
				<br/>Код заказа: <?=$item->hash?>
                <?$customer = $item->getCustomerData()?>
                <?foreach($customer as $k=>$f):
                  	if($k != 'comment' && $f['value'] && ($k!='privacy_policy')):
                   	?><span><em><?=$f['label']?>:</em> <?=$f['value']?></span><?
                   	endif;
                endforeach; ?>
                <?/* if($payment=A::rget($customer, 'payment.value')): 
                  	?><span><em><?=A::rget($customer, 'payment.label')?>:</em> <?=$payment?></span><?
                endif;*/ ?>
            </td>

            <td class="sumprice"><?=$item->getTotalPrice()?> р.</td>
            <td><?=\YiiHelper::formatDate($item->create_time)?></td>
            <td><div class="mark <?=(!$item->completed) ? 'marked' : 'unmarked'?>" data-item="<?=$item->id?>"></div></td>
			<td align="center">
			    <? if($item->in_paid):?>
				    <span data-js="order-paid-<?=$item->id?>" class="label label-warning">в процессе</span>
			    <? else: ?>
				    <span data-js="order-paid-<?=$item->id?>" class="label label-<?=$item->paid ? 'success' : 'danger'?>"><?=$item->paid ? 'Да' : 'Нет'?></span>
				    <br/><br/>
				    <?=\CHtml::ajaxButton(
				        'изменить',
				        'dOrder/changePaid/'.$item->id,
				        [
				            'dataType'=>'json',
				            'beforeSend'=>'js:function(){return confirm("Подтвердите изменение статуса платежа заказа #'.$item->id.'");}',
				            'success'=>'js:function(r){if(r.success){'
				                .'var $o=$("[data-js=\'order-paid-'.$item->id.'\']");'
				                .'$o.removeClass(r.data.paid?"label-danger":"label-success");'
				                .'$o.addClass(r.data.paid?"label-success":"label-danger");'
				                .'$o.text(r.data.paid?"Да":"Нет");'
				                .'}}'
					        ],
				        ['class'=>'btn btn-xs btn-default']
				    );?>
			    <? endif; ?>
			</td>
            <?/*?><td><div class="mark_green <?php echo !$item->paid ? 'marked_green' : 'unmarked_green'; ?>" data-item="<?php echo $item->id; ?>"></div></td><?*/?>
            <td><?=\CHtml::link('Удалить', 'javascript:void()', array('class'=>'dorder-btn-delete', 'data-item'=>$item->id))?></td>
        </tr>
        <?foreach ($item->getOrderData() as $hash=>$attributes):?>
            <tr class="details dorder-list-item-details" data-item="<?=$item->id?>">
                <td colspan="2"><?
	            	$productId=$attributes['id']['value'];
	            	$modelProduct->id=$productId;
	            	$itemLink=Yii::app()->createUrl('shop/product', array('id'=>$productId));
                    echo \CHtml::link(CHtml::image($attributes['image']['value']?:'http://placehold.it/36'), $itemLink, array('target'=>'_blank', 'class'=>'image'));
					?>
                	<?=$attributes['title']['value']?><br />
                	<?$i=0; foreach($attributes as $attribute=>$data): 
                		if($data['value'] && !in_array($attribute, array('id', 'model', 'categoryId', 'price', 'count', 'title', 'privacy_policy', 'image'))):?>
	    				<?=$i++?' / ':''?><small><b><?=$data['label']?>:</b> <?=$data['value']?></small>
	    				<?endif; 
	    			endforeach; ?>
                </td>
                <td class="count"><?=$attributes['count']['value']?></td>
                <td class="sum"><?=$attributes['price']['value']?> р.</td>
                <td colspan="4"><?=$attributes['count']['value'] * $attributes['price']['value']?> р.</td>
            </tr>
        <?endforeach?>
            <tr class="details dorder-list-item-comment" data-item="<?=$item->id?>">
                <td colspan="8"><textarea data-item="<?=$item->id?>" class="comment"><?=$item->comment ?: @$customer['comment']['value']?></textarea></td>
            </tr>
    <?endforeach?>
</table>
<br />
<?$this->widget('CLinkPager', array(
    'pages' => $pages,
));?>
