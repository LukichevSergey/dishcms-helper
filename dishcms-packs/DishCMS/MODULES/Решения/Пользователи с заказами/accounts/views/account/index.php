<?php
/** @var \accounts\controllers\AccountController $this */
/** @var \crud\models\ar\accounts\models\Account $account */
/** @var \crud\models\ar\accounts\models\Account $accountChangePassword */
use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HTools;
use accounts\components\helpers\HAccount;
use crud\models\ar\accounts\models\Coupon;
use common\components\helpers\HHtml;

Y::jsCore('jquery.ui');
Y::jsCore('maskedinput');
Y::module('common')->publishJs('js/tools/activeform.js');
//Y::module('accounts')->publishJs('controllers/account/scripts.js');
//Y::module('accounts')->publishLess('controllers/account/styles.less');

// $accountBankInforation=$account->getBankInformation();
Y::js('accounts-index', '$(document).on("click", ".js-btn-account-reorder", function(e){
    let btn=$(e.target).closest(".js-btn-account-reorder"),msg="";
    $.post("/accounts/account/reorder",{id:btn.data("item")},function(r){
        if(r.success){$(".dcart-total-count").text(r.data.cart);}
        if(r.success && !isNaN(r.data.count) && (r.data.count > 0)) {msg="<p>Доступные товары из заказа добавлены в корзину.</p>";}
        else {msg="<p>Товары из заказа, в данный момент, не доступны к покупке.</p>";}
        $.fancybox.getInstance("close");$.fancybox.open(\'<div class="fancybox-content"><div class="reg-form-box"><h3>Повторение заказа</h3>\'+msg+\'</div></div>\');
        setTimeout(function(){$.fancybox.getInstance("close");},1500);
    },"json");
});', \CClientScript::POS_READY);
?>
<h1 class="under-switch-bar-title">С возвращением, <?= $account->name; ?> <span class="logout-link">(<?= \CHtml::link('Выйти', '/accounts/auth/logout'); ?>)</span></h1>

<?php $this->widget('\accounts\widgets\FlashMessage'); ?>

<div class="switsh-bar">
    <a href="javascript:;" id="my-data" class="active-switch">
        Ваши данные
        <span class="selected"></span>
    </a>
    
    <a href="javascript:;" id="buy-history">
        История покупок
        <span class="selected"></span>
    </a>
    
    <a href="javascript:;" id="liked-goods">
        Избранные товары
        <span class="selected"></span>
    </a>
</div>
<span class="switch-line"></span>

<div class="data-box">
    <div class="reg-form-box">
        <?php $this->widget('\common\widgets\form\ActiveForm', [
    	    'id'=>'accounts__edit-profile-form',
    	    'model'=>$account,
            'attributes'=>['name', 'lastname', 'email', 'phone'],
    	    'tag'=>false,
    	    'errorSummary'=>false,
    	    'formOptions'=>[
    	        'enableAjaxValidation'=>true,
    	        'enableClientValidation'=>false,
    	        'clientOptions'=>[
    	            'hideErrorMessage'=>false,
    	            'validationUrl'=>'/accounts/account/edit',
    	        ],
    	        'htmlOptions'=>['class'=>'reg-form data-form']
    	    ],
            'htmlOptions'=>[
                'rowTag'=>'div',
                'rowOptions'=>['class'=>'feild-box']
            ],
    	    'types'=>[
    	        'phone'=>'phone',    	        
    	    ],
    	    'submitLabel'=>function() {
    	        ?>
    	        <div class="send-btn-3">
                	<span class="dot-left"></span>
            		<input type="submit" value="Сохранить изменения">
            		<span class="dot-right"></span>
            	</div>
    	        <?php 
    	    },
    	]); ?>
    </div>

    <h1>Изменить пароль</h1>

    <div class="reg-form-box">
        <?php $this->widget('\common\widgets\form\ActiveForm', [
    	    'id'=>'accounts__change-password-form',
    	    'model'=>$accountChangePassword,
            'attributes'=>['password', 'repassword'],
    	    'tag'=>false,
    	    'errorSummary'=>false,
    	    'formOptions'=>[
    	        'enableAjaxValidation'=>true,
    	        'enableClientValidation'=>false,
    	        'clientOptions'=>[
    	            'hideErrorMessage'=>false,
    	            'validationUrl'=>'/accounts/account/edit',
    	        ],
    	        'htmlOptions'=>['class'=>'reg-form data-form']
    	    ],
            'htmlOptions'=>[
                'rowTag'=>'div',
                'rowOptions'=>['class'=>'feild-box']
            ],
    	    'types'=>[
    	        'password'=>'passwordField',    	        
    	        'repassword'=>'passwordField',    	        
    	    ],
    	    'submitLabel'=>function() {
    	        ?>
    	        <div class="send-btn-3">
                	<span class="dot-left"></span>
            		<input type="submit" value="Сохранить изменения">
            		<span class="dot-right"></span>
            	</div>
    	        <?php 
    	    },
    	]); ?>
    </div>
</div>

