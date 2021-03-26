Инструкция по установке и использованию модуля
----------------------------------------------------------------------------
Содержание:
I. УСТАНОВКА
II. ИСПОЛЬЗОВАНИЕ

* Рассчет габаритов товара производится в приориете, сначала по весу, потом по объему.
----------------------------------------------------------------------------
I. УСТАНОВКА
----------------------------------------------------------------------------

*) Добавить поле в форму заказа
delivery_type | Доставка

*) Скопировать файлы из папки /install (те, которые требуются).
В новых версиях CMS виджет /protected/modules/DOrder/widgets/delivery уже предустановлен

*) Скопировать видежты отображения (если требуется)
/protected/modules/DOrder/widgets/delivery/*

*) Добавить свойства и поведение в \ShopSettings (/protected/models/ShopSettings.php)
public $cdek_tariff_group;
public $cdek_send_city_id;
public $cdek_extra_charge;
public $cdek_seller_name;
public $cdek_package_item_cost;
...
public function behaviors()
{
	return A::m(parent::behaviors(), [
        'cdekBehavior'=>'\cdek\behaviors\ShopSettingsBehavior'
	]);
}

*) Добавить вкладку в шаблон настроек (/protected/modules/admin/views/settings/_shop_form.php)
$this->widget('zii.widgets.jui.CJuiTabs', [
	'tabs'=>[
	...
	'СДЭК'=>['content'=>$this->widget('\cdek\widgets\Settings', compact('form', 'model'), true), 'id'=>'tab-sdek'],



На старых версиях, необходимо добавить return parent::beforeValidate(); вместо return true;
\DOrder\models\DOrder
public function beforeValidate()
{
	if($this->isNewRecord) {
		$this->create_time = new \CDbExpression('NOW()');
	}
	return parent::beforeValidate();
}

Подключить модуль в /protected/modules/ecommerce/config/main.php
'cdek'=>[
     'class'=>'ecommerce.modules.cdek.CdekModule',
],

Добавить алиас в /protected/config/defaults.php
'aliases'=>[
	...,
	'cdek'=>'application.modules.ecommerce.modules.cdek'
]

Добавить пункт меню в /protected/modules/admin/config/menu.php
  ...
  [
            'active'=>Y::isAction(Y::controller(), 'cdek'),
            'label'=>'СДЭК', 
            'url'=>['cdek/index'], 
  ],


Выполнить миграции 
php -f yiic.php migrate --migrationPath=application.modules.ecommerce.modules.cdek.migrations

Импортировать города из файла City_RUS_20171118.csv в разделе администрирования, 
но рекомендуется города импортировать в базу данных из файла cdek_cities.sql (после того, как будут выполнены миграции)

В шаблоне формы оформления заказа вставить код поля
(вставить в начало файла, в поле <?php )
use common\components\helpers\HYii as Y;
use cdek\components\helpers\HCdek;
Y::jsCore('cookie');
(далее в форме)
			<? if($f['name'] == 'delivery_type'): ?>
                <? $this->widget('\DOrder\widgets\delivery\DeliveryTypeField', [
                    'form'=>$form,
                    'model'=>$this->model,
					'pickup'=>true,
                    'pickupLabel'=>'Самовывоз',
                    'cdekTariffGroup'=>HCdek::settings()->cdek_tariff_group,
                    'cdekTariffModes'=>[\cdek\models\Tariff::MODE_SS, \cdek\models\Tariff::MODE_SD],
					'rpochta'=>false
                ]); ?>
            <? else: ?>
				... здесь будут остальные поля
				<?php switch ($f['type']) { // обычно начинаются с этой строки
			<? endif; ?>

Добавить параметры в файл /protected/config/params.php
    'cdek'=>[
        'geocode'=>true,
        'integration'=>[
            'url'=>'https://integration.cdek.ru',
        ],        
        'account'=>'<account>',
        'secure_password'=>'<secure_password>'
    ],

----------------------------------------------------------------------------
II. ИСПОЛЬЗОВАНИЕ
----------------------------------------------------------------------------

