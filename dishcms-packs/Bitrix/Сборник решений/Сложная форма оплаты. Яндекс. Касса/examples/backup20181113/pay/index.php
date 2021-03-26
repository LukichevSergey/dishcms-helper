<?php   include('config.php'); ?>

<form action="https://money.yandex.ru/eshop.xml" method="post">
	<input name="paymentType" value="" type="hidden">
	<input name="shopId" value="<?php echo $configs['shopId'] ?>" type="hidden"/>
	<input name="scid" value="<?php echo $configs['scId'] ?>" type="hidden"/>
	<input name="sum" value="10"/>
	<input name="customerNumber" value="1"/>
	<input name="cps_phone" value="79529281590"/>
	<input type="submit" value="Заплатить"/>
</form>
