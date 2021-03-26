<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (!empty($arResult['ITEMS'])):
?>
<div class="search-products">
    <div id="search-products-list">
	<?
	foreach ($arResult['ITEMS'] as $key => $arItem):
		$arProps = $arItem['PROPERTIES'];
	?>
	<div class="products-list__item">
    	<div class="products-list__block_left">

        <div class="products-list__img">
			<? if(!empty($arProps['PROP_MARKER']['VALUE_XML_ID']) || !empty($arProps['PROP_NEW']['VALUE'])): ?>
    	    <div class="marker-list">
        	    <? if(in_array('HIT', $arProps['PROP_MARKER']['VALUE_XML_ID'])): ?>
	                <span class="marker marker-hit">Хит</span>
    	        <? endif; ?>
        	    <? if(in_array('SALE', $arProps['PROP_MARKER']['VALUE_XML_ID'])): ?>
            	    <span class="marker marker-sale">Sale</span>
	            <? endif; ?>
    	        <? if(!empty($arProps['PROP_NEW']['VALUE']) && ((int)\Kontur\Helper::GetDateDiff($arProps['PROP_NEW']['VALUE'], "now", "%a") < 30)): ?>
        	        <span class="marker marker-new">New</span>
	            <? endif; ?>
    	    </div>
        	<? endif ?>

			<a href="<?=$arItem['DETAIL_PAGE_URL']?>">
                <img src="<?=$arItem['PREVIEW_PICTURE']['SRC'] ?: 'http://placehold.it/90/ffffff/ffffff/?text=.'?>" alt="<?=$strTitle?>" title="<?=$strTitle?>" />
            </a>
        </div>

		<div class="products-list__name">
            <a href="<?=$arItem['DETAIL_PAGE_URL']?>"><?=$arItem['NAME']?></a>
        </div>

		<div class="products-list__desc">
            <?=$arItem['PREVIEW_TEXT']?>
        </div>

		</div>

		<div class="products-list__block_right<? if(!empty($arItem['PRICE_DISCOUNT'])) echo ' with-discount'; ?>">
			 <? $canBuy=!empty($arItem['PRICE']['PRICE']) || !empty($arItem['PRICE_DISCOUNT']); ?>
            <div class="products-list__price">
				<? if($canBuy): ?>
                <? if(!empty($arItem['PRICE_DISCOUNT'])): ?>
					<span class="price old-price"><?= $arItem['PRICE']['PRICE']; ?> <i>&#97;</i></span>
					<span class="price new-price"><?= $arItem['PRICE_DISCOUNT']; ?> <i>&#97;</i></span>
				<? else: ?>
					<span class="price"><?= $arItem['PRICE']['PRICE']; ?> <i>&#97;</i></span>
				<? endif; ?>
				<? else: ?>
					<span class="price">&nbsp;</span>
				<? endif; ?>
	       	</div>

            <div class="product-inner__control"<? if($canBuy) echo ' data-buy="'.$arItem['ID'].'"'; ?>>
                <? if($canBuy): ?>
                <div class="product-inner__control_count product-inner__control_item">
                    <div class="cart-count">
                        <span class="cart__count-down cart__count-btn icon">&#xe851;</span>
                        <input class="cart__count-input" data-id="<?=$arItem['ID']?>" data-max="<?=$arItem['QUANTITY']?>" type="text" value="1">
                        <span class="cart__count-up cart__count-btn icon">&#xe852;</span>
                    </div>
                </div>
                <div class="product-inner__control_button product-inner__control_item products-list__button">
                    <a href="javascript:;"
						data-url="<?=$arParams['BASKET_URL']?>"
						data-id="<?=$arItem['ID']?>"
						data-has-count="1" class="button default-button product-button add-cart"><i class="site-icon"></i><span><?=GetMessage('BTN_TO_CART')?></span></a>
                </div>
				<? else: ?>
					<div class="product-inner__control_count product-inner__control_item">&nbsp;</div>
					<div class="product-inner__control_button product-inner__control_item products-list__button">
						<a href="<?=$arItem['DETAIL_PAGE_URL']?>" class="button default-button product-button"><i class="site-icon"></i><span>Подробнее</span></a>
					</div>
                <? endif; ?>
            </div>
		</div>
	</div>	
	<? endforeach; ?>
	</div>
	<div><a href="/catalog/?q=<?=$_REQUEST['q']?>" class="button default-button search-list__link-all">Все результаты</a></div>
</div>
<? 
endif; 
?>
