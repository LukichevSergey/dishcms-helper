<?
$MESS["KONTUR_CHECK_PRICE_PARTNER_NAME"]="Kontur";
$MESS["KONTUR_CHECK_PRICE_MODULE_NAME"]="Проверка изменения цен на сайте";
$MESS["KONTUR_CHECK_PRICE_MODULE_DESCRIPTION"]="Модуль проверки изменения цен на сайте";
$MESS["KONTUR_CHECK_PRICE_INSTALL_TITLE"]="Установка модуля проверки изменения цен";
$MESS["KONTUR_CHECK_PRICE_EVENT_TYPE_PRICELIST_NAME"]="Изменения цен на сайте";
$MESS["KONTUR_CHECK_PRICE_EVENT_TYPE_PRICELIST_DESCRIPTION"]='
#PERIOD# - Период за который сформирована таблица изменения цен
#PRICELIST# - Таблица изменения цен
';
$MESS["KONTUR_CHECK_PRICE_EVENT_TEMPLATE_PRICELIST_SUBJECT"]="Изменения цен #PERIOD# на сайте #SITE_NAME#";
$MESS["KONTUR_CHECK_PRICE_EVENT_TEMPLATE_PRICELIST_MESSAGE"]='
    <h1 style="font-size:16px;">Изменения цен #PERIOD# на сайте &laquo;#SITE_NAME#&raquo;</h1>
    <p style="margin:0;font-size:13px;margin-bottom:20px;">
        <a href="#SERVER_NAME#/bitrix/admin/kontur_checkprice_admin.php" target="_blank">Перейти на страницу списка изменения цен</a>
    </p>
    #PRICELIST#
';
?>