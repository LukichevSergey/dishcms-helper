<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if(!empty($arResult['PREVIEW_PICTURE']) || !empty($arResult['DETAIL_PICTURE'])):?>
<?if(empty($arResult['DETAIL_PICTURE'])){$primaryPicture=$arResult['PREVIEW_PICTURE'];}else{$primaryPicture=$arResult['DETAIL_PICTURE'];}?>
<?$containerId=uniqid('js');?>
<?if($arParams['PUBLISH_FANCYBOX_JS'] != 'N'):?><script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.0.47/jquery.fancybox.min.js"></script><?endif;?>
<?if($arParams['PUBLISH_FANCYBOX_CSS'] != 'N'):?><link href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.0.47/jquery.fancybox.min.css" rel="stylesheet"><?endif?>
<div id="<?=$containerId?>">
    <?$arResult['MORE_PHOTO'][]=$primaryPicture;?>
    <style>.<?=$containerId?>-multiple-items.slick-slider img{max-width:<?=$arParams['PREVIEW_WIDTH']?>px;max-height:<?=$arParams['PREVIEW_HEIGHT']?>px;margin:0 2px;}</style>
    <style>.<?=$containerId?>-single-slide .img{width:<?=$arParams['WIDTH']?>px;height:<?=$arParams['HEIGHT']?>px;margin:0 2px;margin-bottom:5px;background-size:cover !important;background-repeat:no-repeat !important;}</style>
    <div class="single-slide <?=$containerId?>-single-slide">
        <?$picture=CFile::GetFileArray($primaryPicture);?>
        <?$img=CFile::ResizeImageGet($primaryPicture, ['width'=>$arParams['WIDTH'], 'height'=>$arParams['HEIGHT']], BX_RESIZE_IMAGE_EXACT, true );?>
        <a data-fancybox="<?=$containerId?>_images" rel="<?=$containerId?>_images" href="<?=$picture['SRC']?>" data-caption>
            <div class="img" style="background:url(<?=$img["src"]?>);" title="<?=$arResult["NAME"]?>">&nbsp;</div>
        </a>
        <? if(!empty($arResult['MORE_PHOTO'])): ?>
            <?foreach($arResult['MORE_PHOTO'] as $pictureId):?>
                <?$picture=CFile::GetFileArray($pictureId);?>
                <a data-fancybox="<?=$containerId?>_images" rel="<?=$containerId?>_images" href="<?=$picture['SRC']?>" style="display:none" data-caption>&nbsp;</a>
            <?endforeach;?>
        <? endif; ?>
    </div>
    <? if(!empty($arResult['MORE_PHOTO'])): ?>
        <div class="multiple-items slick-slider <?=$containerId?>-multiple-items">
            <?foreach($arResult['MORE_PHOTO'] as $pictureId):?>
                <?$picture=CFile::GetFileArray($pictureId);?>
                <?$big=CFile::ResizeImageGet($pictureId, ['width'=>$arParams['WIDTH'], 'height'=>$arParams['HEIGHT']], BX_RESIZE_IMAGE_EXACT, true );?>
                <?$img=CFile::ResizeImageGet($pictureId, ['width'=>$arParams['PREVIEW_WIDTH'], 'height'=>$arParams['PREVIEW_HEIGHT']], BX_RESIZE_IMAGE_EXACT, true );?>
                <img class="slide" src="<?=$img["src"]?>" data-src="<?=$big['src']?>" data-origin="<?=$picture['SRC']?>" alt="" />
            <?endforeach;?>
        </div>
        <script>
        $('.multiple-items').slick({
          infinite: true,
          slidesToShow: 3,
          slidesToScroll: 1,
          variableWidth: false
        });
        $(document).on('click', '#<?=$containerId?> .multiple-items .slide', function(e) {
            $('#<?=$containerId?> .single-slide .img').css('background', 'url('+$(e.target).data('src')+')');
            $('#<?=$containerId?> .single-slide .img').parent().attr('href', $(e.target).data('origin'));
        });
        $('#<?=$containerId?> .single-slide [data-fancybox]').fancybox();
        </script>
    <? endif; ?>
</div>
<? endif; ?>
