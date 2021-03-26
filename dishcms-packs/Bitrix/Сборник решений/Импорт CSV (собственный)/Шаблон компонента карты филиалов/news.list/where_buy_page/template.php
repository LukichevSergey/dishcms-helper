<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */

$this->setFrameMode(true);
?>
<script>
document.addEventListener("DOMContentLoaded",function(){	
    window.mapDialers.init({
        /* icon: "<?= $this->GetFolder() . '/images/icons/icon-26.svg'; ?>", */
        icons: "<?= $this->GetFolder() . '/images/icons'; ?>",
        mapID: "YMapsID",
        center: {lat: "<?= $_SESSION['CITY_POINT_LATITUDE']; ?>", lon: "<?= $_SESSION['CITY_POINT_LONGITUDE']; ?>"},
        zoom: 10,
        partnerType: [
            {XML_ID: "STO", ICON: "/icon-30.svg", ALT: "сервис"}, 
            {XML_ID: "TT", ICON: "/icon-36.svg", ALT: "магазин"}
        ],
        specialization: [
            {XML_ID: "AUTO", ICON: "/icon-35.svg", ALT: "авто"}, 
            {XML_ID: "MOTO", ICON: "/icon-34.svg", ALT: "мото"},
            {XML_ID: "AQUA", ICON: "/icon-33.svg", ALT: "водная техника"},
            {XML_ID: "GARDEN", ICON: "/icon-32.svg", ALT: "садовая техника"},
            {XML_ID: "CARGO", ICON: "/icon-31.svg", ALT: "грузовая техника"}
        ],
        destinations: <?= call_user_func_array(function($arResult){
            $destinations = [];
            foreach($arResult['ITEMS'] as $arItem) {
                if(!empty($arItem['PROPERTIES']['MAPS']['VALUE'])) {
                    $destinations[$arItem['ID']] = [
                        'NAME' => $arItem['NAME'],
                        'COORDS' => explode(',', $arItem['PROPERTIES']['MAPS']['VALUE']),
                    ];
                    if(!empty($arItem['PROPERTIES'])) {
                        foreach($arItem['PROPERTIES'] as $propertyCode=>$arProperty) {
                            if(!in_array($propertyCode, ['MAPS', 'OFICIAL_DILLER'])) {
                                if(is_array($arProperty['VALUE'])) { 
                                    $destinations[$arItem['ID']][$propertyCode] = $arProperty['VALUE_XML_ID'];
                                }
                                else {
                                    $destinations[$arItem['ID']][$propertyCode] = $arProperty['VALUE'];
                                }
                            }
                        }
                    }
                }
            }
            return json_encode($destinations, JSON_UNESCAPED_UNICODE);
       }, [$arResult]); ?>
    });
    $(document).on("change", "#filials__cities", function(e) {
    	window.mapDialers.map.setCenter($(e.target).val().split(","), 10);
    	var isdefault = ($(e.target).find("option:selected").data("is-default") === "1"); 
   		BX.setCookie("filialcity", isdefault ? "" : $(e.target).find("option:selected").text(), {path: "/"});
   		BX.setCookie("filialcitypoint", isdefault ? "" : $(e.target).val(), {path: "/"});
    });
});
</script>
<?php if(!empty($arResult['CITIES'])): ?>
<div class="filials__cities">
    <span>Вы находитесь здесь: </span>
    <div class="filials__cities-choosebox">
        <select id="filials__cities" class="filials__cities-select">
        	<?php $bCitySelected = false; ?>
            <?foreach ($arResult['CITIES'] as $arCity) {?>
                <option value="<?= $arCity['POINT']; ?>"<? if($arCity['SELECTED'] == 'Y') { echo 'selected="selected"'; $bCitySelected=true; } ?>><?= $arCity['NAME']; ?></option>
            <?}?>
            <?php if(!$bCitySelected): ?>
            	<option value="<?= $_SESSION['CITY_POINT']; ?>" selected="selected" data-is-default="1"><?= $_SESSION['CITY']; ?></option>
            <?php endif; ?>
        </select>
    </div>
</div>
<?php endif; ?>

<div id="YMapsID" style="width: 100%; height: 350px;"></div>