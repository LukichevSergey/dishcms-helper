<?
namespace kontur;

if (!\CModule::IncludeModule("iblock"))
{
    \ShowMessage(\GetMessage("IBLOCK_ERROR"));
    return false;
}

if(!function_exists('GetElementIDs'))
{
	function GetElementIDs($filter, $bRefresh=false, $cacheTime=3600, $key=null, $arOrder=array('SORT'=>'ASC'), $arNavStartParams=false)
	{
		$arResult=array();

		$cache = new \CPHPCache();
		$cacheId = 'fkonturGetElementIDs'.md5(serialize($filter)).$key;
		$cachePath = '/'.$cacheId;
		if ($cache->InitCache($cacheTime, $cacheId, $cachePath)) {
   			 $vars = $cache->GetVars(); 
   			 $arResult = $vars['arIDs'];
   		}
   		else {
			$rs=\CIBlockElement::GetList($arOrder, $filter, false, $arNavStartParams, array('ID'));
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