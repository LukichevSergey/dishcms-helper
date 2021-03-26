<?php
/**
 * Тип поля "RAW" (как есть).
 *
 * @var \common\ext\dataAttribute\widgets\DataAttribute $this
 * @var string $name имя поля
 * @var string $value значение поля
 * @var string|array $data данные типа
 * @var string $view шаблон отображения
 * @var array $params дополнительные параметры
 * @var boolean $isTemplate генерируется шаблон нового элемента.
 */
use common\components\helpers\HArray as A;
use common\components\helpers\HHash;

$uid=$isTemplate ? '{{daw-index}}' : HHash::u('f');
$fGetId=function($name) use ($uid) { return "field_option_{$name}_{$uid}"; };

echo \CHtml::label('По умолчанию:', $fGetId('default'));
echo \CHtml::textField($name.'[default]', A::get($value, 'default'), [
    'id'=>$fGetId('default'),
    'class'=>'form-control w100', 
    'style'=>'height:16px;min-height:25px;font-size:12px;padding:4px;'    
]);

echo \CHtml::checkBox($name.'[required]', A::get($value, 'required'), ['class'=>'inline', 'id'=>$fGetId('required')]);
echo \CHtml::label('Обязательное', $fGetId('required'), ['class'=>'inline', 'title'=>'Является обязательным полем для заполнения']);
echo '<br/>';

echo \CHtml::checkBox($name.'[show]', A::get($value, 'show', $isTemplate ? 1 : 0), ['class'=>'inline', 'id'=>$fGetId('show')]);
echo \CHtml::label('Отображать', $fGetId('show'), ['class'=>'inline', 'title'=>'Отображать поле в виджете формы']);
echo '<br/>';

echo \CHtml::checkBox($name.'[editable]', A::get($value, 'editable'), ['class'=>'inline', 'id'=>$fGetId('editable')]);
echo \CHtml::label('Редактируемое', $fGetId('editable'), ['class'=>'inline', 'title'=>'Значение разрешено изменять']);
echo '<br/>';

echo \CHtml::checkBox($name.'[email]', A::get($value, 'email', $isTemplate ? 1 : 0), ['class'=>'inline', 'id'=>$fGetId('email')]);
echo \CHtml::label('В уведомлении', $fGetId('email'), ['class'=>'inline', 'title'=>'Отображать в почтовом уведомлении']);
?>