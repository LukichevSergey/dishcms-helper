<?php
/** @var \DOrder\widgets\actions\OrderWidget $this */
/** @var \DOrder\models\CustomerForm $customerForm */
?>
<?php if (Yii::app()->user->hasFlash('dorder')): ?>
	<div class="flash-success">
	    <?php echo Yii::app()->user->getFlash('dorder'); ?>
	</div>
<?php else: ?>
	<?php if(\Yii::app()->cart->isEmpty()):?>
		Ваша корзина пуста.
	<?php else: ?>
		<h1>Оформление заказа</h1>
	
		<?php $this->owner->widget('\DCart\widgets\CartWidget'); ?>
		<?php $this->owner->widget('\DOrder\widgets\CustomerFormWidget', array('model' => $customerForm)); ?>
	<?php endif; ?>
<?php endif; ?>