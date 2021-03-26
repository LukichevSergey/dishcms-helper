Установка
-------------

В нужной модели (например Product) 
Добавляем:

----------
1. Добавить поведение:

public function behaviors()
{
	return array(
		'dynamicAttributeBehavior'=>array(
			'class'=>'\ext\D\dynamicAttribute\behaviors\DynamicAttributeBehavior',
			'attribute'=>'<имя аттрибута>'
		)	
	);
}   

2. Примечание: Добавить, если нет, в метод модели beforeSave().
parent::beforeSave();

3. Добавить аттрибут, в правила валидации, как безопасный
пример:
public function rules() {
	return array(
		array('<имя аттрибута>', 'safe')
   	);
}
------------
Необязательно, но часто востребовано
------------
3. Добавить в модель attributeLabels()
пример:
'<имя аттрибута>'=>'Дополнительные характеристики'

----------
Использование
----------
1. Получение значений
$model->dynamicAttributeBehavior->get()
$model->dynamicAttributeBehavior->getActive()

2. Установка значений
$model->dynamicAttributeBehavior->set($array)

3. Виджет
$this->widget('\ext\D\dynamicAttribute\widgets\DynamicAttributeWidget', array(
	'behavior' => $model->dynamicAttributeBehavior,
	'attribute' => '<имя аттрибута>',
	'header'=>array('title'=>'Название', 'value'=>'Значение'),
	'hideAddButton'=>true,
	'readOnly'=>array('title'),
	'default' => array(
		array('title'=>'', 'value'=>'')
	)
);

Часто используемый код для вставки виджета в _form_product.php
<div class="row">
	<?php echo $form->labelEx($model, 'props_data'); ?>
	<?php $this->widget('\ext\D\dynamicAttribute\widgets\DynamicAttributeWidget', array(
		'behavior' => $model->dynamicAttributeBehavior,
		'attribute' => '<имя аттрибута>',
		'header'=>array('title'=>'Название', 'value'=>'Значение'),
		'default' => array(
			array('title'=>'', 'value'=>''),
		)
	));?>
</div>