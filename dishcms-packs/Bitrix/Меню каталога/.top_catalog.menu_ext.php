<? 
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die(); 
global $APPLICATION; 
$aMenuLinksExt = $APPLICATION->IncludeComponent("bitrix:menu.sections","",Array(
        "IS_SEF" => "Y", 
        "SEF_BASE_URL" => "/catalog/", 
        "SECTION_PAGE_URL" => "#SECTION_ID#/", 
        "DETAIL_PAGE_URL" => "#SECTION_ID#/#ELEMENT_ID#", 
        "IBLOCK_TYPE" => "1c_catalog", 
        "IBLOCK_ID" => "11", 
        "DEPTH_LEVEL" => "4", 
        "CACHE_TYPE" => "A", 
        "CACHE_TIME" => "3600" 
    )
);

$maxTopLinks=4;
$countTopLinks=0;
$aMenuTopLinksExt=array();
foreach($aMenuLinksExt as $arMenuLink) {
	if(($arMenuLink[3]['DEPTH_LEVEL'] == 1) && ($countTopLinks++ > $maxTopLinks)) {
		break;
	}
	$aMenuTopLinksExt[]=$arMenuLink;
}

$aMenuCatalogLinks=array(array('<i class="fa fa-angle-down js-menu-catalog-popup"></i>', 'javascript:;', array(), array('DEPTH_LEVEL'=>1, 'IS_PARENT'=>true)));
?><div class="menu-catalog js-menu-catalog" style="display:none">
	<span class="nav__catalog-top_header">Каталог</span>
	<ul class="nav__catalog-top_level-1"><?
	$depthLevel=1;
	foreach($aMenuLinksExt as $arLink) {
		if($depthLevel > $arLink[3]['DEPTH_LEVEL']) {
            ?></li></ul><?
            $depthLevel=$arLink[3]['DEPTH_LEVEL'];
        }
		elseif($depthLevel < $arLink[3]['DEPTH_LEVEL']) {
			?><ul class="nav__catalog-top_level-<?=$arLink[3]['DEPTH_LEVEL']?>"><?
			$depthLevel=$arLink[3]['DEPTH_LEVEL'];
		}
		else { ?></li><?
		}
	?><li><a href="<?=$arLink[1]?>"><?=$arLink[0]?></a><?
	}
	str_repeat('</li></ul>', $depthLevel-1);
?></ul></div><?

$aMenuLinks = array_merge($aMenuCatalogLinks, $aMenuLinks);
$aMenuLinks = array_merge($aMenuLinks, $aMenuTopLinksExt); 
?><script>
$(document).ready(function() {
	$(document).on("click", ".js-menu-catalog-popup", function(e) {
		$(".js-menu-catalog").toggle();
	});
	$(document).on("mouseleave", ".js-menu-catalog", function(e) {
		if(!$(e.target).closest(".bx-top-nav").length)
	        $(".js-menu-catalog").hide();
    });
});
</script><?

?>
