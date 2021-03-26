// обновление базовой цены из свойства ЦЕНА
/**/
if(isset($_GET['updateprice']) && ($_GET['updateprice']=='run')) {
	$IBLOCK_ID = 12;
	$PRICE_PROPERTY_NAME = "PRICE";

    CModule::IncludeModule('iblock');
    CModule::IncludeModule('catalog');

    $rsElements = CIBlockElement::GetList(array(), array('IBLOCK_ID'=>$IBLOCK_ID), false, false, array('ID', 'PROPERTY_'.$PRICE_PROPERTY_NAME));
    while($arElement = $rsElements->GetNext()) { $i++;
        $ID = $arElement['ID'];
        $PRICE = (float)preg_replace('/[^0-9.]/', '', str_replace(',', '.', $arElement['PROPERTY_'.$PRICE_PROPERTY_NAME.'_VALUE'])) ?: 0;

        \CPrice::SetBasePrice($ID, $PRICE, 'RUB');

        CCatalogProduct::Add(array(
            'ID'=>$ID,
            'TYPE'=>\Bitrix\Catalog\ProductTable::TYPE_PRODUCT,
            'AVAILABLE'=>'Y',
            'CAN_BUY_ZERO'=>'D',
            'PURCHASING_PRICE'=>$PRICE,
            'PURCHASING_CURRENCY'=>'RUB',
            'PRICE_TYPE'=>'S',
            'AVAILABLE'=>'Y',
			'QUANTITY'=>1
        ));

        $arPriceFields = array(
            'PRODUCT_ID'=>$ID,
            'CATALOG_GROUP_ID'=>1,
            'PRICE'=>(string)$PRICE,
            'CURRENCY'=>'RUB',
            'QUANTITY_FROM'=>false,
            'QUANTITY_TO'=>false
        ));

        $rsPrice = CPrice::GetList(array(), array('CATALOG_GROUP_ID'=>1, 'PRODUCT_ID'=>$ID));
        $arPrice = $rsPrice->Fetch();
        if(!empty($arPrice)) { 
            CPrice::Update($arPrice['ID'], $arPriceFields);
        }
        else {
            CPrice::Add($arPriceFields);
        }

        CIBlockElement::UpdateSearch($ID, true);
    }
}
/**/

