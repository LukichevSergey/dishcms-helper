<?php
/**
 * Минимальная цена со скидкой
 *
 * пример {=actual_price} будет получена цена для группы текущего пользователя 
 * из под которого будет сгенерирован кэш мета-тэга, 
 * или {=actual_price 2}, где "2" - это группа пользователей, для которой будет получена цена.
 */
\kontur\seo\SeoFunction::register('actual_price', array(
	'add_entity' => true,
	'calculate' => function($parameters, $result) {
		if ( empty($result) ) {
			return null;
		}
		
		// \Bitrix\Main\Loader::includeModule('iblock');		
		// \Bitrix\Main\Loader::includeModule('catalog');
		// \Bitrix\Main\Loader::includeModule('sale');
		
		$event = array_shift($result);
		$userGroup = (int)array_shift($result);
		if ( $userGroup ) {
			$userGroups = array( $userGroup );
		}
		else {
			global $USER;
			$userGroups = $USER->GetUserGroupArray();
		}
	
		$price = 0;
		if ( $ID = $event->getField('ID') ) {
			$IBLOCK_ID = $event->getField('IBLOCK_ID');
			$arOffers = \CCatalogSKU::getOffersList($ID, $IBLOCK_ID);
			if (!empty($arOffers[$ID])) {
				$minPrice = 0;
				foreach($arOffers[$ID] as $offerID=>$arOffer) {
					$prices = \CCatalogProduct::GetOptimalPrice($offerID, 1, $userGroups, 'N');
					if(isset($prices['RESULT_PRICE']['DISCOUNT_PRICE'])) {
						if ( !$price || ($prices['RESULT_PRICE']['DISCOUNT_PRICE'] < $price)) {
							$price = $prices['RESULT_PRICE']['DISCOUNT_PRICE'];
						}
					}
				}
			}
			if( !$price ) {
				$prices = \CCatalogProduct::GetOptimalPrice($ID, 1, $userGroups, 'N');
				if(isset($prices['RESULT_PRICE']['DISCOUNT_PRICE'])) {
					$price = $prices['RESULT_PRICE']['DISCOUNT_PRICE'];
				}
			}
		}
		return (float)$price;
	}
));

