<?
$IS_AJAX = false;
if( isset($_SERVER['HTTP_X_REQUESTED_WITH']) || $_REQUEST['AJAX_CALL']=='Y' )
{
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
	$IS_AJAX = true;
} else {
	require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');
	$APPLICATION->SetTitle("Узнать цену");
}
?> 

<?$APPLICATION->IncludeComponent("kontur:feedback.findoutprice", ".default", array(), false);?>

<?if(!$IS_AJAX):?>
<?require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');?>
<?endif;?>
