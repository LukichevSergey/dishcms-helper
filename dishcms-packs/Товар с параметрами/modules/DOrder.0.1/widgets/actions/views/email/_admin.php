<?php
/**
 * @use \YiiHelper (>=1.03)
 *
 * @var \DOrder\widget\actions\OrderWidget $this 
 * @var \DOrder\models\Order $model 
 */
?>
<h3>Новый заказ с сайта <?php \Yii::app()->name ?></h3>

<?php foreach($model->getCustomerData() as $name=>$data): ?>
<p>
    <strong><?php echo ($name == 'name') ? 'Имя' : $data['label']; ?></strong>:<br />
    <?php echo ($name == 'create_time') ? \YiiHelper::formatDate($model->create_time) : $data['value']; ?>
</p>
<?php endforeach; ?>

<?php
// @var integer $total Итоговая сумма заказа
$total = 0;  
?>
<h4>Товары</h4>
<ol>
    <?php 
    foreach($model->getOrderData() as $hash=>$attributes): 
	    $price = $attributes['price']['value'];
	    $count = $attributes['count']['value'];
	    $total += $price*$count;
	    ?>
	    <li>
	    	<strong><?php echo $attributes['title']['value']; ?></strong>
	    	<ul style="list-style: none">
	    		<?php $outAttributes = $this->adminMailAttributes ? \YiiHelper::arraySort($attributes, $this->adminMailAttributes) : $attributes; ?>
	    		<?php foreach($outAttributes as $attribute=>$data): ?>
	    			<?php if(!in_array($attribute, array('id', 'model', 'title', 'categoryId'))): ?>
	    			<li>
	    				<?php echo $data['label']; ?>: <?php echo $data['value']; ?>
	    				<?php if($attribute == 'price') echo ' руб.';?>
	    				<?php if($attribute == 'count') echo ' шт.';?>
	    			</li>
	    			<?php endif; ?>
	    		<?php endforeach; ?>
	    		<li>
	    			<strong>Итого</strong>: <?php printf('(%d руб) x %d шт = %d руб', $price, $count, $price*$count); ?>
	    		</li>
	    	</ul>
	    </li>
    <?php endforeach; ?>
</ol>

<h4>Итого: <?php echo $total; ?> руб.</h4>