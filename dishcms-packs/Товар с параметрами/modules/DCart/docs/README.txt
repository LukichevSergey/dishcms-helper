Использование модуля DCart
--------------------------
1. Виджет мини-корзины:
<?php $this->widget('\DCart\widgets\MiniCartWidget'); ?>

2. Стандартный виджет корзины:
<?php $this->widget('\DCart\widgets\СartWidget'); ?>

3. Виджет кнопки добавления в корзину
Простое использование:
Пример 1:
<?php $this->widget('\DCart\widgets\AddToCartButtonWidget', array(
	'id' => $model->id,
	'model' => $model
)); ?>

Пример 2:
<?php $this->widget('\DCart\widgets\AddToCartButtonWidget', array(
	'id' => $product->id,
	'model' => $product,
	'title'=>'<span>В корзину</span>', 
	'cssClass'=>'shop-button to-cart')); 
?>

Пример разширенного использования, для товаров с дополнительными параметрами:
<?php $this->widget('\DCart\widgets\AddToCartButtonWidget', array(
	'id' => $product->id,
	'model' => $product,
	'title' => '<span>Купить</span>', 
	'cssClass' => 'shop-button to-cart',
	'attributes' => array(
		array($product->attributeColor->attributeOne, $product, $product->attributeColor->attribute),
		array($product->attributeTSize->attributeOne, $product, $product->attributeTSize->attribute),
	)
)); ?>
