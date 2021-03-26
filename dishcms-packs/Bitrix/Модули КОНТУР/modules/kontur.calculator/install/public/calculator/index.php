<?
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');
$APPLICATION->SetTitle('Калькулятор');
?>
<? $APPLICATION->IncludeComponent('kontur:calculator', '', [
 
]); ?>
<?require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');?>