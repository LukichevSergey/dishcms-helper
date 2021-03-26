<?php
/** @var \DOrder\widget\admin\OrderButtonWidget $this */
/** @var \DOrder\models\Order $model */
?>
<a class="<?php echo ($this->owner->id == 'dOrder') ? 'order-button-active' : 'default-button'; ?> left" href="/cp/dOrder">Заказы
	<div class="notify notifybutton dorder-order-button-widget-count">
    	<?php echo (int)$model->uncompleted()->count(); ?>
    </div>
</a>
