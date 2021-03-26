<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?
use Kontur\Core\Main\Tools\Data,
    Kontur\Core\Iblock\Tools\File;

if(!empty($arResult['ITEMS'])):
$cssClass=Data::get($arParams, 'FANCYBOX_CLASS', 'gallery-image');
$rel=Data::get($arParams, 'FANCYBOX_REL', 'gallery-images');
?><div class="mediagallery"><div class="row"><?
	$i=0;
    foreach($arResult['ITEMS'] as $item):
        ?><div class="col-sm-6 col-md-4 col-lg-3">
			<div class="mediagallery-item">
				<a href="<?=$item['PATH']?>" class="<?=$cssClass?>" rel="<?=$rel?>"><img src="<?=File::getSrc(
						$item,
						1, 
						(int)Data::get($arParams, 'TMB_WIDTH', 0),
						(int)Data::get($arParams, 'TMB_HEIGHT', 0),
						BX_RESIZE_IMAGE_EXACT,
                        '',
                        'PATH'
					)?>" /></a>
			</div>
		</div>
		<?if(($i+1) % 2 == 0) { ?><div class="clearfix visible-sm"></div><? } ?>
		<?if(($i+1) % 3 == 0) { ?><div class="clearfix visible-md"></div><? } ?>
		<?if(($i+1) % 4 == 0) { ?><div class="clearfix visible-lg"></div><? } ?>
		<?$i++?><?
    endforeach;
	?></div>
</div><? 
if(Data::get($arParams, 'FANCYBOX_SCRIPT_DISABLE', 'N') != 'Y'): 
?><script>document.addEventListener("DOMContentLoaded",function() {
	var $images=$('.mediagallery a.<?=$cssClass?>'); 
	if($images.length) {
	    $images.fancybox({
	        overlayColor: '#333',
	        overlayOpacity: 0.8,
	        titlePosition : 'over',
	        helpers: { overlay: { locked: false } }
	    });
	}
});</script><?
endif;
?><?
endif;
?>
