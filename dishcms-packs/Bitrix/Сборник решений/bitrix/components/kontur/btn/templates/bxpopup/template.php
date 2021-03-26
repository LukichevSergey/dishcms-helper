<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?
\Bitrix\Main\Page\Asset::getInstance()->addJs('/bitrix/js/kontur.core/tools/bx_popup.js');
?><?
if(!empty($arParams['LINK'])):
    ?><noindex><a rel="nofollow" id="<?=$arResult['BTN_ID']?>" href="<?=$arParams['LINK']?>" class="<?=$arParams['CSS_CLASS']?>"><?=$arParams['LABEL']?></a></noindex><?
else: 
    ?><span id="<?=$arResult['BTN_ID']?>" class="<?=$arParams['CSS_CLASS']?>"><?=$arParams['LABEL']?></span><?
endif
?>
<script>;document.addEventListener("DOMContentLoaded",function(){KBTN_BXPOPUP_Init({
	btnId: "<?=$arResult['BTN_ID']?>",
	popupId:"<?=$arParams['BX_POPUP_ID']?>",
	popupTitle: "<?=$arParams['BX_POPUP_TITLE']?>",
	popupBtnOkEnable: <?=($arParams['BX_POPUP_BTN_OK_ENABLE'] == 'Y') ? 'true' : 'false'?>,<?
    if($arParams['BX_POPUP_BTN_OK_ENABLE'] == 'Y'): 
        ?>popupBtnOkLabel: "<?=$arParams['BX_POPUP_BTN_OK_LABEL']?>",<?
        if($arParams['BX_POPUP_BTN_OK_CSSCLASS']): 
        ?>popupBtnOkClassName: "<?=$arParams['BX_POPUP_BTN_OK_CSSCLASS']?>",<?
        endif;
        if(trim($arParams['BX_POPUP_BTN_OK_CLICK'])): 
        ?>popupBtnOkClick: <?=$arParams['BX_POPUP_BTN_OK_CLICK']?>,<?
        endif;
    endif;
    if($arParams['BX_POPUP_BTN_CANCEL_CSSCLASS']): 
        ?>popupBtnCancelClassName: "<?=$arParams['BX_POPUP_BTN_CANCEL_CSSCLASS']?>",<?
    endif;
    ?>popupBtnCancelLabel: "<?=$arParams['BX_POPUP_BTN_CANCEL_LABEL']?>",
    popupOptions: {autoHide:true, closeByEsc:true, overlay:{backgroundColor:"#000000", opacity:"20"}}
});});</script>

