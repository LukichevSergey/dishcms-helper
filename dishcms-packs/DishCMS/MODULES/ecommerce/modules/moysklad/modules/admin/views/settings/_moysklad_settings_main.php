<?php
/** @var \CActiveForm $form */
/** @var \ecommerce\modules\moysklad\models\MoySkladSettings $model */
use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use ecommerce\modules\moysklad\components\helpers\HMoySklad;
use MoySklad\Exceptions\ApiResponseException;

Y::module('ecommerce.moysklad');

$this->widget('\common\widgets\form\TextField', A::m(compact('form', 'model'), [
    'attribute'=>'login',
]));

$this->widget('\common\widgets\form\PasswordField', A::m(compact('form', 'model'), [
    'attribute'=>'password',
]));

/* try { /**/
$this->widget('\common\widgets\form\DropDownListField', A::m(compact('form', 'model'), [
    'attribute'=>'organization',
    'data'=>HMoySklad::getOrganizationListData(),
    'htmlOptions'=>['class'=>'form-control w50', 'empty'=>'-- выберите организацию --'],
    'note'=>'Если организация не задана, то выгрузка заказов <span class="bg-danger">производиться не будет</span>'
]));

$this->widget('\common\widgets\form\DropDownListField', A::m(compact('form', 'model'), [
    'attribute'=>'store',
    'data'=>HMoySklad::getStoreListData(),
    'htmlOptions'=>['class'=>'form-control w50', 'empty'=>'-- выберите склад --'],
    'note'=>'Если склад не задан, то выгрузка остатоков товара произведена не будет'
]));

$this->widget('\common\widgets\form\TextField', A::m(compact('form', 'model'), [
    'attribute'=>'price_type',
    'note'=>'Если не задано, то для товара будет выбрана максимально доступная цена'
]));
/*
} catch(ApiResponseException $e) {
    echo \CHtml::tag(
        'div', 
        ['class'=>'row'], 
        (($model->login || $model->password) ? \CHtml::tag('div', ['class'=>'alert alert-danger'], 'Не удалось соединиться с сервисом "Мой Склад"') : '')
        . \CHtml::tag('div', ['class'=>'alert alert-info'], 'Для дополнительных настроек укажите достоверный доступ к сервису "Мой Склад" и сохраните настройки')
    );
}
/**/

$this->widget('\common\widgets\form\TextField', A::m(compact('form', 'model'), [
    'attribute'=>'secure',
]));

$this->widget('\common\widgets\form\NumberField', A::m(compact('form', 'model'), [
    'attribute'=>'limit',
    'htmlOptions'=>['class'=>'form-control w10']
]));

$this->widget('\common\widgets\form\CheckboxField', A::m(compact('form', 'model'), [
    'attribute'=>'normalize_categories',
    'note'=>'Если отключено, уже существующая структура категорий будет оставлена без изменений'
]));

