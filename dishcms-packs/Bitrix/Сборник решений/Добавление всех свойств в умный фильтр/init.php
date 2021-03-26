function set_smart_filter_to_all_properties($iblockId) {
	\CModule::IncludeModule('iblock');
	$arFilter = array('IBLOCK_ID' => $iblockId);
	$rsProperty = CIBlockProperty::GetList(array(),$arFilter);
	while ($element = $rsProperty->Fetch()) {
    	// добавление свойства в умный фильтр:
	    $arFields = Array('SMART_FILTER' => 'Y', 'IBLOCK_ID' => $iblockId);
	    $ibp = new CIBlockProperty();
    	if(!$ibp->Update($element['ID'], $arFields)) echo $ibp->LAST_ERROR;
	}
	echo 'done!'; die;
}

