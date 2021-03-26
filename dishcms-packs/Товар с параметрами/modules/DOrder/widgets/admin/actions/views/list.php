<?php
/** @var \DOrder\widget\admin\ListWidget $this */
/** @var \DOrder\models\Order $model */
/** @var \CPagination $pages */
?>
<h1>Заказы</h1>

<table id="orders" class="dorder-admin-list">
    <tr class="head">
        <td>№</td>
        <td style="border-right:none; width:280px;">ФИО, контакты</td>
        <td style="border:none;width:35px;"></td>
        <td style="width:80px;">Сумма</td>
        <td>Дата</td>
        <td>Статус</td>
        <?php /* ?><td>Оплачен</td> <?php */?>
        <td></td>
    </tr>

    <?php foreach($model as $item): ?>
        <tr class="order<?php if($item->paid == 1) echo ' payment_complete'; ?> dorder-list-item" data-item="<?php echo $item->id; ?>">
            <td class="number"><?php echo $item->id; ?>.</td>
            <td colspan="2">
            	<?php $customer = $item->getCustomerData()?>
                    <?php echo \CHtml::link($customer['name']['value'], 'javascript:void()', array('class' => 'orderuser', 'data-item' => $item->id)); ?>,
                    <?php echo $customer['email']['value']; ?>,
                    <?php echo $customer['phone']['value']; ?>,
                    <?php echo $customer['address']['value']; ?>.
                    <?php /*?><br/><i>Тип оплаты:</i> <?php echo $item->payment; ?><?php */ ?>
            </td>

            <td class="sumprice"><?php echo $item->getTotalPrice(); ?> р.</td>
            <td><?php echo \YiiHelper::formatDate($item->create_time); ?></td>
            <td><div class="mark <?php echo !$item->completed ? 'marked' : 'unmarked'; ?>" data-item="<?php echo $item->id; ?>"></div></td>
            <?php /* ?><td><div class="mark_green <?php echo !$item->paid ? 'marked_green' : 'unmarked_green'; ?>" data-item="<?php echo $item->id; ?>"></div></td><?php */ ?>
            <td><?php echo \CHtml::link('Удалить', 'javascript:void()', array('class'=>'dorder-btn-delete', 'data-item'=>$item->id)); ?></td>
        </tr>
        <?php foreach ($item->getOrderData() as $hash=>$attributes): ?>
            <tr class="details dorder-list-item-details"" data-item="<?php echo $item->id; ?>">
                <td colspan="2">
                	<?php echo $attributes['title']['value']; ?><br />
                	<?php 
                	foreach($attributes as $attribute=>$data): 
                		if(!in_array($attribute, array('id', 'model', 'categoryId', 'price', 'count', 'title'))): ?>
	    				/ <small><b><?php echo $data['label']; ?>:</b> <?php echo $data['value']; ?></small>
	    			<?php endif; 
	    			endforeach; ?>
                </td>
                <td class="count"><?php echo $attributes['count']['value']; ?></td>
                <td class="sum"><?php echo $attributes['price']['value']; ?> р.</td>
                <td colspan="3"><?php echo $attributes['count']['value'] * $attributes['price']['value']; ?> р.</td>
            </tr>
        <?php endforeach; ?>
            <tr class="details dorder-list-item-comment" data-item="<?php echo $item->id; ?>">
                <td colspan="7"><textarea data-item="<?php echo $item->id; ?>" class="comment"><?php echo $item->comment ?: @$customer['comment']['value']; ?></textarea></td>
            </tr>
    <?php endforeach; ?>
</table>
<br />
<?php $this->widget('CLinkPager', array(
    'pages' => $pages,
)); ?>

