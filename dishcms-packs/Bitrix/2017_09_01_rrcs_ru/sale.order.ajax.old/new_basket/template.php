<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if($USER->IsAuthorized() || $arParams["ALLOW_AUTO_REGISTER"] == "Y")
{
	if($arResult["USER_VALS"]["CONFIRM_ORDER"] == "Y" || $arResult["NEED_REDIRECT"] == "Y")
	{
		if(strlen($arResult["REDIRECT_URL"]) > 0)
		{
			$APPLICATION->RestartBuffer();
			?>
			<script type="text/javascript">
				window.top.location.href='<?=CUtil::JSEscape($arResult["REDIRECT_URL"])?>';
			</script>
			<?
			die();
		}

	}
}

$APPLICATION->SetAdditionalCSS($templateFolder."/style_cart.css");
$APPLICATION->SetAdditionalCSS($templateFolder."/style.css");
?>

<a name="order_form"></a>

<div id="order_form_div" class="order-checkout">
<NOSCRIPT>
	<div class="errortext"><?=GetMessage("SOA_NO_JS")?></div>
</NOSCRIPT>

<?
if (!function_exists("getColumnName"))
{
	function getColumnName($arHeader)
	{
		return (strlen($arHeader["name"]) > 0) ? $arHeader["name"] : GetMessage("SALE_".$arHeader["id"]);
	}
}

if (!function_exists("cmpBySort"))
{
	function cmpBySort($array1, $array2)
	{
		if (!isset($array1["SORT"]) || !isset($array2["SORT"]))
			return -1;

		if ($array1["SORT"] > $array2["SORT"])
			return 1;

		if ($array1["SORT"] < $array2["SORT"])
			return -1;

		if ($array1["SORT"] == $array2["SORT"])
			return 0;
	}
}
?>

