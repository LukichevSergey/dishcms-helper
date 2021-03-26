<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("iblock");

function KonturGetElementDetailPageUrl($arItem)
{
    $url=array('catalog');
    $rsGroup=CIBlockElement::GetElementGroups($arItem['ID'], true, array("ID", "CODE"));
    $group=$rsGroup->Fetch();
    
    $rs=CIBlockSection::GetNavChain($arItem['IBLOCK_ID'], $group["ID"], array("ID", "CODE"));
    while($ar=$rs->ExtractFields('nav_')) $url[]=$ar["CODE"];
    
    $url[]=$arItem["ID"];
    return '/' . implode('/', $url) . '/';
}

if (!empty($_POST['q']) && (strlen($_POST['q']) > 1)) {
    $q=\Bitrix\Main\Text\Encoding::convertEncodingToCurrent($_POST['q']);
	$IBLOCK_ID=7;
	$rs=\CIBlockElement::GetList(
	    array('NAME'=>'ASC'),
	    array('NAME'=>"%{$q}%"),
	    false,
	    array('nTopCount'=>10),
	    array('ID', 'IBLOCK_ID', 'NAME')
	);
	$html='<ul>';
	while($el=$rs->Fetch()) {
	    $url=KonturGetElementDetailPageUrl($el);
	    $name=str_replace($q, "<b>{$q}</b>", $el['NAME']);
	    $html.="<li data-href=\"{$url}\">{$name}</li>\n";
	}
    $html.='</ul>';
    
    global $APPLICATION;
    $APPLICATION->RestartBuffer();
	echo $html;
    exit;
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");
?>
