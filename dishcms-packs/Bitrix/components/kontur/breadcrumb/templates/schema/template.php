<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

//delayed function must return a string
if(empty($arResult))
	return "";

global $GLOBALS;
$SECOND_ITEM_HIDE = ($GLOBALS['BREADCRUMB_SECOND_ITEM_HIDE'] == 'Y');
$FIRST_ITEM_TITLE = $GLOBALS['BREADCRUMB_FIRST_ITEM_TITLE'];

$strReturn = '<div class="breadcrumbs">';
$num_items = count($arResult);
for($index = 0, $itemSize = $num_items; $index < $itemSize; $index++)
{
	if(($index === 1) && $SECOND_ITEM_HIDE) continue;

	$title = htmlspecialcharsex((($index===0) && !empty($FIRST_ITEM_TITLE)) ? $FIRST_ITEM_TITLE : $arResult[$index]["TITLE"]);
	
	$strReturn .= '<div class="breadcrumbs__item" itemscope="" itemtype="http://data-vocabulary.org/Breadcrumb">';
	if($arResult[$index]["LINK"] <> "" && $index != $itemSize-1) 
		$strReturn .= '<a href="'.$arResult[$index]["LINK"].'" title="'.$title.'" itemprop="url"><span itemprop="title">'.$title.'</span></a>';
	else 
		$strReturn .= '<span itemprop="title">'.$title.'</span>';
	
	$strReturn .= '</div>';
}

$strReturn .= '</div>';

return $strReturn;
?>
