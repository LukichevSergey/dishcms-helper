Подключение модуля Яндекс.Кассы (HTTP протокол)

Данная инструкция только для версии CMS более 2.5.31 (для более ранних версий необходимо заменить модуль common из текущией версии)

ПЕРЕЗАПИСЫВАТЬ ФАЙЛЫ ИЗ ПАПКИ "install" МОЖНО ТОЛЬКО ЕСЛИ НА ПРОЕКТЕ НЕ БЫЛИ ВНЕСЕНЫ ДОПОЛНИТЕЛЬНЫЕ ПРАВКИ В СЛЕДУЮЩИЕ ФАЙЛЫ:
- /protected/modules/admin/controller/DOrderController.php
- /protected/modules/DOrder/widgets/admin/actions/views/list.php
- /protected/modules/DOrder/widgets/actions/OrderWidget.php
В ПРОТИВНОМ СЛУЧАЕ, О ТОМ, КАКИЕ ВНОСИТЬ ДОПОЛНИТЕЛЬНЫЕ ПРАВКИ читать раздел "ПРАВКИ В ФАЙЛАХ МОДУЛЯ DORDER"

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
return array(
	'payment/hash/<hash>'=>'payment/index',
    'pay/payment_success'=>'payment/ymhttpSuccess',
    'pay/payment_fail'=>'payment/ymhttpFail',
    
*) Добавить параметры подключения в файл параметров системы (/protected/config/params.php)
return [
	...
	'payment'=>[
        'action'=>'/payment',
        'ymhttp'=>[
            //'action'=>'https://demomoney.yandex.ru/eshop.xml', // тестовый
            'action'=>'https://money.yandex.ru/eshop.xml',
            'shopId'=>'',
            'scid'=>'',
            //'scid'=>'', // тестовый
            'secretKey'=>'',
            //'delivery'=>['tax'=>1, 'title'=>'Доставка'], // при использовании доставки
            //'types'=>['card', 'emoney'], // типы платежей
            /*'types'=>[
            	'card',
            	'banking'=>['alfa', 'masterpass', 'psb'],
            	'emoney'=>['qiwi', 'yamoney']
            ]*/
            //'paymentType'=>'AC' // тип платежа по умолчанию
        ],
        'online'=>['On-line оплата']
    ],

! Установите значение параметра 'types'=>false - для активации режима выбора типа платежа по умолчанию.
- если параметр "types" будет закомментирован - будут отображены все доступные способы оплаты.

URL для уведомлений
https://biz-alliance.ru/payment/notif

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

Email для отправки реестров: почта_для_реестров
shopPassword: сгенерированный_пароль

*) При использовании доставки и для включения ее стоимости в отдельную графу необходимо добавить соответвующее получение в файле
/protected/modules/ecommerce/modules/ykassa/views/httpPayment/index.php
переменная $deliveryPrice
...
$deliveryPrice=0;

*) Добавить поле способа оплаты в форму оплаты (/cp/shop/orderFields)
Имя: payment
Подпись: Способ оплаты
Тип: группа переключателей
Обязательное поле: Да
Возможные значения: (на каждой новой строке, новый способ оплаты)
Значение оплаты через Яндекс-Кассу: "On-line оплата"
Если нужно другое название для оплаты, то необходимо заменить все значения "On-line оплата" в данной документации на соответствующее.

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

-----------------------------
ПРАВКИ В ФАЙЛАХ МОДУЛЯ DORDER
-----------------------------
1) Модель заказа (/protected/modules/DOrder/models/DOrder.php)
- добавить use
use common\components\helpers\HArray as A;

- наследовать модель от класа \common\components\base\ActiveRecord
class DOrder extends \common\components\base\ActiveRecord

- внести соотвествующие правки необходимые для корректной работы модели, наследуемой от \common\components\base\ActiveRecord
-- добавить метод
public function relations()
{
    return $this->getRelations([
    ]);
}

-- поправить метод scopes()
public function scopes()
{
    return $this->getScopes(array(
        'uncompleted' => array('condition' => 'completed<>1'),
        'payed'=>['condition'=>'paid=1']
    ));
}


