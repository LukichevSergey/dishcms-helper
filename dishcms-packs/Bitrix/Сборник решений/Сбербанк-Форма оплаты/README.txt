1) Создать инфоблок со свойствами
Сумма (руб) / Число / AMOUNT
Телефон / Строка / PHONE
E-Mail / Строка / EMAIL
Статус / Список / STATUS (Минимальные значения: WAIT/Ожидает оплаты, PAID/Оплачено, FAIL/Отмена)
ID платежа / Строка / PAYMENT_ID

2) Почтовый тип (например)
KONTUR_PAYMENT_FORM / Платежная форма
#ORDER_ID# - Номер заказа
#STATUS# - Статус платежа
#DATE# - Дата платежа
#AMOUNT# - Сумма платежа
#NAME# - Имя плательщика
#PHONE# - Контактный телефон плательщика
#EMAIL# - Адрес электронной почты плательщика

en / Payment Form
#ORDER_ID# - Order ID
#STATUS# - Payment status
#DATE# - Payment date
#AMOUNT# - Amount
#NAME# - Name
#PHONE# - Phone
#EMAIL# - E-Mail

3) Почтовый шаблон (Новый платеж)
Новый платеж на сайте #SERVER_NAME#.

Статус платежа: #STATUS#
Сумма платежа: #AMOUNT#

Номер заказа: #ORDER_ID#
Дата платежа: #DATE#

Имя плательщика: #NAME#
Контактный телефон: #PHONE#
E-Mail: #EMAIL#

4) Почтовый шаблон (Оплачено)

Оплачен заказ на сайте #SERVER_NAME#.

Статус платежа: #STATUS#
Сумма платежа: #AMOUNT#

Номер заказа: #ORDER_ID#
Дата платежа: #DATE#

Имя плательщика: #NAME#
Контактный телефон: #PHONE#
E-Mail: #EMAIL#

5) Почтовый шаблон (Отмена платежа)

На сайте #SERVER_NAME# отменен платеж.

Статус платежа: #STATUS#
Сумма платежа: #AMOUNT#

Номер заказа: #ORDER_ID#
Дата платежа: #DATE#

Имя плательщика: #NAME#
Контактный телефон: #PHONE#
E-Mail: #EMAIL#

6) Добавить компонент формы оплаты на страницу и настроить параметры.

<?php $APPLICATION->IncludeComponent(
	"kontur.payments",
	"sberbank", 
	array(
	),
	false
); ?>

