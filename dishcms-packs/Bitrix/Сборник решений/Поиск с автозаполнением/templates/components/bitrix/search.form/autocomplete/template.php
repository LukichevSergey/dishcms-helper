<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<!--noindex-->
<div class="search_box">
	<form action="<?=$arResult["FORM_ACTION"]?>">
		<input type="text" value="" name="q" placeholder="<?=GetMessage("BSF_T_SEARCH_BUTTON")?>">
	    <input name="s" type="submit" value="">
    </form>
    <div class="search_box-qresults" style="dispaly:none"></div>
</div>
<!--/noindex-->