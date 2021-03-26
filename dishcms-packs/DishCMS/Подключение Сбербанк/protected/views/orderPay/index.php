<?php

$modelProduct = Product::model();

?>



<h1><?= $title ?></h1>



<p style="color: red;"><?= $error ?></p>



<h2>Состав заказа</h2>



<table class="pay-order-table" border="1">

    <thead>

    	<tr>

    	    <th>Наименование</th>

    	    <th>Кол-во</th>

    	    <th>Сумма</th>

    	</tr>

    </thead>



    <tbody>

	    <?php foreach ($order->getOrderData() as $hash=>$attributes): ?>

	        <tr>

	            <td><?php

	            	$productId = $attributes['id']['value'];

	            	$modelProduct->id = $productId;

	            	$itemLink = Yii::app()->createUrl('shop/product', array('id'=>$productId));

					echo \CHtml::link(CHtml::image($modelProduct->getMainImg()?:'http://placehold.it/36'), $itemLink, array('target'=>'_blank', 'class'=>'image'))

					?>

	            	<?=$attributes['title']['value']?><br />

	            	<?foreach($attributes as $attribute=>$data): 

	            		if($data['value'] && !in_array($attribute, array('id', 'model', 'categoryId', 'price', 'count', 'title'))):?>

	    				/ <small><b><?=$data['label']?>:</b> <?=$data['value']?></small>

	    				<?endif; 

	    			endforeach; ?>

	            </td>

	            <td><?=$attributes['count']['value']?></td>

	            <td><?= $order->priceWithSale($attributes['count']['value'] * $attributes['price']['value']) ?> руб.</td>

	        </tr>

	    <?endforeach?>

    </tbody>

</table>



<div class="pay-order-total">

	<b>Итого: <?= $order->getPayTotalPrice() ?> руб.</b>

</div>



<div class="pay-order-buttons">

	<form action="" method="post">

		<input type="submit" name="pay" value="Оплатить" class="pay-order-button">

	</form>

</div>



<div class="pay-order-info">

	<?= D::cms('pay_desc') ?>

</div>


