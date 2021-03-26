
Тип почтового сообщения KONTUR_PAYMENT_FORM
ru: Платежная форма
#SUBJECT# - Тема письма
#ORDER_ID# - Номер заказа
#PAYMENT_STATUS# - Статус платежа
#DATE# - Дата платежа
#AMOUNT# - Сумма платежа
#PROMOCODE# - Промокод
#NAME# - Имя плательщика
#PHONE# - Контактный телефон плательщика
#EMAIL# - Адрес электронной почты плательщика

en: Payment Form
#SUBJECT# - Subject
#ORDER_ID# - Order ID
#PAYMENT_STATUS# - Payment status
#DATE# - Payment date
#AMOUNT# - Amount
#PROMOCODE# - Promocode
#NAME# - Name
#PHONE# - Phone
#EMAIL# - E-Mail

Шаблон сообщения (тема #SUBJECT#)
<h1>Изменен статус заявки №#ORDER_ID# с сайта #SITE_NAME#.</h1>
 <br>
 <b>Номер заказа:</b> #ORDER_ID#<br>
 <b>Дата заказа:</b> #DATE#<br>
 <b>Статус платежа:</b> #PAYMENT_STATUS#<br>
 <b>Сумма платежа:</b> #AMOUNT# руб.<br>
 <br>
 <b>Промокод:</b> #PROMOCODE#<br>
 <br>
 <b>ФИО:</b> #NAME#<br>
 <b>Контактный телефон:</b> #PHONE#<br>
 <b>E-Mail:</b> #EMAIL#<br>
 <br>
 <a href="https://#SERVER_NAME#/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=66&type=requests&ID=#ORDER_ID#&lang=ru&find_section_section=-1&WF=Y">Перейти к более подробной информации</a>


Клиенту (с файлом вложений)
#MAIL_TO#

#SUBJECT#

<h1>Ваш заказ на сайте #SITE_NAME# оплачен.</h1>
 <br>
 <b>Номер заказа:</b> #ORDER_ID#<br>
 <b>Дата заказа:</b> #DATE#<br>

Документ Вашего заявления прикреплен к данному письму.
