<?define('NEED_AUTH', true);
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');
$APPLICATION->SetTitle('Список ценников');
?>
<? $APPLICATION->IncludeComponent('kontur:checkprice.pricetag', '', [
 
], false, ['HIDE_ICONS'=>'Y']); ?>
<?require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');?>