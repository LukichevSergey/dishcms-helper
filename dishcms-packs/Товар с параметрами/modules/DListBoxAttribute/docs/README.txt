--------------------------------------
Использование модуля DListBoxAttribute
--------------------------------------

Все примеры приведены для модели DishCMS Product.

----------------
Настройка модели
----------------

1. Добавить в метод модели behaviors() поведения
Пример:
public function behaviors()
{
	return array(
		...
		'attributeColor' => array(
			'class' => '\DListBoxAttribute\components\behaviors\DListBoxAttributeBehavior',
			'name' => 'attributeColor',
			'attribute' => 'colors',
			'attributeOne' => 'color',
			'attributeListBox' => 'color',
			'title' => 'Цвета',
			'titleOne' => 'Цвет'
		),
		'attributeTSize' => array(
			'class' => '\DListBoxAttribute\components\behaviors\DListBoxAttributeBehavior',
			'name' => 'attributeTSize',
			'attribute' => 'tsizes',
			'attributeOne' => 'tsize',
			'attributeListBox' => 'tsize',
			'title' => 'Размеры',
			'titleOne' => 'Размер'
		)
	);
}

2. В конструктуре модели добавить поведения и вызвать метод поведения init(). 
Пример:

public function __construct($scenario='insert')
{
	$this->attachBehaviors($this->behaviors());
	$this->asa('attributeColor')->init();
	$this->asa('attributeTSize')->init();
	
	parent::__construct($scenario);
}

2. Добавить в модель соответствующие public свойства аттрибута одиночного значения.
Пример:
public $color;
public $tsize;

3. В правилах валидации rules(), добавить аттрибут в список safe.
Пример:
array('colors, tsizes', 'safe')

4. Добавить в метод модели attributeLabels() значения меток.
Пример:
public function attributeLabels()
{
	return array(
		...
		'color'=>'Цвет',
		'colors'=>'Цвета',
		'tsize'=>'Размер',
		'tsizes'=>'Размеры',
	);
}

5. Добавить в метод модели realtions() связи
Пример:
public function relations()
{
	return \CMap::mergeArray($this->attributeTSize->relations(), 
		\CMap::mergeArray($this->attributeColor->relations(), array(
        	...
        	// связи модели
	)));
}

---------------------------------------------------------------
Использование (виджеты)
---------------------------------------------------------------

1. В разделе администрирования в шаблоне формы создания/редактирования модели вставить виджет.
Пример:
<?php $this->widget('\DListBoxAttribute\widgets\admin\ListBoxWidget', array(
	'behavior' => $model->attributeColor,
	'form' => $form, 
	'model' => $model,
	// 'cssClass' => 'listBox'
)); ?> 

2. На странице товара, где необходимо отобразить выпадающий список, вставить виджет.
Пример:
<?php $this->widget('\DListBoxAttribute\widgets\DropDownListWidget', array(
	'behavior' => $product->attributeColor,
	'model' => $product,
	'prompt' => '-- Выберите цвет --',
	'promptAlert' => 'Вы не выбрали цвет',
	'cssClass'=>'drop-down-list'
)); ?>