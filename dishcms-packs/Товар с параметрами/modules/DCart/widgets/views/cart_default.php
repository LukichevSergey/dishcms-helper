<?php
/** @var \DCart\widgets\CartWidget $this */
/** @var \DCart\components\DCart $cart */
?>
<?php if($cart->isEmpty()): ?>
	<p>Ваша корзина пуста</p>
<?php else: ?>
<table class="dcart-cart">
    <thead>
    <tr>
        <td width="1%"></td>
        <td>Название</td>
        <td width="15%">Кол-во</td>
        <td width="15%">Цена за шт.</td>
        <td width="1%"></td>
    </tr>
    </thead>
    <tbody class="dcart-cart-items">
		<?php $this->render('_cart_items', compact('cart')); ?>
    </tbody>
    <tfoot>
    <tr>
        <td colspan="3" style="text-align: right; font-weight: bold;">Итого:</td>
        <td colspan="2"><span class="dcart-cart-total-price"><?php echo $cart->getTotalPrice(); ?></span> руб</td>
    </tr>
    </tfoot>
</table>
<?php endif; ?>