-- поправить метод rules()
public function rules()
{
	return $this->getRules(array(
		...
		['paid', 'safe', 'on'=>'updatePaid'] <- добавить
	));
}

-- поправить метод attributeLabels
public function attributeLabels()
{
	return $this->getAttributeLabels(


1) Действие оформления заказа (/protected/modules/DOrder/widgets/actions/OrderWidget.php)
В методе public function run()
- добавить сценарий "payment"
	заменить
    $customerForm = new CustomerForm();
    на
    $customerForm = new CustomerForm('payment');
    
- закомментрировать строку
	заменить
 	$customerForm->paymentType=$payment; 
	$customerForm->payment=Y::param('payment.types.'.$payment);
	на
	$customerForm->paymentType=$payment; 
	//$customerForm->payment=Y::param('payment.types.'.$payment);
	
2) Если необходимо, чтобы онлайн-оплата была выбрана по умолчанию, необходимо добавить строку в файл /protected/modules/DOrder/widgets/views/customer_form.php 
foreach ($fields as $f) { ?>
	<? if($f['name'] == 'payment') $this->model->payment='On-line оплата'; ?>	
	
3) Файл шаблона отображения списка заказов (/protected/modules/DOrder/widgets/admin/actions/views/list.php)
- Закомментировать строки (если такие есть)
<? /* if($payment=A::rget($customer, 'payment.value')): 
	?><span><em><?=A::rget($customer, 'payment.label')?>:</em> <?=$payment?></span><?
endif; */ ?>

- в <tr class="head"> добавить колонку
<td>Статус</td>
<td style="width:30px">Оплачен</td> <- эту

- добавить выражение для дополнительного css-класса (если такого кода еще нет)
<tr class="order<?=D::c(($item->paid == 1), ' payment_complete')?> dorder-list-item" data-item="<?=$item->id?>">

- отобразить код заказа в колонке <td class="info" colspan="2">
<br/>Код заказа: <?=$item->hash?>
...

- добавить поле отображения оплаты
после
<td><div class="mark <?=(!$item->completed) ? 'marked' : 'unmarked'?>" data-item="<?=$item->id?>"></div></td>
добавить код ниже
<td align="center">
    <? if($item->in_paid):?>
    <span data-js="order-paid-<?=$item->id?>" class="label label-warning">в процессе</span>
    <? else: ?>
    <span data-js="order-paid-<?=$item->id?>" class="label label-<?=$item->paid ? 'success' : 'danger'?>"><?=$item->paid ? 'Да' : 'Нет'?></span>
    <br/><br/>
    <?=\CHtml::ajaxButton(
        'изменить',
        'dOrder/changePaid/'.$item->id,
        [
            'dataType'=>'json',
            'beforeSend'=>'js:function(){return confirm("Подтвердите изменение статуса платежа заказа #'.$item->id.'");}',
            'success'=>'js:function(r){if(r.success){'
                .'var $o=$("[data-js=\'order-paid-'.$item->id.'\']");'
                .'$o.removeClass(r.data.paid?"label-danger":"label-success");'
                .'$o.addClass(r.data.paid?"label-success":"label-danger");'
                .'$o.text(r.data.paid?"Да":"Нет");'
                .'}}'
        ],
        ['class'=>'btn btn-xs btn-default']
    );?>
    <? endif; ?>
</td>

4) Добавить действие в файл контроллера (/protected/modules/admin/controller/DOrderController.php)
- добавить use
use common\components\helpers\HAjax;
use DOrder\models\DOrder;

- в фильтр добавить действие
заменить
'ajaxOnly + completed, comment, delete'
на
'ajaxOnly + completed, comment, delete, changePaid'

- добавить действие
	public function actionChangePaid($id)
    {
        $ajax=HAjax::start();
        if($order=DOrder::model()->findByPk($id)) {
            $order->paid=($order->paid == 1) ? 0 : 1;
            $ajax->data=['paid'=>$order->paid];
            $ajax->success=$order->save();
        }
        $ajax->end();
    }


