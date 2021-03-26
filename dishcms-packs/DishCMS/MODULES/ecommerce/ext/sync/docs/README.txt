------------------------------------------------------------
На стороне сайта, на который выгружаются обновления каталога
------------------------------------------------------------
1) Добавление настроек синхронизации
/protected/models/ShopSettings.php

	public $sync_url;
	public $sync_token;
	public $sync_limit;
	public $sync_reload_files;

	public function behaviors()
	{
		return A::m(parent::behaviors(), [
		    'syncSettings'=>'\ecommerce\ext\sync\behaviors\SettingsBehavior'
		]);
	}

2) Добавление действия синхронизации в контроллер
/protected/modules/admin/controllers/ShopController.php
public function actions()
{
	return A::m(parent::actions(), [
		...
		'sync'=>'\ecommerce\ext\sync\actions\Sync'
	]);
}

3) Добавление таба синхронизации в форму настроек
/protected/modules/admin/views/settings/_shop_form.php
	$this->widget('zii.widgets.jui.CJuiTabs', [
		'tabs'=>[
			...
		    'Синхронизация'=>['content'=>$this->widget('\ecommerce\ext\sync\widgets\Settings', compact('model', 'form'), true), 'id'=>'tab-sync']
		],
		'options'=>[]
	]);

4) Виджет кнопки синхронизации 
/protected/modules/admin/views/shop/index.php
/protected/modules/admin/views/shop/category.php
Например:
<h1>...<?php $this->widget('\ecommerce\ext\sync\widgets\Sync'); ?></h1>


5) Если требуется кнопка запуска синхронизации на сайте-источнике (в разработке)
/protected/controllers/ShopController.php

use common\components\helpers\HArray as A;
...
public function actions()
{
	return A::m(parent::actions(), [
		...
		'sync'=>'\ecommerce\ext\sync\actions\Listner'
	]);
}
------------------------------------------------------------
На стороне сайта, с которого выгружаются обновления каталога
------------------------------------------------------------
1) Добавление настроек синхронизации
/protected/models/ShopSettings.php

	public $sync_url;
	public $sync_token;
	public $sync_limit;
	public $sync_reload_files;
	
	public function behaviors()
	{
		return A::m(parent::behaviors(), [
		    'syncSettings'=>'\ecommerce\ext\sync\behaviors\SettingsBehavior'
		]);
	}
	
2) Добавление действия слушателя в контроллер
/protected/controllers/ShopController.php

use common\components\helpers\HArray as A;
...
public function actions()
{
	return A::m(parent::actions(), [
		...
		'sync'=>'\ecommerce\ext\sync\actions\Server'
	]);
}

3) Добавление таба синхронизации в форму настроек
/protected/modules/admin/views/settings/_shop_form.php
	$this->widget('zii.widgets.jui.CJuiTabs', [
		'tabs'=>[
			...
		    'Синхронизация'=>['content'=>$this->widget('\ecommerce\ext\sync\widgets\ServerSettings', compact('model', 'form'), true), 'id'=>'tab-sync']
		],
		'options'=>[]
	]);
 


