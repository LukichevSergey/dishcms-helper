<?php
if(!function_exists("KonturGetMetaDescName"))
{
    function KonturGetMetaDescName($CITYPREFIX=null)
    {
		if($CITYPREFIX === null) $CITYPREFIX=CITY_PREFIX;

		$UF_META_DESCRIPTION='UF_MAIN_META_DESC';
		if($CITYPREFIX) {
		    $UF_META_DESCRIPTION='UF_' . $CITYPREFIX . 'META_DESC';
		}

		return $UF_META_DESCRIPTION;
	}
}
