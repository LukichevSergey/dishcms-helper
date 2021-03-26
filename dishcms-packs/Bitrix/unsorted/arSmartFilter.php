<?
// @var function специальная функция генерации элементов(<OPTION>) для <SELECT> формы фильтра arSmartFilter_pf.
$_fWsassGetListOptions=function($section, $name, $iBlockID, $arFilterKey='IBLOCK_ID', $valueKey='ID', $filterDouble=false) {
	CModule::IncludeModule("iblock");
	$sListSource = "";
	$arFilter = Array($arFilterKey=>$iBlockID);
	$aGroup = Array("ID", "NAME");
	$res = CIBlockElement::GetList(Array("NAME"=>"ASC"), $arFilter, $aGroup, false, false);
	$items=array();
	while($ob = $res->GetNextElement()) {
	  	$arFields = $ob->GetFields();
  		if (trim($arFields['NAME']) == "") continue;
  		if($filterDouble) {
  			$continue=false;
  			foreach($items as $item) {
  				if(($item[$valueKey]==$arFileds[$valueKey]) || ($item['NAME']==$arFields['NAME'])) {
  					$continue=true;
  					break;
  				}
  			}
  			if($continue) continue;
  		}
  		$items[]=$arFields;
  	}
  	foreach($items as $arFields) { 
   		$sel = (isset($_GET[$section][$name]) && ($arFields[$valueKey]==$_GET[$section][$name])) ? " selected" : "";
   		$sListSource .= '<option'.$sel.' value="'.$arFields[$valueKey].'">'.$arFields['NAME']."</option>";
	}
	return $sListSource;
};
// @var function специальная функция генерации элементов(<OPTION>) для <SELECT> разделов инфоблока формы фильтра arSmartFilter_pf.
/*$_fWsassGetListSectionOptions=function($name, $iBlockID) {
	$rs_Section = CIBlockSection::GetList(array('left_margin' => 'asc'), array('IBLOCK_ID' => $iBlockID));
	while ($ar_Section = $rs_Section->Fetch()) {
		$ar_Result[] = array(
			'ID' => $ar_Section['ID'],
			'NAME' => $ar_Section['NAME'],
			'IBLOCK_SECTION_ID' => $ar_Section['IBLOCK_SECTION_ID'],
			'IBLOCK_SECTION_ID' => $ar_Section['IBLOCK_SECTION_ID'],
			'LEFT_MARGIN' => $ar_Section['LEFT_MARGIN'],
			'RIGHT_MARGIN' => $ar_Section['RIGHT_MARGIN'],
			'DEPTH_LEVEL' => $ar_Section['DEPTH_LEVEL'],
		);
	}

	$sListSource = "";
    foreach( $ar_Result as $ar_Value ) {
        $s=str_repeat('-', $ar_Value['DEPTH_LEVEL']-1);
        $s .= ' '.$ar_Value['NAME'];
   		$sel = (isset($_GET['arSmartFilter_ff'][$name]) && ($ar_Value['ID']==$_GET['arSmartFilter_ff'][$name])) ? " selected" : "";
        $sListSource.="<option{$sel} value=\"{$ar_Value['ID']}\">{$s}</option>";
    }
	return $sListSource;
};*/
?>
<ul class="wsass_filter" id="wsass_filter_fileds">
	<li>
		<select name="arSmartFilter_ff[NAME]">
			<option value="">-- Название --</option>
			<?=$_fWsassGetListOptions('arSmartFilter_ff', 'NAME', array(5,6,7), "SECTION_ID", 'NAME', true)?>
		</select>
		<?/*<select name="arSmartFilter_ff[IBLOCK_ID]">
			<option value="">-- Типу услуг --</option>
			<optgroup label="Бизнес - объявления партнеров">
				<option value="8">-- Все</option>
				<?=$_fWsassGetListSectionOptions('IBLOCK_ID', 8)?>
			</optgroup>
			<optgroup label="Заказчики Сервиса cts-club">
				<option value="5">-- Все</option>
				<?=$_fWsassGetListSectionOptions('IBLOCK_ID', 5)?>
			</optgroup>
			<optgroup label="Бизнес - объявления партнеров">
				<option value="9">-- Все</option>
				<?=$_fWsassGetListSectionOptions('IBLOCK_ID', 9)?>
			</optgroup>
		</select>*/?>
	</li>
	<li>
		<select name="arSmartFilter_pf[ORG]">
			<option value="">-- Название организации --</option>
			<?=$_fWsassGetListOptions('arSmartFilter_pf', 'ORG', 30)?>
		</select>
	</li>
	<li>
		<input type="submit" name="set_filter" value="<?=GetMessage("IBLOCK_SET_FILTER")?>" />
		<input type="hidden" name="set_filter" value="Y" />
		&nbsp;&nbsp;<input type="submit" name="del_filter" id="wsass_btn_reset" value="<?=GetMessage("IBLOCK_DEL_FILTER")?>" />
	</li>
</ul>
<script type="text/javascript">
$("#wsass_btn_reset").on("click", function() {
	$("#wsass_filter_fileds").find("option:selected").removeAttr("selected");
});
</script>