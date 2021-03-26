<?
namespace kontur;

if (!\CModule::IncludeModule("iblock"))
{
    \ShowMessage(\GetMessage("IBLOCK_ERROR"));
    return false;
}

if(!function_exists('GetElementRandIDs'))
{
	function GetElementRandIDs($iblockId, $limit=10, $cacheTime=3600, $key=null)
	{
		$arResult=array();

		$cache = new \CPHPCache();
		$cacheId = "fkonturGetElementRandIDs_{$iblockId}_{$limit}_".($key?:'');
		$cachePath = '/'.$cacheId;
		if ($cache->InitCache($cacheTime, $cacheId, $cachePath)) {
   			 $vars = $cache->GetVars(); 
   			 $arResult = $vars['arIDs'];
   		}
   		else {
			$arAllIDs=\kontur\GetAllElementIDs($iblockId, false, $cacheTime);

			if(count($arAllIDs) <= $limit) {
				$arResult=$arAllIDs;
			}
			else {
				$arResult=array();
				$i=0;
				while((count($arResult) < $limit) && ($i++ < 2*$limit)) {
					$idx=rand(0,count($arAllIDs)-1);
					if(isset($arAllIDs[$idx]) && !in_array($arResult, $arAllIDs[$idx])) {
						$arResult[]=$arAllIDs[$idx];
					}
				}
			}
   			
   			if($cache->StartDataCache()) {
	         	$cache->EndDataCache(array('arIDs'=>$arResult));
			} 
   		}

		return $arResult;
	}
}
