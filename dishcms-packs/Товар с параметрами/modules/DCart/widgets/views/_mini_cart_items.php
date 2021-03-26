<?php
/** @var \DCart\widgets\MiniCartWidget $this */
/** @var \DCart\components\DCart $cart */
?>
<?php if (!$cart->isEmpty()): ?>
    <ul class="dcart-mini-cart-list">
        <?php foreach($cart->getData() as $hash=>$data): ?>
        <?php $itemLink = Yii::app()->createUrl('shop/product', array('id'=>$data[$cart->attributeId])); ?>
        <li id="cart-product-<?php echo $data['id']; ?>" class="dcart-mini-cart-item" data-item="<?php echo $data['id']; ?>">
            <table>
            <tr>
                <td class="img dcart-mini-cart-image">
               		<?php echo \CHtml::link(CHtml::image($cart->getImage($hash)?:'http://placehold.it/36'), $itemLink); ?>
                </td>
                <td class="dcart-mini-cart-item-info">
                	<?php echo \CHtml::link($data['attributes'][$cart->attributeTitle], array('shop/product', 'id'=>$data[$cart->attributeId])) ?>
                    <p><small>
                    цена: <span class="price"><?php echo @$data['price']; ?></span> руб.
                    </small></p>
                    <?php 
					// вывод дополнительных аттрибутов
					foreach($cart->getAttributes(true, true) as $attribute):
						list($label, $value) = $cart->getAttributeValue($hash, $attribute, true);
						if(!is_null($value)): 
						?>
		            	<p><small>
		            		<?php if ($label) echo mb_strtolower($label) . ':'; ?>
		            		<i><?php echo $value; ?></i>
		            	</small></p>
		            	<?php endif;
		            endforeach; 
		            ?>
                </td>
                <td class="dcart-mini-cart-item-count">
                	шт.
                	<?php echo \CHtml::textField('count', $data['count'], array(
                		'data-item-hash' => $hash,
                		'size' => '2',
                		'maxlength' => '3'
                	)); ?>
                </td>
            </tr>
            </table>
        </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
