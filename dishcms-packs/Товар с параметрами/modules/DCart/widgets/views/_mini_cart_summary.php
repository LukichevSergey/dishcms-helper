<?php
/** @var \DCart\widgets\MiniCartWidget $this */
/** @var \DCart\components\DCart $cart */
?>
<?php if (!$cart->isEmpty()): ?>
    В <?php echo \CHtml::link('корзине', array('shop/order'), array('id'=>'open-cart')); ?>
    <strong id="summary-count"><?php echo $cart->getTotalCount(); ?></strong> товаров
    на <strong id="summary-price"><?php echo $cart->getTotalPrice(); ?></strong> руб.
<?php else : ?>
    Ваша корзина пуста
<?php endif; ?>
