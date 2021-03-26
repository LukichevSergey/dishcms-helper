<?php
/** @var ShopController $this */
/** @var array $products */

?>

<?php if (count($products)): ?>
<?php
/** @var integer $productPerRow */
$productPerRow = Yii::app()->params['product_per_row'] ?: (int)ModuleHelper::getParam('product_per_row', true);
if(!$productPerRow) $productPerRow = 3;  
?>
<table class="product-list clearfix" border="0" cellpadding="0" cellspacing="0">
	<tr>
    <?php foreach($products as $idx=>$product): ?>
        <td class="product<?php if ($product->sale) echo ' sale'; elseif ($product->new) echo ' new'; ?>">
        	<table height="100%" border="0" cellpadding="0" cellspacing="0" align="center">
        		<tr>
        			<td class="img">
			            <?php if($product->sale || $product->new): ?>
			            	<div class="<?php if ($product->sale) echo 'sale-img'; elseif ($product->new) echo 'new-img'; ?>"></div>
			            <?php endif; ?>
			            <?php echo CHtml::link(CHtml::image($product->mainImg), Yii::app()->createUrl('shop/product', array('id'=>$product->id))); ?>
        			</td>
        		</tr>
        		<tr>
        			<td class="title">
        				<?php echo CHtml::link($product->title, array('shop/product', 'id'=>$product->id)) ?>
        			</td>
        		</tr>
        		<tr>
        			<td class="price">
        				<?php echo $product->price; ?> руб.
        			</td>
        		</tr>
        		<tr>
        			<td class="to-cart-button">
        				 <?php if ($product->notexist): ?>
                		Нет в наличии
                		<?php else: ?>
                			<?php echo CHtml::link('<span>В корзину</span>', Yii::app()->createUrl('shop/addtocart', array('id'=>$product->id)), array('class'=>'shop-button to-cart')); ?>
                		<?php endif; ?>
        			</td>
        		</tr>
        	</table> 
        </td>
        <?php if (($idx+1) % $productPerRow == 0) echo '</tr><tr>'; ?>
    <?php endforeach; ?>
    <?php if(($idx+1) % $productPerRow !== 0) {
    	while($idx++ < count($products)) echo '<td class="product-empty">&nbsp;</td>';
    	echo '</tr>';
    } ?>
</table>

<?php if (isset($pages)): ?>
    <?php $this->widget('CLinkPager', array(
        'header'=>'Страницы: ',
        'pages'=>$pages,
        'nextPageLabel'=>'&gt;',
        'prevPageLabel'=>'&lt;',
        'cssFile'=>false,
        'htmlOptions'=>array('class'=>'news-pager')
    )); ?>
<?php endif; ?>

<?php else: ?>
<p>Нет товаров</p>
<?php endif;?>
