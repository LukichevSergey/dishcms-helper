<?php
/** @var \DCart\widgets\MiniCartWidget $this */
/** @var \DCart\components\DCart $cart */
?>
<div id="shop-cart" class="dcart-mini-cart">
    <div class="wrap">
        <div class="module<?php if ($cart->isEmpty()) echo ' empty'; ?>">
            <div class="module-main">
            	<div class="module-head">
                    <div class="summary">
                    	<div class="dcart-mini-cart-summary">
	                		<?php \DCart\widgets\MiniCartWidget::renderSummary(); ?>
	                	</div>
		            </div>
		        </div>

                <div class="module-content dcart-mini-cart-content">
                    <div id="cart-products">
                    	<div class="dcart-mini-cart-items">
	                         <?php \DCart\widgets\MiniCartWidget::renderItems(); ?>
	                    </div>
                    </div>

                    <p class="clear-cart">
                        <?php echo \CHtml::link('Очистить', array('dCart/clear'), array('class'=>'dcart-mini-cart-btn-clear')); ?>
                    </p>

                    <p class="goto-order">
                        <?php echo \CHtml::link('Перейти к оформлению', $this->orderUrl, array('class'=>'shop-button')) ?>
                    </p>
                    <p class="minimize"><a id="cart-minimize" class="link">Свернуть</a></p>
                </div>
            </div>
            <a class="cart-open-link"></a>
        </div>
    </div>
</div>