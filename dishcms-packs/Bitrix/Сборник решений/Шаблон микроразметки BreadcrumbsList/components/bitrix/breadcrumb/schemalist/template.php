<?if( !defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true ) die();

if( empty( $arResult ) )
	return "";

$strReturn = '<div class="breadcrumb">';

for( $index = 0, $itemSize = count($arResult); $index < $itemSize; $index++ ){
	$title = htmlspecialcharsex($arResult[$index]["TITLE"]);
	if( $arResult[$index]["LINK"] <> "" && $arResult[$index]['LINK'] != GetPagePath() && $arResult[$index]['LINK']."index.php" != GetPagePath()) {
		$strReturn .= '<div itemscope itemtype="http://schema.org/BreadcrumbList">';
		$strReturn .= '<div itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
		$strReturn .= '<a itemprop="item" href="'.$arResult[$index]["LINK"].'" title="'.$title.'"><span itemprop="name">'.$title.'</span></a>';
		$strReturn .= '<meta itemprop="position" content="'.($index + 1).'" />';
		$strReturn .= '</div>';
		$strReturn .= '</div>';
	}
	else{
		$strReturn .= '<div itemscope itemtype="http://schema.org/BreadcrumbList" class="active">';
		$strReturn .= '<div itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
		$strReturn .= '<span itemprop="name">'.$title.'</span>';
		$strReturn .= '<meta itemprop="position" content="'.($index + 1).'" />';
		$strReturn .= '</div>';
		$strReturn .= '</div>';
		break;
	}
}

$strReturn .= '</div>';
return $strReturn;?>
