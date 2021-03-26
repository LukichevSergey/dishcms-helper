добавить в самый конец файла

if (!empty($arResult['ITEMS'])){
	$arOfferPrices=[];
	$arOfferIDs=[];
	foreach ($arResult['ITEMS'] as $key => $arItem) {
		if(!empty($arItem['OFFERS'])) {
			$propIDs=[];
			if(!empty($arItem['OFFERS_PROPS_JS'])) {
				foreach($arItem['OFFERS_PROPS_JS'] as $propCode=>$prop) {
					$propIDs[]=$prop['ID'];
				}
			}
			foreach($arResult['ITEMS'][$key]['OFFERS'] as $arOffer) {
				$valIDs=[];
				foreach($propIDs as $propID) {
					if(!empty($arOffer['TREE']['PROP_' . $propID])) {
						$valIDs[]=$arOffer['TREE']['PROP_' . $propID];
					}
				}
				if(!empty($valIDs) && !empty($arOffer['MIN_PRICE']['VALUE'])) {
					foreach($valIDs as $valID) {
						$arOfferPrices[$key][$valID]=(float)$arOffer['MIN_PRICE']['VALUE'];
						$arOfferIDs[$key][$valID]=$arOffer['ID'];
					}
				}
			}
		}
	}
	foreach ($arResult['ITEMS'] as $key => $arItem) {
		if(!empty($arItem['OFFERS_PROPS_JS'])) {
			foreach($arItem['OFFERS_PROPS_JS'] as $propCode=>$prop) {
				if(!empty($prop['VALUES'])) {
					usort($arResult['ITEMS'][$key]['OFFERS_PROPS_JS'][$propCode]['VALUES'], function($a, $b) use ($key, $arOfferPrices) {
						if(empty($arOfferPrices[$key][$a['ID']])) return -1;
		                if(empty($arOfferPrices[$key][$b['ID']])) return -1;
		                if($arOfferPrices[$key][$a['ID']] < $arOfferPrices[$key][$b['ID']]) return -1;
		                if($arOfferPrices[$key][$a['ID']] > $arOfferPrices[$key][$b['ID']]) return 1;
		                return 0;
					});
				}
			}
			
			$jsOffers=[];
			foreach($arResult['ITEMS'][$key]['OFFERS_PROPS_JS'][$propCode]['VALUES'] as $val) {
				foreach($arItem['JS_OFFERS'] as $arJsOffer) {
					if($arJsOffer['ID'] ==  $arOfferIDs[$key][$val['ID']]) {
						$jsOffers[]=$arJsOffer;
						break;
					}
				}
			}
			$arResult['ITEMS'][$key]['JS_OFFERS']=$jsOffers;
			$arResult['ITEMS'][$key]['OFFERS_SELECTED']=0;
		}
    }
}
