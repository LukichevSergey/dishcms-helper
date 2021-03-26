<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?php if(!empty($arResult['GROUPS'])): ?>
<?php
$fGetPrice=function($el) {
    if(!empty($el['PROPERTIES']['PRICE']['VALUE'])) return (int)$el['PROPERTIES']['PRICE']['VALUE'];
    return 0;
};
$fGetPriceFormatted=function($el, $notEmpty=false) {
    if(!empty($el['PROPERTIES']['PRICE']['VALUE'])) $price='+ ' . number_format($el['PROPERTIES']['PRICE']['VALUE'], 0, '.', ' ') . ' руб.';
    elseif(!empty($el['PROPERTIES']['PRICE_TEXT']['VALUE'])) $price=$el['PROPERTIES']['PRICE_TEXT']['VALUE'];
    elseif($notEmpty) $price=null;
	else $price='+ 0 руб.';
    return $price;
};
$fGetImageSrc=function($el) {
    if(!empty($el['PREVIEW_PICTURE'])) {
        $file=CFile::ResizeImageGet($el['PREVIEW_PICTURE'], array("width"=>170, "height"=>170), BX_RESIZE_IMAGE_EXACT);
        if(!empty($file['src'])) return $file['src'];
    }
    return '';
};
?>
<div class="options-container js-options-container">
	<h2>Дополнительные опции</h2>
	<div class="options__rows-wrap">
		<?php foreach($arResult['GROUPS'] as $groupId=>$group): ?>
			<?php if(!empty($group['ELEMENTS']) || !empty($group['SUBGROUPS'])): ?>
			<div class="options__row">
				<?php if(!empty($group['ELEMENTS'])): ?>
				<div class="options-group options__group">
					<span class="options-group__title"><?= $group['NAME']; ?></span>
					<div class="options-group__box">
						<?php $type=empty($group['UF_MULTIPLE']) ? 'radio' : 'checkbox'; ?>
						<?php foreach($group['ELEMENTS'] as $elementId=>$element): ?>
    						<div class="options-item options-group__item">
    							<input type="<?= $type; ?>" data-price="<?= $fGetPrice($element); ?>" name="options-diameter" id="options-<?= $elementId; ?>" class="options-item__input options-group__input js-options-input">
    							<label for="options-<?= $elementId; ?>" class="options-item__label" style="background-image: url(<?= $fGetImageSrc($element); ?>);">
    							</label>
    							<span class="options-item__title"><?= $element['NAME']; ?></span>
    							<span class="options-item__price options-group__price"><?= $fGetPriceFormatted($element); ?></span>
    						</div>
						<?php endforeach; ?>    						
					</div>
				</div>
				<?php endif; ?>
				<?php if(!empty($group['SUBGROUPS'])): ?>
					<?php $nrow=0; foreach($group['SUBGROUPS'] as $subgroupId=>$subgroup): ?>
						<?php if(!empty($subgroup['ELEMENTS'])): ?>
    						<?php $nelm=0; $type=empty($subgroup['UF_MULTIPLE']) ? 'radio' : 'checkbox'; ?>
							<?php foreach($subgroup['ELEMENTS'] as $elementId=>$element): ?>
    							<?php if(!($nelm % 5)): ?>
                				<div class="options-group options-group_has-common-price options__group">
                					<?php if(!$nrow): $nrow++; ?><span class="options-group__title"><?= empty($group['ELEMENTS']) ? $group['NAME'] : '&nbsp;'; ?></span><?php endif; ?>
                					<div class="options-group__box">
                				<?php endif; ?>
        							<div class="options-item options-group__item">
            							<input type="<?= $type; ?>" data-price="<?= $fGetPrice($element); ?>" name="options-roof" id="options-<?= $elementId; ?>" class="options-item__input js-options-input">
            							<label for="options-<?= $elementId; ?>" class="options-item__label" style="background-image: url(<?= $fGetImageSrc($element); ?>);">
            							</label>
            							<span class="options-item__title"><?= $element['NAME']; ?></span>
            							<span class="options-item__price options-group__price"><?= $fGetPriceFormatted($element, true) ?: $subgroup['NAME']; ?></span>
            						</div>
                				<?php if(!(($nelm + 1) % 5)): ?>
            						</div>
                					<div class="options-group__common-price-wrap">
                						<span class="options-group__common-price"><?= $subgroup['NAME']; ?></span>
                					</div>
        						</div>
            					<?php endif; ?>
            					<?php $nelm++; ?>
    						<?php endforeach; ?>
    						<?php if(count($subgroup['ELEMENTS']) % 5): ?>
    							</div>
                					<div class="options-group__common-price-wrap">
                						<span class="options-group__common-price"><?= $subgroup['NAME']; ?></span>
                					</div>
        						</div>
    						<?php endif; ?>            						
    					<?php endif; ?>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>
			<?php endif; ?>
		<?php endforeach; ?>
    </div>	
  	<div class="options__footer">
			<div class="options__total-prices-wrap">
				<span class="options__total-price">БАЗОВАЯ СТОИМОСТЬ: <span class="js-total-price-base" data-price="<?= $arResult['ELEMENT']['FILTER_PRICE']; ?>"><?= $arResult['ELEMENT']['FILTER_PRICE']; ?></span> руб.</span>
				<span class="options__total-price">СТОИМОСТЬ ОПЦИЙ: <span class="js-total-price-options">0</span> руб.</span>
				<span class="options__total-price">ИТОГОВАЯ ЦЕНА: <span class="js-total-price"><?= $arResult['ELEMENT']['FILTER_PRICE']; ?></span> руб.</span>
			</div>
			<span class="options__submit js-options__submit" data-event="jqm" data-param-id="<?=CCache::$arIBlocks[SITE_ID]['aspro_stroy_form']['aspro_stroy_form_order_options'][0]?>" data-name="order_options" data-product="<?=$arResult['ELEMENT']['NAME']?>" data-options="">Оформить заказ</span>
		</div>
	</form>
</div>
<?php endif; ?>
