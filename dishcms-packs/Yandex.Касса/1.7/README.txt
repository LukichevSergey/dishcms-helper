Установка и настройка Яндекс.Касса для Дишмана версии >=1.7

С пунктом "оплата наличными"
1. В config/defaults.php в раздел 'params'

'payment' => array(
	'yandex' => array(
		'scid' => <scid>,
		'ShopID' => <ShopID>
	)
),

2. Добавить стили 
/** Yandex.Kassa */
img.dorder-payment-yandex-btn {
	width: 36px;
	position: absolute;
	border: 2px solid #000;
	border-radius: 5px;
}

#dorder-yandex-form .row.buttons {
	position: relative;
	margin-top: 20px;
}

.yandex-submit-btn {
	width: 150px;
	height: 40px;
	color: #fff;
	vertical-align: middle;
	font-size: 19px !important;
	padding-left: 36px !important;
	padding-top: 8px !important;
	border-radius: 5px;
	border: 2px solid rgba(0, 0, 0, 0.48);
	background: #f9a764; /* Old browsers */
	background: -moz-linear-gradient(top,  #f9a764 0%, #ef581c 100%); /* FF3.6+ */
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#f9a764), color-stop(100%,#ef581c)); /* Chrome,Safari4+ */
	background: -webkit-linear-gradient(top,  #f9a764 0%,#ef581c 100%); /* Chrome10+,Safari5.1+ */
	background: -o-linear-gradient(top,  #f9a764 0%,#ef581c 100%); /* Opera 11.10+ */
	background: -ms-linear-gradient(top,  #f9a764 0%,#ef581c 100%); /* IE10+ */
	background: linear-gradient(to bottom,  #f9a764 0%,#ef581c 100%); /* W3C */
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f9a764', endColorstr='#ef581c',GradientType=0 ); /* IE6-9 */
	
	&:focus {
		outline: 0 !important;
	}
	
	&:active {
		color: rgba(255, 255, 255, 0.75);
		background: #ef581c; /* Old browsers */
		background: -moz-linear-gradient(top,  #ef581c 0%, #f9a764 100%); /* FF3.6+ */
		background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#ef581c), color-stop(100%,#f9a764)); /* Chrome,Safari4+ */
		background: -webkit-linear-gradient(top,  #ef581c 0%,#f9a764 100%); /* Chrome10+,Safari5.1+ */
		background: -o-linear-gradient(top,  #ef581c 0%,#f9a764 100%); /* Opera 11.10+ */
		background: -ms-linear-gradient(top,  #ef581c 0%,#f9a764 100%); /* IE10+ */
		background: linear-gradient(to bottom,  #ef581c 0%,#f9a764 100%); /* W3C */
		filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ef581c', endColorstr='#f9a764',GradientType=0 ); /* IE6-9 */
	}
}

3. Скопировать файлы.

4. !!! Раскомментировать нужные типы платежей в методе YandexForm::getPaymentTypes()
\protected\modules\DOrder\models\YandexForm.php