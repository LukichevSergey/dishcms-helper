<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?
use \Kiora\Payments;

$rsUser = CUser::GetByID($USER->GetID()); 
$arUser = $rsUser->Fetch();
$arUser['name_print'] = ( $arUser['NAME']? $arUser['NAME'] : '' ) . ( $arUser['NAME'] && $arUser['LAST_NAME'] ? ' ' : '' ) . ( $arUser['LAST_NAME']? $arUser['LAST_NAME'] : '' );
$arUser["PERSONAL_PHONE"] = $arUser["PERSONAL_PHONE"] ? $arUser["PERSONAL_PHONE"]:$arUser["PERSONAL_MOBILE"] ? $arUser["PERSONAL_MOBILE"]:'';
$arUser['EMAIL'] = $arUser['EMAIL']?$arUser['EMAIL']:'';

$modal = $arParams['MODAL_FORM'] == 'Y' ? true : false ;

?>
<? if( $modal ){ ?>
<div class="payment-wrap">
	<a href="javascript:void(0);" class="button good showModal"><?=GetMessage('BUY')?></a>
	<div class="modal formModal">

		<!-- Modal content -->	
		<div class="modal-content slideDown">

			<div class="modal-header">
				<span class="closeModal">&times;</span>
				<h3><?=GetMessage('PAY_ORDER')?></h3>
			</div>
	<?}//MODAL_FORM?>
<div id="content">
	<h1>Оплата заявления об отказе от взаимодействия №<?=$arResult['ORDER']['ID']?></h1>
	<p>Сейчас Вы будете перенаправлены на страницу оплаты Вашего заявления об отказе от взаимодействия.</p>
	<p>Если этого не произошло, нажмите на кнопку "Оплатить".</p>
	<br/>
			<div class="form-body ki-yaka payment-system-<?=$arParams['PAYMENT_TYPE']?>">
	<?=( $arResult['DEMO_MODE'] ? ' <div style="color:red;" align="center">'.GetMessage('DEMO_MODE').'</div>' : '' )?>
				<form id="yform" action="<?=$arResult['POST_URL']?>" <? if( $modal ){ ?>class="modal-form"<?}?> method="post" >
					<input name="shopId" value="<?=$arResult['SHOP_ID']?>" type="hidden"/>
                    <input name="SITE_LID" value="<?=SITE_ID?>" type="hidden"/>
					<input name="scid" value="<?=$arResult['SC_ID']?>" type="hidden"/>
					<input name="sum" value="<?=$arParams['SUM']?>" type="hidden" />
					<input name="paymentType" value="<?=$arParams['PAYMENT_TYPE']?>" type="hidden"/>
					<input name="order_content" value="<?=($arResult['DEMO_MODE'] ? 'DEMO-MODE: ': '').$arParams['ITEM_NAME']?>" type="hidden" />
					<input name="order_amount" value="<?=$arParams['SUM']?>" type="hidden" />
					<input name="orderNumber" value="<?=$arResult['ORDER']['ID']?>" type="hidden" />
					<input name="customerNumber" value="" type="hidden" />
                    <input name="f_quantity" value="1" type="hidden" />
                    <input name="f_tax" value="<?=$arResult['CHECK_TAX']?>" type="hidden" />
                    <?if($arResult['SEND_CHECK']){?>
                        <input name="ym_merchant_receipt" value='' type="hidden"/>
                    <?}?>
								
					<input name="user_name" value="<?=$arResult['ORDER']['NAME']?>" required type="hidden" />
					<input name="user_phone" value="<?= $arResult['ORDER']['PROPERTIES']['PHONE']['VALUE'] ?>" type="hidden"/>
					<input name="user_email" value="<?= $arResult['ORDER']['PROPERTIES']['EMAIL']['VALUE'] ?>" type="hidden"/>
					
					<div class="modal-footer" style="justify-content:center;">
						<input type="submit" class="button good" value="Оплатить">
					</div>
				</form>
			</div><!--/.form-body-->
</div>
<script>//setTimeout(function(){$("#yform").submit();}, 2000);</script>
	<? if( $modal ){ ?>
		</div><!--/.modal-content-->

	</div><!--/#formModal-->
</div><!--/.payment-wrap-->

<?}?>
