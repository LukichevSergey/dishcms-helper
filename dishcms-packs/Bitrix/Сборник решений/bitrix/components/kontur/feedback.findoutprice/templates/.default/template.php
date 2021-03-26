<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
$GetSectionProperty=function($PROPERTY_CODE, $SECTION_ID=false) use ($arResult) {
    if($SECTION_ID === false) $SECTION_ID=$arResult['IBLOCK_SECTION_ID'];
    $dbSections=\CIBlockSection::GetList(Array("SORT"=>"ASC"), Array("IBLOCK_ID"=>$arResult['IBLOCK_ID'], "ID"=>$SECTION_ID),false, Array($PROPERTY_CODE));
    return ($arSection=$dbSections->GetNext()) ? $arSection[$PROPERTY_CODE] : null;
};
$GetPropertyBySection=function() use ($GetSectionProperty, $arResult) {
    $VALUE='';
    $dbNav = \CIBlockSection::GetNavChain($arResult['IBLOCK_ID'], $arResult['IBLOCK_SECTION_ID'], Array('ID', 'NAME'));
    while($arNav=$dbNav->GetNext()) {
        $bAsProp=$GetSectionProperty('UF_SUBSEC_AS_MANUF', $arNav['ID']);
        if(!empty($bAsProp) && ($bAsProp != 'N')) {
            $PROPERTY_NAME=$GetSectionProperty('UF_SUBSEC_AS_MAN_NM', $arNav['ID']);
            $arNav=$dbNav->GetNext();
            $VALUE=$arNav['NAME'];
            return (empty($PROPERTY_NAME) || empty($VALUE)) ? false : array('PROPERTY_NAME'=>$PROPERTY_NAME, 'VALUE'=>$VALUE);
        }
    }
    return false;
};
?>
<style><?include('style.css')?></style>
<script><?include('script.js')?></script>
<div class="feedback__findoutprice">
<? /*	<div class="findoutprice__title">Узнать цену</div> */ ?>
	<div class="findoutprice__product">
		<div class="findoutprice__product-image">
			<? $picture=empty($arResult['PREVIEW_PICTURE']) ? $arResult['DETAIL_PICTURE'] : $arResult['PREVIEW_PICTURE']; ?>
			<? $src=KonturGetImageSrc($picture, 230, 200); ?>
			<? if(empty($src)) $src='http://placehold.it/100'; ?>
			<img src="<?=$src?>" />
		</div>
		<div class="findoutprice__product-title"><?=$arResult['NAME']?></div>
		<?
        // Подразделы как свойство товара
        if($arPropBySection = $GetPropertyBySection()) { ?>
		<div class="findoutprice__product_manufacturer">
			<div class="findoutprice__product_manufacturer-title"><?=$arPropBySection['PROPERTY_NAME']?></div>
			<div class="findoutprice__product_manufacturer-value"><?=$arPropBySection['VALUE']?></div>
		</div><?
		}
		?>
	</div>
	<div class="findoutprice__form">
	<form method="POST">
		<input type="hidden" name="id" value="<?=$arResult['ID']?>" />
		<div class="findoutprice__form-item">
			<label>Ваше имя*</label>
			<input name="name" type="text" value="" />
		</div>
		<div class="findoutprice__form-item">
			<label>E-mail</label>
			<input name="email" type="text" value="" />
		</div>
		<div class="findoutprice__form-item">
			<label>Телефон для связи*</label>
			<input name="phone" type="text" value="" />
		</div>
		<div class="findoutprice__form-item">
			<label>Ваш комментарий или вопрос</label>
			<textarea name="comment" type="text"></textarea>
		</div>
		<div class="findoutprice__form-control">
			<input type="submit" value="Отправить" class="btn3">
		</div>
	</form>
	</div>
</div>

