-------------------------------------------------------------------
--
-- Подключение модуля Яндекс.Кассы (API протокол) 
-- для версий >= 2.28.8
--
-------------------------------------------------------------------
  * для старых версий 2.x (с уже установленным модулем ecommerce) 
  * необходимо обновить модуль common
-------------------------------------------------------------------

ВАЖНО!

Если нужно менять логику обработки платежей, формирования чека, 
обработки статусов заказ или другую доступную информармацию в 
конфигурации обработки платежей, то необходимо скопировать текущий
конфигурационный файл из 
protected\modules\ecommerce\modules\ykassa\config\api\ecommerce_order.php
в папку локальных конфигураций 
protected\config\ykassa\ecommerce_order.php

Если изменить наименование (напр, my_ecommerce_order.php), то 
необходимо будет изменить имя конфигурации в систенмых настройках 
яндекс.кассы в разделе администрирования сайта

-------------------------------------------------------------------

1) Скопировать файлы из папки install в корень сайта.

2) Удалить файл .installed в папке protected\runtime\, чтобы накатились миграции в базу данных

3) Подключить модуль в файле конфигурации модуля ECommerce [/protected/modules/ecommerce/config/main.php]

return [
    ...
    'modules'=>[
        ...
        'ykassa'
    ],
    ...
];

4) Добавить алиасы в основном файле конфигурации [/protected/config/defaults.php]

return [
    ...
    'aliases'=>array(
        ...
        'ykassa'=>'application.modules.ecommerce.modules.ykassa',
        'YandexCheckout'=>'ykassa.vendor.YandexCheckout'
    ),
    ...
];

5) Добавить правила для маршрутизатора (в начало) [/protected/config/urls.php]

return [
    'payment/<id>'=>'yandexPayment/index',
    'payment/<action:\w+>/<id>'=>'yandexPayment/<action>',
    ...
];

6) Добавить пункт меню в раздел администрирования [protected\modules\admin\config\menu.php]

return [
    ...
    'modules'=>array_merge([
        \ykassa\components\helpers\HYKassa::getAdminMenuItem(),
        ...
    
7) Добавить CRUD конфигурацию [/protected/config/crud.php]

return [
    ...
    'ykassa.config.crud',
];

8) Добавить настройки [/protected/config/settings.php]

return [
    ...
    'ykassa.config.settings',
];


9) Добавить в виджет оформления заказа [protected\modules\DOrder\widgets\actions\OrderWidget.php]
(в том месте, где нужно перенаправить на страницу оплаты)

Обычно после строки
HEvent::raise('OnDOrderNewOrderSuccess', [
...
]);

\ykassa\components\helpers\HYKassa::checkOnlinePayment($customerForm->payment, ['hash'=>$order->hash]);

10) Рекомендуется добавить для обновления статуса платежа в файл
protected\modules\ecommerce\modules\order\modules\admin\views\default\_orders_gridview.php

if((bool)Y::module('ecommerce.ykassa')) {
    $columns[]=[
        ...
        'value'=>function($data, $index, $column) {
            \ykassa\components\helpers\HYKassa::checkPaymentStatus($data->yandex_payment_id);
            ...


-------------------------------------------------------------------
--
-- ДОПОЛНИТЕЛЬНО
--
-------------------------------------------------------------------

Можно вывести пункт настроек Яндекс.Кассы в выпадающее меню "Настройки" [protected\modules\admin\views\layouts\main.php]
    <li><a href="/admin/settings/ykassa">Яндекс.Касса</a></li>

-----------------------------

Для радиокнопок в форме оформления заказа можно добавить стили
#DOrder_models_CustomerForm_payment {
    width: 100% !important;
    label {
        display: inline-block !important;
        width: 85% !important;
    }
}

-----------------------------

Добавить в файл /protected/modules/DOrder/widgets/views/customer_form.php
    case OCF::TYPE_RADIOBUTTON : // добавить после данной строки
       if(count($f['values']) === 1) { $this->model->{$f['name']}=reset($f['values']); 


-----------------------------

Для старых версих CMS может потребоваться добавить настройки Яндекс.Кассы 
в файл файл /protected/config/settings.php
return [
    ...
    'ykassa'=>[
        'class'=>'\ykassa\models\YKassaSettings',
        'title'=>'Настройки Яндекс.Кассы',
        'menuItemLabel'=>'Яндекс.Касса',
        'breadcrumbs'=>[
            'История платежей'=>'/cp/crud/index?cid=ykassa_payments'
        ],
        'viewForm'=>'ykassa.views.settings._ykassa_form'
    ],
];