<div class="bx_order_make">
	<?
	if(!$USER->IsAuthorized() && $arParams["ALLOW_AUTO_REGISTER"] == "N")
	{
		if(!empty($arResult["ERROR"]))
		{
			foreach($arResult["ERROR"] as $v)
				echo ShowError($v);
		}
		elseif(!empty($arResult["OK_MESSAGE"]))
		{
			foreach($arResult["OK_MESSAGE"] as $v)
				echo ShowNote($v);
		}

		include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/auth.php");
	}
	else
	{
		if($arResult["USER_VALS"]["CONFIRM_ORDER"] == "Y" || $arResult["NEED_REDIRECT"] == "Y")
		{
			if(strlen($arResult["REDIRECT_URL"]) == 0)
			{
				include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/confirm.php");
			}
		}
		else
		{
			?>
			<script type="text/javascript">

			<?if(CSaleLocation::isLocationProEnabled()):?>

				<?
				// spike: for children of cities we place this prompt
				$city = \Bitrix\Sale\Location\TypeTable::getList(array('filter' => array('=CODE' => 'CITY'), 'select' => array('ID')))->fetch();
				?>

				BX.saleOrderAjax.init(<?=CUtil::PhpToJSObject(array(
					'source' => $this->__component->getPath().'/get.php',
					'cityTypeId' => intval($city['ID']),
					'messages' => array(
						'otherLocation' => '--- '.GetMessage('SOA_OTHER_LOCATION'),
						'moreInfoLocation' => '--- '.GetMessage('SOA_NOT_SELECTED_ALT'), // spike: for children of cities we place this prompt
						'notFoundPrompt' => '<div class="-bx-popup-special-prompt">'.GetMessage('SOA_LOCATION_NOT_FOUND').'.<br />'.GetMessage('SOA_LOCATION_NOT_FOUND_PROMPT', array(
							'#ANCHOR#' => '<a href="javascript:void(0)" class="-bx-popup-set-mode-add-loc">',
							'#ANCHOR_END#' => '</a>'
						)).'</div>'
					)
				))?>);

			<?endif?>

			var BXFormPosting = false;
			function submitForm(val)
			{
				if (BXFormPosting === true)
					return true;

				BXFormPosting = true;
				if(val != 'Y')
					BX('confirmorder').value = 'N';

				var orderForm = BX('ORDER_FORM');
				BX.showWait();

				<?if(CSaleLocation::isLocationProEnabled()):?>
					BX.saleOrderAjax.cleanUp();
				<?endif?>

				BX.ajax.submit(orderForm, ajaxResult);

				return true;
			}

			function ajaxResult(res)
			{
				var orderForm = BX('ORDER_FORM');
				try
				{
					// if json came, it obviously a successfull order submit

					var json = JSON.parse(res);
					BX.closeWait();

					if (json.error)
					{
						BXFormPosting = false;
						return;
					}
					else if (json.redirect)
					{
						window.top.location.href = json.redirect;
					}
				}
				catch (e)
				{
					// json parse failed, so it is a simple chunk of html

					BXFormPosting = false;
					BX('order_form_content').innerHTML = res;

					<?if(CSaleLocation::isLocationProEnabled()):?>
						BX.saleOrderAjax.initDeferredControl();
					<?endif?>
				}

				BX.closeWait();
				BX.onCustomEvent(orderForm, 'onAjaxSuccess');
				
				window.konturKladrInit();
				if($(".nobasemessage").length) KladrJsObj.nobasemessage();
				
				/*var currentDeliveryId=$(".bx-old-soa-delivery .bx_element [name='DELIVERY_ID']:checked").val();
				if((currentDeliveryId == <?=intval($arParams['DELIVERY_SELFPICKUP_ID'])?>) || (currentDeliveryId == <?=intval($arParams['DELIVERY_OUTNSK_ID'])?>)) {
					$(".order-property_location").hide();
				}
				else {
					$(".order-property_location").show();
				}*/
				/*if(!window.oldSoaFormInitialized) {
					window.oldSoaFormInitialized=false;
					var currentDeliveryId=$(".bx-old-soa-delivery .bx_element [name='DELIVERY_ID']:checked").val();
					var deliveryHash=[]; 
					$(".bx-old-soa-delivery .bx_element [name='DELIVERY_ID']").each(function(){deliveryHash.push($(this).val());}); 
					if(!window.oldSoaFormDeliveryHash || (window.oldSoaFormDeliveryHash != deliveryHash.join(':'))) {
						window.oldSoaFormDeliveryHash=deliveryHash.join(':');
						setTimeout(function() { 
						$(".bx-old-soa-delivery .bx_element [name='DELIVERY_ID'][value!='<?=intval($arParams['DELIVERY_SELFPICKUP_ID'])?>'][value!='<?=intval($arParams['DELIVERY_OUTNSK_ID'])?>']:radio:eq(0):not(:checked)").trigger("click"); }, 500);
					}
				}*/				
			}

			function SetContact(profileId)
			{
				BX("profile_change").value = "Y";
				submitForm();
			}
			</script>			
			<?if($_POST["is_ajax_post"] != "Y")
			{
				?><form action="<?=$APPLICATION->GetCurPage();?>" method="POST" name="ORDER_FORM" id="ORDER_FORM" enctype="multipart/form-data">
				<?=bitrix_sessid_post()?>
				<div id="order_form_content">
				<?
			}
			else
			{
				$APPLICATION->RestartBuffer();
			}

			if($_REQUEST['PERMANENT_MODE_STEPS'] == 1)
			{
				?>
				<input type="hidden" name="PERMANENT_MODE_STEPS" value="1" />
				<?
			}

			if(!empty($arResult["ERROR"]) && $arResult["USER_VALS"]["FINAL_STEP"] == "Y")
			{
				foreach($arResult["ERROR"] as $v)
					echo ShowError($v);
				?>
				<script type="text/javascript">
					top.BX.scrollToNode(top.BX('ORDER_FORM'));
				</script>
				<?
			}

			if ($arParams["DELIVERY_TO_PAYSYSTEM"] == "p2d")
			{
				include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/paysystem.php");
				include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/delivery.php");
			}
			else
			{
				include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/delivery.php");
				include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/paysystem.php");
			}
			include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/person_type.php"); 
			include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/props.php"); 
			include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/related_props.php");
	
			?>
			<div class="bx_section bx-old-soa-comment">
				<div class="order-property_textarea">
					<div class="order-property__label"><?=GetMessage("SOA_TEMPL_SUM_COMMENTS")?></div>
					<div class="order-property__input"><textarea name="ORDER_DESCRIPTION" id="ORDER_DESCRIPTION" style="max-width:100%;min-height:70px"><?=$arResult["USER_VALS"]["ORDER_DESCRIPTION"]?></textarea></div>
					<input type="hidden" name="" value="">
				</div>
			</div>
			<?


			include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/summary.php");
			if(strlen($arResult["PREPAY_ADIT_FIELDS"]) > 0)
				echo $arResult["PREPAY_ADIT_FIELDS"];
			?>
			<input type="hidden" name="INITIALIZE" id="OLD_SOA_INITIALIZE" value="0" />

			<?if($_POST["is_ajax_post"] != "Y")
			{
				?>
					</div>
					<input type="hidden" name="confirmorder" id="confirmorder" value="Y">
					<input type="hidden" name="profile_change" id="profile_change" value="N">
					<input type="hidden" name="is_ajax_post" id="is_ajax_post" value="Y">
					<input type="hidden" name="json" value="Y">
					<?/*<div class="bx_ordercart_order_pay_center"><a href="javascript:void();" onclick="submitForm('Y'); return false;" id="ORDER_CONFIRM_BUTTON" class="checkout"><?=GetMessage("SOA_TEMPL_BUTTON")?></a></div>*/?>
				</form>
				<?
				if($arParams["DELIVERY_NO_AJAX"] == "N")
				{
					?>
					<div style="display:none;"><?$APPLICATION->IncludeComponent("bitrix:sale.ajax.delivery.calculator", "", array(), null, array('HIDE_ICONS' => 'Y')); ?></div>
					<?
				}
			}
			else
			{
				?>
				<script type="text/javascript">
					top.BX('confirmorder').value = 'Y';
					top.BX('profile_change').value = 'N';
				</script>
				<?
				die();
			}
		}
	}
	?>
	</div>
