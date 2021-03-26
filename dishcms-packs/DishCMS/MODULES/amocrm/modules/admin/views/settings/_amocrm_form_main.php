<?
use common\components\helpers\HArray as A;

$this->widget('\common\widgets\form\TextField', A::m(compact('form', 'model'), [
    'attribute'=>'account',
    'htmlOptions'=>['class'=>'form-control w50']
]));

$this->widget('\common\widgets\form\TextField', A::m(compact('form', 'model'), [
    'attribute'=>'redirect_uri',
    'htmlOptions'=>['class'=>'form-control w50']
]));

$this->widget('\common\widgets\form\TextField', A::m(compact('form', 'model'), [
    'attribute'=>'client_id',
    'htmlOptions'=>['class'=>'form-control w100']
]));

$this->widget('\common\widgets\form\TextField', A::m(compact('form', 'model'), [
    'attribute'=>'client_secret',
    'htmlOptions'=>['class'=>'form-control w100']
]));

$this->widget('\common\widgets\form\TextAreaField', A::m(compact('form', 'model'), [
    'attribute'=>'auth_code',
    'htmlOptions'=>['class'=>'form-control w100', 'style'=>'height:200px'],
    'note'=>'Обратите внимание, что код авторизации актуален только 20 минут после создания новой интеграции'
]));

?>
