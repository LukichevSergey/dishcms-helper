<?
namespace kontur;

if (!\CModule::IncludeModule("iblock"))
{
    \ShowMessage(\GetMessage("IBLOCK_ERROR"));
    return false;
}

if(!function_exists('GetAllElementIDs'))
{
	function GetAllElementIDs($IBLOCK_ID, $bRefresh=false, $cacheTime=3600, $key=null, $arOrder=array('SORT'=>'ASC'), $arNavStartParams=false)
	{
		$arResult=array();

		$cache = new \CPHPCache();
		$cacheId = 'fkonturGetAllElementIDs'.$key.$IBLOCK_ID;
		$cachePath = '/'.$cacheId;
		if ($cache->InitCache($cacheTime, $cacheId, $cachePath)) {
   			 $vars = $cache->GetVars(); 
   			 $arResult = $vars['arIDs'];
   		}
   		else {
			$rs=\CIBlockElement::GetList($arOrder, array('IBLOCK_ID'=>$IBLOCK_ID, 'ACTIVE'=>'Y', 'SECTION_GLOBAL_ACTIVE'=>'Y','CATALOG_AVAILABLE'=>'Y'), false, $arNavStartParams, array('ID'));
			while($arID=$rs->Fetch()) {
				$arResult[]=$arID['ID'];
			}
   			
   			if($r=$cache->StartDataCache()) {
	         	$cache->EndDataCache(array('arIDs'=>$arResult));
			} 
   		}

		return $arResult;
	}
}