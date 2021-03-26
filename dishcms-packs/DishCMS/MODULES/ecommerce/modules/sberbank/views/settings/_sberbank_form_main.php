<?
use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;

$this->widget('\common\widgets\form\CheckboxField', A::m(compact('form', 'model'), ['attribute'=>'is_test_mode']));

$this->widget('\common\widgets\form\DropDownListField', A::m(compact('form', 'model'), [
    'attribute'=>'tax_system',
    'data'=>$model->taxSystems(),
    'htmlOptions'=>['class'=>'form-control w50', 'empty'=>'-- не указано --'],
    'note'=>'для фискализации (для магазинов без настроек фискализации параметр не обязателен)'
]));

$this->widget('\common\widgets\form\TextField', A::m(compact('form', 'model'), ['attribute'=>'merchant']));

$this->widget('\common\widgets\form\TextField', A::m(compact('form', 'model'), [
    'attribute'=>'token', 
    'note'=>'Является более приоритеным, чем авторизация по логину и паролю.<br/>Оставьте пустым, если авторизация происходит по логину и паролю.'
]));

$this->widget('\common\widgets\form\TextField', A::m(compact('form', 'model'), ['attribute'=>'login']));
$this->widget('\common\widgets\form\TextField', A::m(compact('form', 'model'), ['attribute'=>'password']));

$this->widget('\common\widgets\form\TextField', A::m(compact('form', 'model'), [
    'attribute'=>'success_url',
    'note'=>'По умолчанию: ' . Y::createAbsoluteUrl('/ecommerce/sberbank/pay/success')
]));
$this->widget('\common\widgets\form\TextField', A::m(compact('form', 'model'), [
    'attribute'=>'fail_url',
    'note'=>'По умолчанию: ' . Y::createAbsoluteUrl('/ecommerce/sberbank/pay/fail')
]));

?>
