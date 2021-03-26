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

$currentID = $arParams['SECTION_VAR_ID'];

function findInSubSection($sections, $id) {
	foreach ($sections as $section) {
		if ($section['ID'] == $id) {
			return true;
		}
	}

	return false;
}

$this->setFrameMode(true);?>

<?if($arResult["NEW_TREE"]){?>
<? 
if(!function_exists('catalog_menu_subsection_print')) {
	function catalog_menu_subsection_print($arSubSections, $currentID) {
		if(!empty($arSubSections)): ?>
			<ul class="categories_menu__nav">
				<?foreach ($arSubSections as $arSubSect){?>
					<li class="<?= $arSubSect['ID'] == $currentID ? 'active' : '' ?>">
						<a href="<?=$arSubSect["SECTION_PAGE_URL"]?>"><?=$arSubSect["NAME"]?></a>
						<? catalog_menu_subsection_print($arSubSect['SUBSECTIONS'], $currentID); ?>
					</li>
				<?}?>
			</ul>
		<?
		endif;
	}
} 
?>
	<div class="categories_menu">
		<?foreach ($arResult["NEW_TREE"] as $arSection){
			$this->AddEditAction($arSection['ID'], $arSection['EDIT_LINK'], CIBlock::GetArrayByID($arSection["IBLOCK_ID"], "SECTION_EDIT"));
			$this->AddDeleteAction($arSection['ID'], $arSection['DELETE_LINK'], CIBlock::GetArrayByID($arSection["IBLOCK_ID"], "SECTION_DELETE"), array("CONFIRM" => GetMessage('CT_BCSL_ELEMENT_DELETE_CONFIRM')));
			?>
			<div class="categories_menu_elem <?= $arSection['ID'] == $currentID || findInSubSection($arSection["SUBSECTIONS"], $currentID) ? 'active' : '' ?>">
				<div class="categories_menu__title categories_menu__toggle">
					<a href="javascript:;"><?=$arSection["NAME"]?></a>
					<!--<div class="categories_menu__toggle"><div class="triangle"></div></div>-->
				</div>
				<? catalog_menu_subsection_print($arSection["SUBSECTIONS"], $currentID); ?>
			</div>
		<?}?>
	</div>
<?}?>

