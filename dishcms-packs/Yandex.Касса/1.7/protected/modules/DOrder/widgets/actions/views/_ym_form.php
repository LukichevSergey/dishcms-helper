<?php
/** @var \DOrder\models\YandexForm $model */
$q = function($text) {
	return str_replace('"', '\'', $text);
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<title>Перенаправление на сервис Яндекс.Деньги</title>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
</head>
<body bgcolor="#fff">
	<table width="100%" height="100%" border=0 style="position:absolute;">
		<tr>
			<td align="center" valign="middle" style="font-size:18px;color:#999;">
			Сейчас Вы будете перенаправлены на сервис 
			Яндекс.Деньги для оплаты Вашего заказа.
			</td>
		</tr>
	</table>
	<form name=ShopForm method="POST" action="https://money.yandex.ru/eshop.xml">
	<input type="hidden" name="scid" value="<?php echo $q($model->scid); ?>">
	<input type="hidden" name="ShopID" value="<?php echo $q($model->ShopID); ?>">
	<input type="hidden" name="shopSuccessURL" value="<?php echo 'http://' . Yii::app()->request->serverName . $q($model->shopSuccessURL); ?>">
	<input type="hidden" name="shopFailURL" value="<?php echo 'http://' . Yii::app()->request->serverName . $q($model->shopFailURL); ?>">
	<input type="hidden" name="CustomerNumber" value="<?php echo $q($model->CustomerNumber); ?>">
	<input type="hidden" name="orderNumber" value="<?php echo $q($model->orderNumber); ?>">
	<input type="hidden" name="Sum" value="<?php echo $q($model->Sum); ?>">
	<input type="hidden" name="CustName" value="<?php echo $q($model->CustName); ?>">
	<input type="hidden" name="CustAddr" value="<?php echo $q($model->CustAddr); ?>">
	<input type="hidden" name="CustEMail" value="<?php echo $q($model->CustEMail); ?>">
	<input name="paymentType" value="<?php echo $q($model->paymentType); ?>" type="radio" checked="checked" style="display:none"/>
	<textarea name="OrderDetails" style="display:none;width:0;height:0"><?php echo CHtml::encode($model->OrderDetails); ?></textarea>
	</form>
	<script>
	$(document).ready(function() {
		$('form[name="ShopForm"]').submit();
	});
	</script>
</body>
</html>