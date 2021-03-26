<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?
use Kontur\Core\Main\Tools\Data,
    Kontur\Core\Iblock\Tools\File;

if(!empty($arResult['ITEMS'])):
?><ul class="medialib__collection"><?
    foreach($arResult['ITEMS'] as $item):
        ?><li><? 
        echo File::get2Img(
            $item, 
            (int)Data::get($arParams, 'TMB_WIDTH', 0),
            (int)Data::get($arParams, 'TMB_HEIGHT', 0),
            '',
            BX_RESIZE_IMAGE_EXACT,
            '',
            $item['NAME'],
            'PATH'
        );
        ?></li><?
    endforeach;
?></ul><?
endif;
?>