<div class="buy-history-box" style="display: none;">
	<?php if($orders=HAccount::getOrders()): ?>
    <ul class="main-list">
    	<?php foreach($orders as $order): ?>
        <li>
            <div class="order">
                <div class="order-content">
                    <div class="order-num">
                        <h4 class="order-default-title">Заказ</h4>
                        <a href="javascript:;" class="order-link">№<?= $order->id; ?></a>
                    </div>
                    <div class="order-time">
                        <h4 class="order-default-title">Дата покупки</h4>
                        <span class="order-time-date"><?= Y::formatDate($order->create_time, 'dd.MM.yyyy'); ?></span>
                    </div>
                    <div class="order-sum">
                        <h4 class="order-default-title">Сумма</h4>
                        <span class="order-sum-cost"><?= HHtml::price($order->getTotalPrice()); ?> руб.</span>
                    </div>
                </div>
                <div class="send-btn-3">
                    <span class="dot-left2"></span>
                    <input type="submit" class="js-btn-account-reorder" data-item="<?= $order->id; ?>" value="Повторить заказ">
                    <span class="dot-right2"></span>
                </div>
            </div>
            <ul class="sub-list" style="display: none;">
            	<?php 
            	$orderItems=$order->getOrderData(); 
            	foreach($orderItems as $item):
            	?>
                <li>
                    <div class="apeared">
                        <div class="apeared-titles">
                            <h4 class="title-good order-default-title">Товар</h4>
                            <h4 class="title-amount order-default-title">Кол-во</h4>
                            <h4 class="title-price order-default-title">Цена</h4>
                            <h4 class="title-sum order-default-title">Сумма</h4>
                        </div>
                        <div class="goods">
                            <div class="good-img">
                            	<?php if($item['image']['value']): ?>
                                	<img src="<?= $item['image']['value']; ?>" alt="" />
                                <?php else: ?>
                                	&nbsp;
                                <?php endif; ?>
                            </div>
                            <div class="good-description">
                                <span><?= $item['title']['value']; ?></span>
                            </div>
                            <div class="good-amount">
                                <span><?= (int)$item['count']['value']; ?></span>
                            </div>
                            <div class="good-price">
                                <span><?= HHtml::price((float)$item['price']['value']); ?> руб.</span>
                            </div>
                            <div class="good-sum">
                                <span><?= HHtml::price((int)$item['count']['value'] * (float)$item['price']['value']); ?> руб.</span>
                            </div>
                        </div>
                    </div>
                </li>
        		<?php endforeach; ?>
                <div class="sum-to-buy">
                    <span>Общая сумма заказа:</span>
                    <span class="sum-to-buy-mark"><?= HHtml::price($order->getTotalPrice()) ?> руб.</span>
                </div>
            </ul>
        </li>
        <?php endforeach; ?>
    </ul>
    <?php else: ?>
    	<p>Вы еще не оформили ни одного заказа.</p>
        <p><?= \CHtml::link('Перейти в каталог', '/catalog'); ?></p>
    <?php endif; ?>
</div>

<div class="liked-goods-box" style="display: none;">
	<?php if($favoriteProducts=HAccount::getFavoriteProducts(['scopes'=>'cardColumns'])): ?>
    <div class="product-list row">
    	<?php foreach($favoriteProducts as $favoriteProduct): ?>
        <div class="product-item col-sm-6 col-md-4">
            <div class="product">
            	<span class="heart js-set-favorite-product<?php if(HAccount::isFavoriteProduct($favoriteProduct->id)) echo ' heart-pushed'; ?>" data-item="<?= $favoriteProduct->id; ?>"></span>
                <div class="product__image product-block">
                	<?=CHtml::link(CHtml::image(ResizeHelper::resize($favoriteProduct->getSrc(), 660, 480)), ['/shop/product', 'id'=>$favoriteProduct->id]); ?>
                </div>
                <div class="product__title product-block">
                	<?=CHtml::link($favoriteProduct->title, ['/shop/product', 'id'=>$favoriteProduct->id], array('title'=>$favoriteProduct->link_title)); ?>
                </div>
                <div class="product__footer">
                    <div class="product__price">
                        <p class="order__price">
                            <span class="new_price"><?= HtmlHelper::priceFormat($favoriteProduct->price); ?><span class="rub"><i class="fas fa-ruble-sign"></i></span>
                            </span>
                        </p>
                    </div>
                    <div class="product__to-cart">
                    <?if($favoriteProduct->notexist):?>
    					Нет в наличии
    				<?else:?>
    					<?$this->widget('\DCart\widgets\AddToCartButtonWidget', array(
    					    'id' => $favoriteProduct->id,
    					    'model' => $favoriteProduct,
    						'title'=>'<span><i class="fas fa-shopping-basket"></i> В корзину</span>',
    						'cssClass'=>'btn btn_cart hop-button to-cart button_1 js__in-cart open-cart',
    						'attributes'=>[
    							// ['count', '#js-product-count-' . $data->id],
    						]
    					));
    					?>
    				<?endif?>                        
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
        <p>У Вас не добавлено еще ни одного избранного товара.</p>
        <p><?= \CHtml::link('Перейти в каталог', '/catalog'); ?></p>
    <?php endif; ?>
</div>