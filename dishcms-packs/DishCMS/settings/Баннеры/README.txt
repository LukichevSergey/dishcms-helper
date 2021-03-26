Добавление
----------
1) Скопировать модуль settings из последней версии DishCMS
2) Обновить модуль common из последней версии DishCMS
3) protected\config\defaults.php
    'modules'=>[
        ...
		'settings'=>[
			'class'=>'application.modules.settings.SettingsModule',
			'config'=>[
				'banners'=>[
					'class'=>'\BannerSettings',
					'title'=>'Настройки баннеров',
					'menuItemLabel'=>'Баннеры',
					'viewForm'=>'admin.views.settings._banner_form'
				],
                
4) protected\config\urls.php

	'settings/admin/default/index/<id:\w+>'=>'settings/admin/default/index',
	'admin/settings/<id:\w+>'=>'admin/settings/index',
	'cp/settings/<id:\w+>'=>'admin/settings/index',

5) protected\modules\admin\views\layouts\main.php
use common\components\helpers\HArray as A;
use settings\components\helpers\HSettings;
...
$modulesMenu[] = ['label'=>'', 'itemOptions'=>['class'=>'divider']];
$modulesMenu = A::m($modulesMenu, HSettings::getMenuItems($this, ['banners'], 'settings/index'));

-------------
Использование
-------------
<? $this->widget('widget.banners.BannerWidget'); ?>