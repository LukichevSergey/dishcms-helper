<?php
/**
 * @use \YiiHelper (>=1.03)
 * 
 * @var \DOrder\widget\actions\OrderWidget $this
 * @var \DOrder\models\Order $model 
 */

// @var array $customer 
$customer = $model->getCustomerData();
?>
<p>Здравствуйте, <?php echo $customer['name']['value']; ?></p>
<p>Заказ <?php echo '№'. $model->id; ?></p>

<?php 
foreach($customer as $name=>$data): 
	if(in_array($name, array('email', 'name')) || empty($data['value'])) continue; 
	?>
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
	    		<?php $outAttributes = $this->mailAttributes ? \YiiHelper::arraySort($attributes, $this->adminMailAttributes) : $attributes; ?>
	    		<?php foreach($outAttributes as $attribute=>$data): ?>
	    			<?php if($data['value'] && !in_array($attribute, array('id', 'title'))): ?>
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
<p>В ближайшее время с Вами свяжется менеджер для уточнения заказа</p>