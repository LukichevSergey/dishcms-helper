if(!!empty($arResult['OFFERS']) && !empty($arResult['TMP_OFFERS_PROP'])) {
	$arOfferPrices=[];
	$arOfferKeys=[];
	foreach($arResult['OFFERS'] as $key=>$arOffer) {
		$arOfferPrices[$arOffer['ID']]=(float)$arOffer['MIN_PRICE']['VALUE'];
		$arOfferKeys[$arOffer['ID']]=$key;
	}
	asort($arOfferPrices);
	
	/////////////////////////////
	
	$propValues=[];
	foreach($arResult['TMP_OFFERS_PROP'] as $propCode=>$prop) {
		$propValues[$propCode]=['ID'=>$prop['ID'], 'VALUES'=>[]];
		if(!empty($prop['VALUES'])) {
			foreach($prop['VALUES'] as $propValID=>$val) {
				$propValues[$propCode]['VALUES'][]=$propValID;
			}
		}
	}
	
	
	$arJsOfferKeys=[];
	foreach($arResult['JS_OFFERS'] as $key=>$arJsOffer) {
		'TREE'['PROP_' . $propID]=$valID
		$arJsOfferKeys[$arJsOffer['ID']]=$key;
	}
	
	$sort=100;
	$arOffers=[];
	foreach($arOfferPrices as $offerId=>$offerPrice) {
		$arResult['OFFERS'][$arOfferKeys[$offerId]]['SKU_SORT_WEIGHT']=$sort;
		$arOffers[]=$arResult['OFFERS'][$arOfferKeys[$offerId]];
		$sort+=100;
	}
	$arResult['OFFERS']=$arOffers;
	
	$sort=100; // TMP_OFFERS_PROP (PROP_CODE=>(ID, VALUES=>()))
	$arJsOffers=[];
	foreach($arOfferPrices as $offerId=>$offerPrice) {
		$arResult['JS_OFFERS'][$arOfferKeys[$offerId]]['SKU_SORT_WEIGHT']=$sort;
		$arJsOffers[]=$arResult['JS_OFFERS'][$arJsOfferKeys[$offerId]];
		$sort+=100;
	}
	$arResult['JS_OFFERS']=$arJsOffers;
	$arResult['OFFERS_SELECTED']=0;
}