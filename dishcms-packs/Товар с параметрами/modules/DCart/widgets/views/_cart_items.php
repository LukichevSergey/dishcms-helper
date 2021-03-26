<?php
/** @var \DCart\widgets\CartWidget $this */
/** @var \DCart\components\DCart $cart */
?>
    <?php foreach($cart->getData() as $hash=>$data): ?>
    <tr class="dcart-cart-item" data-item-hash="<?php echo $hash; ?>">
        <td class="img">
            <img src="<?php //echo $p->obj->tmbImg; ?>" alt="" />
        </td>
        <td class="dcart-cart-info">
            <?php echo \CHtml::link($data['attributes'][$cart->attributeTitle], array('shop/product', 'id'=>$data[$cart->attributeId])) ?>
            <?php 
			// вывод дополнительных аттрибутов
			foreach($cart->getAttributes(true, false, true) as $attribute):
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
        <td class="dcart-cart-count">
        	<?php echo \CHtml::textField('count', $data['count'], array('data-item-hash' => $hash, 'size'=>7)); ?>
        </td>
        <td class="dcart-cart-price"><?php echo $data['price']; ?> руб</td>
        <td class="dcart-cart-remove">
        	<?php echo \CHtml::link('Удалить', $this->owner->createUrl('dCart/delete'), array(
        		'class'=>'dcart-cart-btn-remove', 
        		'data-item-hash' => $hash
        	)); ?>
        </td>
    </tr>
    <?php endforeach; ?>