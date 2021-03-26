<?php
/** @var \DOrder\widgets\actions\OrderWidget $this */
/** @var \DOrder\models\BaseForm $modelForm */
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
	
		<?php $this->owner->widget('\DCart\widgets\ModalCartWidget', array('hidePayButton'=>true)); ?>
		<?php // $this->owner->widget('\DOrder\widgets\CustomerFormWidget', array('model' => $modelForm)); ?>
		<?php $this->owner->widget('\DOrder\widgets\YandexFormWidget', array(
			'model' => $modelForm,
			'scid' => \Yii::app()->params['payment']['yandex']['scid'],
			'ShopID' => \Yii::app()->params['payment']['yandex']['ShopID'],
		)); ?>
	<?php endif; ?>
<?php endif; ?>