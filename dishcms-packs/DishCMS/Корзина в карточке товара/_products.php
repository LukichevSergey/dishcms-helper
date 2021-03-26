в  _products_listview.php добавить

'afterAjaxUpdate'=>'function(){productUpdateCounts();}',

-------------------------------------------------------

<div class="product-item col-sm-6 col-md-4">
...
			<div class="product__to-cart">
				<?if($data->notexist):?>
				Нет в наличии
				<?else:?>
				<div class="cart">
					<a href="#!" class="cart__minus" data-id="<?=$data->id?>">-</a>
					<a class="cart__image-link" href="/cart">
	                    <img src="/images/cart.png" alt="" class="cart__image">
	                    <span class="cart__count" data-id="<?=$data->id?>">0</span>
	                </a>
	                <a href="#!" class="cart__plus" data-id="<?=$data->id?>">+</a>
				</div>
				<?endif;?>
			</div>
...
</div>
