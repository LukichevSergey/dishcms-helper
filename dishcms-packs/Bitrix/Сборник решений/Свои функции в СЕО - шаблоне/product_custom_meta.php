header.php
<title><? 
    	//$APPLICATION->ShowTitle();
    	echo $APPLICATION->AddBufferContent('konturMetaTitle');
    ?></title>
    <? // $APPLICATION->ShowHead(); ?>
    <?$APPLICATION->ShowMeta('robots')?>
	<?$APPLICATION->ShowCSS()?>
	<?$APPLICATION->ShowHeadStrings()?>
	<?$APPLICATION->ShowHeadScripts()?>
	<?//$APPLICATION->ShowMeta('description')?>
	<?= $APPLICATION->AddBufferContent('konturMeta', 'description');?>

init.php
function konturMetaTitle()
{
	global $APPLICATION;
	$title = $APPLICATION->GetPageProperty('title_custom');
	if ( !$title ) {
		$title = $APPLICATION->GetPageProperty('title');
	}
	return $title;
}

function konturMeta($name)
{
	global $APPLICATION;
	$value = $APPLICATION->GetPageProperty($name.'_custom');
	if ( !$value ) {
		$value = $APPLICATION->GetPageProperty($name);
	}
    return '<meta name="'.$name.'" content="'. htmlspecialcharsEx($value) . '" />';
}

catalog.element component_epilog.php
// get meta tags
$price = '';
$productColor = null;
$currentCode = preg_replace('#^.*/(.*?)/$#', '\\1', $APPLICATION->GetCurPage());
$arOffers = \CCatalogSKU::getOffersList($arResult['ID'], $arResult['IBLOCK_ID'], array(), array('CODE', 'PROPERTY_COLOR_REF'));
if (!empty($arOffers[ $arResult['ID'] ])) {
	$minPrice = 0;
    foreach($arOffers[ $arResult['ID'] ] as $offerID=>$arOffer) { 
    	if ($currentCode == $arOffer['CODE']) {
			if ( !empty($arOffer['PROPERTY_COLOR_REF_VALUE_ID']) ) {
				\Bitrix\Main\Loader::includeModule('highloadblock');
				$hlblock = \Bitrix\Highloadblock\HighloadBlockTable::getById(1)->fetch();
				if (!empty($hlblock)) {
					$entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock);
					$entity_data_class = $entity->getDataClass();
					$reshl = $entity_data_class::getList(array(
					   'select' => array('*'),
					   'filter' => array('UF_XML_ID' => $arOffer['PROPERTY_COLOR_REF_VALUE'])
				   	));
					$row = $reshl->fetch();
					if (!empty($row) && $row['UF_NAME']) {
						$productColor = $row['UF_NAME'];
					}
				}
			}

			$prices = \CCatalogProduct::GetOptimalPrice($offerID, 1, array(2), 'N');
			if(isset($prices['RESULT_PRICE']['DISCOUNT_PRICE'])) {
				if ( !$price || ($prices['RESULT_PRICE']['DISCOUNT_PRICE'] < $price)) {
					$price = $prices['RESULT_PRICE']['DISCOUNT_PRICE'];
				}
			}
    	}
    }
	if( !$price ) {
		$prices = \CCatalogProduct::GetOptimalPrice($arResult['ID'], 1, array(2), 'N');
		if(isset($prices['RESULT_PRICE']['DISCOUNT_PRICE'])) {
			$price = $prices['RESULT_PRICE']['DISCOUNT_PRICE'];
		}
	}
}
if ($productColor) {
	$h1 = $arResult['NAME'];
	$dbElement = \CIBlockElement::GetByID($arParams['ELEMENT_ID']);
	if ( $arElement = $dbElement->GetNext() ) {
		$h1 = $arElement['NAME'];
	}
	$APPLICATION->SetPageProperty('title_custom', $h1 . ' (цвет '.$productColor.') в Новосибирске' . ($price ? (' по цене ' . $price . ' руб.') : '') . ' | Мебельные салоны «Добрый Дом»');
	$APPLICATION->SetPageProperty('description_custom', 'Купить товар '.$h1.' (цвет '.$productColor.') в Новосибирске вы можете в интернет-магазине ddmebel.ru. Подробности по тел.: 8 (383) 381-51-15.');
}
