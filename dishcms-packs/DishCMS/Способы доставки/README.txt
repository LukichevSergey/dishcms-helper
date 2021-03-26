Добавление типа поля в форму заказа "Доставка" (delivery)

1) файл protected\modules\DOrder\models\OrderCustomerFields.php

1.1) Добавить константу 
const TYPE_DELIVERY = 'delivery';

1.2) Добавить запись для списка типов
public static function getTypes() {
    ...
    self::TYPE_DELIVERY => 'доставка',
    
2) файл protected\modules\DOrder\models\CustomerForm.php
2.1) Перевести модель на класс \common\components\base\FormModel (если она еще не переведена)
use common\components\helpers\HArray as A;
use common\components\base\FormModel;
class CustomerForm extends FormModel
в том числе поправить методы rules(), attributeLabels(), добавив обертку getRules() и getAttributeLabels()
необязательно аналогично для scopes() и relations()

2.2) Добавить поведеление
public function behaviors() 
{
    return A::m(parent::behaviors(), [
        'deliveryBehavior'=>'\ecommerce\modules\order\behaviors\DeliveryOrderCustomerFieldBehavior'
    ]);
}

3) Добавить виджет поля в форму protected\modules\DOrder\widgets\views\customer_form.php
    ...
    case OCF::TYPE_DELIVERY:
        $this->widget('\ecommerce\modules\order\widgets\delivery\OrderCustomerField', [
            'behavior'=>$this->model->deliveryBehavior,
            'form'=>$form
        ]);
    break;

4) Подключить CRUD для пунктов самовывоза protected\config\crud.php
return [
    ...
    'ecommerce.modules.order.config.crud',

5) Добавить пункт меню в раздел администрирования protected\modules\admin\config\menu.php
return [
    ...
    'catalog'=>[
        ...
        ['label'=>'', 'itemOptions'=>['class'=>'divider'], 'visible'=>'divider'],
		HCrud::getMenuItems(Y::controller(), 'delivery_types', 'crud/index', true),
		HCrud::getMenuItems(Y::controller(), 'pickup_points', 'crud/index', true),
        ...
]


protected\modules\DOrder\widgets\actions\OrderWidget.php
вставить
    $customerForm->deliveryBehavior->calcDiscountPrice(\Yii::app()->cart->getTotalPrice(), true);
перед строкой
    $order->customer_data = $customerForm->getAttributes(null, true, true, true);


внести исключения и правки в файлы почтовых уведомлений
protected\modules\DOrder\views\_email\
$discount = isset($customer['delivery_discount']['value']) ? (float)$customer['delivery_discount']['value'] : 0;
...
in_array($key, ['paymentType', 'privacy_policy', 'delivery_type_id', 'delivery_discount', 'delivery_discount_format', 'delivery_email', 'delivery_pickup_point_id'])) continue;