</div>
<script>
<?php
$LOC_STEPS_JS_CONTROL_GLOBAL_ID=100;
$LOC_SEARCH_JS_CONTROL_GLOBAL_ID=200;
?>
document.addEventListener("DOMContentLoaded",function(){
	window.KonturKladrJsObjMapCoords=[];
	window.KonturKladrJsObjMapLastCoords=false;
	window.KonturKladrJsObjMapLastDistrict=false;
	$(document).on("click", ".nobasemessage", function() {
		window.konturKladrInit();
	});
	// определение района по адресу КЛАДР.
	window.konturKladrInit=function(){
		var kladrIntervalId=setInterval(function(){
			if((typeof(KladrJsObj.map)!="undefined") && KladrJsObj.map){
				konturKladrInit();clearInterval(kladrIntervalId);
			}else{
				if($(".nobasemessage").length) KladrJsObj.nobasemessage();
			}
		},200);
	};
	var kladrIntervalInitId=setInterval(function(){
		if((typeof(KladrJsObj)!="undefined")){
			changeLocationValue(false, true);
			clearInterval(kladrIntervalInitId);
		}
	}, 200);
	function changeLocationValue(code, forcy) {
		if(!code || (typeof(code) == "undefined")) code=<?=$arParams['LOCATIONS_DEFAULT_CODE']?>;
		$.post(window.location.href, {ajaxc: "locations", ajaxa: "getLocationIdByCode", code: code}, function(response) {
			if((typeof(response.location) != "undefined") && response.location.ID) {
				if((forcy === true) || ($("[name='ORDER_PROP_4']").val() != response.location.ID)) {
					$("[name='ORDER_PROP_4']").val(response.location.ID);
					submitForm();
				}
			}
			else {
				$(".bx-old-soa-delivery .bx_element [name='DELIVERY_ID'][value='<?=intval($arParams['DELIVERY_OUTNSK_ID'])?>']:radio:eq(0):not(:checked)").trigger("click");
			}
			//console.log(response);
		}, "json");
		/*if(typeof(window.BX.locationSelectors[<?=$arParams['LOCATIONS_JS_CONTROL_GLOBAL_ID']?>]) != "undefined") {
		//window.BX.locationSelectors[<?=$arParams['LOCATIONS_JS_CONTROL_GLOBAL_ID']?>].setValueByLocationCode(<?=$arParams['LOCATIONS_DEFAULT_CODE']?>, true);
				window.BX.locationSelectors[<?=$arParams['LOCATIONS_JS_CONTROL_GLOBAL_ID']?>].setValueByLocationCode(<?=$arParams['LOCATIONS_DEFAULT_ID']?>);
				var locItem=window.BX.locationSelectors[<?=$arParams['LOCATIONS_JS_CONTROL_GLOBAL_ID']?>].getCurrentItem();
				if(locItem && (typeof(locItem.VALUE) != "undefined")) {
					$("[name='ORDER_PROP_4']").val(locItem.VALUE);
					submitForm();
				}
				//console.log("window.BX.locationSelectors[<?=$arParams['LOCATIONS_JS_CONTROL_GLOBAL_ID']?>].setValueByLocationCode(<?=$arParams['LOCATIONS_DEFAULT_CODE']?>, true);");
				// window.BX.locationSelectors[300].getNodeByLocationId(3146)
				//KladrJsObj.nobasemessage();
		}*/
	}
	function konturKladrInit() {
		KladrJsObj.map.geoObjects.events.add(['pixelboundschange'], function(e) {
			var currentCoords=KladrJsObj.map.geoObjects.get(0).geometry.getCoordinates();
			if(KonturKladrJsObjMapLastCoords && (KonturKladrJsObjMapLastCoords.join(',') == currentCoords.join(','))) {
				return true;
			}
			window.KonturKladrJsObjMapLastCoords=currentCoords;
			var districtData=false;
			window.KonturKladrJsObjMapCoords.forEach(function(data){
				if((data.coords[0] == currentCoords[0]) && (data.coords[1] == currentCoords[1])) {
					districtData=data;
				}
			});
	
			// @var function смена района
			function changeDistrict(districtData) {
				// район сменился
				if(window.KonturKladrJsObjMapLastDistrict != districtData.district.code) {
					window.KonturKladrJsObjMapLastDistrict=districtData.district.code;
					changeLocationValue(districtData.district.code);
				}
			}
	
			if(districtData) {
				changeDistrict(districtData);
			}
			else {
				$.get("http://data.esosedi.org/geocode/v1", {lng:"ru", point: currentCoords.join(',')}, function(response) {
					districtData={coords: currentCoords, district:{code: response.target.ll, name: response.names[response.target.ll].name}};
					window.KonturKladrJsObjMapCoords.push(districtData);
					changeDistrict(districtData);
				}, "json");
			}
		});		
	}
});
</script>

<?if(CSaleLocation::isLocationProEnabled()):?>

	<div style="display: none">
		<?// we need to have all styles for sale.location.selector.steps, but RestartBuffer() cuts off document head with styles in it?>
		<?$APPLICATION->IncludeComponent(
			"bitrix:sale.location.selector.steps", 
			".default", 
			array(
			'JS_CONTROL_GLOBAL_ID'=>100
			),
			false
		);?>
		<?$APPLICATION->IncludeComponent(
			"bitrix:sale.location.selector.search", 
			".default", 
			array(
				'JS_CONTROL_GLOBAL_ID'=>$arParams['LOCATIONS_JS_CONTROL_GLOBAL_ID']
			),
			false
		);?>
	</div>

<?endif?>
