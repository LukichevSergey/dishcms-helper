<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/**
 * @var array $arParams
 * @var array $arResult
 * @var SaleOrderAjax $component
 */
$component = $this->__component;
$component::scaleImages($arResult['JS_DATA'], $arParams['SERVICES_IMAGES_SCALING']);

\CJSCore::Init(['jquery', 'date']);

// Get delivery Novisibirsk districts
$arParams['DELIVERY_NSK_DISTRICTS']=[];
if(!empty($arParams['ORDER_AJAX_EXT_NSK_DISTRICTS_IBLOCK_ID'])) {
    $rs=\CIblockElement::GetList(
        ['SORT'=>'ASC'], 
        ['IBLOCK_ID'=>(int)$arParams['ORDER_AJAX_EXT_NSK_DISTRICTS_IBLOCK_ID'], 'ACTIVE'=>'Y'], 
        false, 
        false,
        ['ID', 'IBLOCK_ID', 'NAME', 'PROPERTY_IS_REMOTE']
    );
    while($el=$rs->Fetch()) {
        $arParams['DELIVERY_NSK_DISTRICTS'][$el['ID']]=[
            'ID'=>$el['ID'],
            'NAME'=>$el['NAME'],
            'IS_REMOTE'=>($el['PROPERTY_IS_REMOTE_VALUE'] == 'Y') ? 'Y' : 'N'
        ];
    }
}