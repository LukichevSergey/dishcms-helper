----------------------------------------------------------------
Установка (первичная)
----------------------------------------------------------------
1) Скопировать файлы из папки "Установка" в /protected
2) В папке /config/crud/ переименовать файлы "example_sections.php" и "example_section_items.php" 
и задать параметры переменной $c
3) Переименовать файл контроллера /controllers/ExampleSectionsController.php в нужный и задать нужные значения свойств.

----------------------------------------------------------------
Установка (вторичная)
----------------------------------------------------------------
1) Для нового модуля достаточно скопировать из папки "Установка" папки /config/crud и /controllers
2) В папке /config/crud/ переименовать файлы "example_sections.php" и "example_section_items.php" 
и задать параметры переменной $c
3) Переименовать файл контроллера /controllers/ExampleSectionsController.php в нужный и задать нужные значения свойств.

----------------------------------------------------------------
Подключение (общее)
----------------------------------------------------------------
1) Подключить импорт классов из папки /components/base, если еще не установлено (/protected/config/defaults.php)
	'import'=>array(
		'application.components.base.*',
		...

----------------------------------------------------------------
Подключение (на основе примера "Услуги")
----------------------------------------------------------------

1) Подключить конфигурации (/protected/config/crud.php)
return [
	...
    'service_sections'=>'application.config.crud.service_sections',
    'services'=>'application.config.crud.services'
];


2) Подключение ЧПУ (/protected/config/urls.php)

return array(
	['class'=>'\seo\ext\sef\components\rules\SefRule', 'config'=>[
        '\crud\models\ar\ServiceSection'=>[
            'base'=>'uslugi',
            'url'=>'services/section',
            'nestedset'=>'nestedSetBehavior'
        ],
        '\crud\models\ar\Service'=>[
            'base'=>'uslugi',
            'url'=>'services/view'
        ],
    ]],
    ...

3) Подключить в меню раздела администрирования (/protected/modules/admin/config/menu.php)
use crud\components\helpers\HCrud; (если закомментировано - разкомментировать)
return [
	...
	'modules'=>array_merge([
		HCrud::getMenuItems(Y::controller(), 'service_sections', 'crud/index', true),

4) Пример отображения в шаблоне макета (/layouts/index.php)
<? $this->renderPartial('application.views.base.sections._sections', [
	'sections'=>\crud\models\ar\ServiceSection::model()->roots()->published()->findAll(), 
	'header'=>'Услуги нашего салона'
]); ?>

