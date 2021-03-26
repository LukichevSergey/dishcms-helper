
$arResult["GRID"]["HEADERS"]=array_filter($arResult["GRID"]["HEADERS"], function($v) { return $v['id'] != 'PROPERTY_MORE_PHOTO_VALUE'; });
foreach($arResult["GRID"]["ROWS"] as $k=>$arItem) {
    if(!empty($arItem['~PROPERTY_MORE_PHOTO_VALUE'])) {
        $iMorePhotoId=(int)preg_replace('/^(\d+)[^\d]+.*?$/', '\\1', $arItem['~PROPERTY_MORE_PHOTO_VALUE']);
        $arResult["GRID"]["ROWS"][$k]['DETAIL_PICTURE_SRC']=KonturGetImageSrc($iMorePhotoId, 132, 132, BX_RESIZE_IMAGE_EXACT);
        unset($arResult["GRID"]["ROWS"][$k]['PROPERTY_MORE_PHOTO_VALUE']);
    }
}

