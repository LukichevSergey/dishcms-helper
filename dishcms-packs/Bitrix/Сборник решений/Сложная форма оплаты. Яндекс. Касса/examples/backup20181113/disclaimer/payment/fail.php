<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle('Заявление об отказе от взаимодействия');
?>
<div id="content">
	<h1>Заказ успешно оплачен</h1>
	<div id="payment-fail">
		<div class="payment-fail">
		    <div class="payment-fail__text">
		        <p>Произошла ошибка при проведении Вашего платежа.<br/>
		        Если деньги были списаны обратитесь, пожалуйста, в техническую службу Вашего банка.</p>
		    </div>
		</div>
	</div> 
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
