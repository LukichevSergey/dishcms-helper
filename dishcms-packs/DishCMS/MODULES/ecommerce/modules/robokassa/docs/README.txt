Подключение модуля Robokassa

-------------------------------------------
-- УСТАНОВКА
-------------------------------------------

1) Добавить модуль в конфигурационный файл модуля ecommerce
\protected\modules\ecommerce\config\main.php
    ...
    'robokassa'=>['class'=>'\ecommerce\modules\robokassa\RobokassaModule'],

2) Добавить в /protected/config/settings.php
    ...
    'ecommerce.modules.robokassa.config.settings',

3) Добавить в /protected/config/crud.php
    ...
    'ecommerce.modules.robokassa.config.crud',

4) Добавить пункт меню в /protected/modules/admin/config/menu.php
    ...
    HCrud::getMenuItems(Y::controller(), 'robokassa_payments', 'crud/index', true),


2) Авторизоваться под суперпользователем и перейти в режим разработки.

3) Перейти по ссылке /ecommerce/robokassa/admin





* Настройки для Яндекс.Кассы

checkUrl: https://домен/payment/ymcheck
avisoUrl: https://домен/payment/ymaviso
shopSuccessUrl: https://домен/payment/ymsuccess
shopFailUrl: https://домен/payment/ymfail

Я буду проводить тестовые платежи: Да

checkUrl (демо): https://домен/payment/ymdemocheck
avisoUrl (демо): https://домен/payment/ymdemoaviso
shopSuccessUrl (демо): https://домен/payment/ymdemosuccess
shopFailUrl (демо): https://домен/payment/ymdemofail

-------------------------------------------


ПЕРЕЗАПИСЫВАТЬ ФАЙЛЫ ИЗ ПАПКИ "install" МОЖНО ТОЛЬКО ЕСЛИ НА ПРОЕКТЕ НЕ БЫЛИ ВНЕСЕНЫ ДОПОЛНИТЕЛЬНЫЕ ПРАВКИ В СЛЕДУЮЩИЕ ФАЙЛЫ:
- /protected/controllers/PaymentController.php

ОБЯЗАТЕЛЬНО ПРОВЕРИТЬ, что файла не существует (чтобы не перезаписать) /protected/controllers/PaymentController.php

1) Скопировать файлы из папки install в корень сайта.

2) Подулючить модуль в файле конфигурации модуля ECommerce (/protected/modules/ecommerce/config/main.php)
return [
    'modules'=>[
    ...
    	'ykassa'
    	
*) Подключить модуль и добавить алиас в основном файле конфигурации (/protected/config/defaults.php)
	'aliases'=>array(
		...
        'ykassa'=>'application.modules.ecommerce.modules.ykassa',
    ),
    ...
    'modules'=>array(
    	...
    	'ecommerce', - данный модуль уже может быть, но закомментирован.


*) Добавить правила для маршрутизатора (/protected/config/urls.php)
- для формы заказа
return array(
	'payment/hash/<hash>'=>'payment/index',
    'payment/<action:\w+>'=>'payment/<action>',

- для формы произвольной оплаты
	'payment'=>'paymentCustom/index',
	'payment/<action:\w+>'=>'paymentCustom/<action>',

*) Добавить пункт меню в /admin/config/menu.php
\ykassa\components\helpers\HYKassa::getAdminMenuItem()

*) Для произвольной оплаты
- добавить в /protected/config/crud.php
	'ykassa.config.crud',

*) Добавить настройки в /protected/config/settings.php
	'ykassa.config.settings',

*) При необходимости добавить стили
#DOrder_models_CustomerForm_payment {
    width: 100% !important;
    label {
        display: inline-block !important;
        width: 85% !important;
    }
}

.payment-ym-button {
    cursor: pointer;
    background: #FFCC33;
    border: 0;
    outline: 0;
    padding: 10px 20px !important;
}

*) Добавить поле способа оплаты в форму оплаты (/cp/shop/orderFields)
Имя: payment
Подпись: Способ оплаты
Тип: группа переключателей
Обязательное поле: Да
Возможные значения: (на каждой новой строке, новый способ оплаты)
Значение оплаты через Яндекс-Кассу: "Онлайн-оплата"
Значение можно указать любое, но при этом необходимо установить соответсвующие значение в настройках 
Яндекс.Кассы в поле: Значение онлайн-оплаты для поля "Способ оплаты"


*) Действие оформления заказа (/protected/modules/DOrder/widgets/actions/OrderWidget.php)
- добавить перед классом 
use ykassa\components\helpers\HYKassa;
class OrderWidget extends BaseActionWidget // перед этой строкой
{

- добавить сценарий "payment"
    заменить
    $customerForm = new CustomerForm();
    на
    $customerForm = new CustomerForm('payment');

- заменить аналогичный блок на
	if($customerForm->scenario=='payment') {
		$payment=$customerForm->payment;
		$customerForm->paymentType=$payment;
		$customerForm->payment=$payment;
	}

- заменить аналогичный блок на
	if(($customerForm->scenario == 'payment') && HYKassa::isOnlinePaymentType($customerForm->paymentType)) {
		$this->owner->redirect($this->owner->createUrl('/payment', ['hash'=>$order->hash]));
	}

*) Формирование параметров для чека и позиции доставки для заказа формируется в файле
/protected/modules/ecommerce/modules/ykassa/views/httpPayment/index.php
Может потребоваться редаткирование при торговых предложениях

*) Формирование параметров для чека и позиции доставки для произвольной оплаты формируется в файле
/protected/modules/ecommerce/modules/ykassa/views/httpPaymentCustom/_payment_form.php

---------------------------------------------------------------

Для относительно старых версий, может потребовать только:

*) Добавить в файл /protected/modules/DOrder/widgets/views/customer_form.php
	case OCF::TYPE_RADIOBUTTON : // добавить после данной строки
	   if(count($f['values']) === 1) { $this->model->{$f['name']}=reset($f['values']); 

*) Настройки добавить явно в файл /protected/config/settings.php
	use ykassa\components\helpers\HYKassa;
	...
	'ykassa'=>[
        'class'=>'\ykassa\models\YKassaSettings',
        'title'=>'Настройки Яндекс.Кассы',
        'menuItemLabel'=>'Яндекс.Касса',
        'breadcrumbs'=>HYKassa::isCustomForm() ? ['История платежей'=>'/cp/crud/index?cid=ykassa_custom_payments'] : [],
        'viewForm'=>'ykassa.views.settings._ykassa_form'
    ],
