На примере добавления для товара (\Product) с атрибутами car_brand_id и car_model_id

1) Скопировать конфигурации в папку /protected/config/crud

2) Добавить в /protected/config/crud.php
return [
  ...,
  'car_brands'=>'application.config.crud.car_brands',
  'car_models'=>'application.config.crud.car_models',
];

3) Добавить пункт меню в раздел администрирования (/protected/modules/admin/config/menu.php)
	...
	HCrud::getMenuItems(Y::controller(), 'car_brands', 'crud/index', true),

4) Добавить миграцию в базу данных

	public function up()
	{
		$this->addColumn('product', 'car_brand_id', 'integer');
		$this->addColumn('product', 'car_model_id', 'integer');
	}

5) Добавить правило для атриубтов в модель (/protected/models/Product.php)
	
	public function rules()
	{
		return ...
			['car_brand_id, car_model_id', 'safe']
		...
	}
		
6) Добавить подписи для атрибутов (/protected/models/Product.php)
	public function attributeLabels()
	{
		...
		'car_brand_id'=>'Марка машины',
        'car_model_id'=>'Модель машины',
	}

7) Добавить в форму редактирования товара (/protected/modules/admin/views/shop/_form_product.php)
	...
	\crud\models\ar\CarBrand::formFields($form, $model, 'car_brand_id', 'car_model_id'); 

8) На стороне публичной части сайта доработка выполняется индивидуально.

Пример сквозного фильтра выбора раздела каталога, марки и модели автомобиля

Добавить в /protected/components/Controller

	public $category_id=null;
	public $carbrand=null;
	public $carmodel=null;

Добавить в /protected/controllers/ShopController::actionCategory()
		// использует use common\components\helpers\HRequest as R;
		...
		$this->category_id=$category->id;
        $this->carbrand=R::get('b');
        $this->carmodel=R::get('m');

Добавить в шаблон (файлы виджета фильтра в папке widget, использует select2)

<?php $this->widget('\widget\filters\MainFilter'); ?>
