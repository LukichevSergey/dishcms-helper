Инструкция по установке и использованию модуля \extend\modules\regions
----------------------------------------------------------------------------
Содержание:
I. УСТАНОВКА
II. ИСПОЛЬЗОВАНИЕ
III. ИСПОЛЬЗОВАНИЕ ДЛЯ МОДЕЛЕЙ \CFormModel
IV. НАСТРОЙКА МОДЕЛИ \SettingsForm

----------------------------------------------------------------------------
I. УСТАНОВКА (для версии DishCMS >= 2.4)
----------------------------------------------------------------------------
ОБЯЗАТЕЛЬНО! Выполнение пункта 3.

1) Подключить модуль
1.1) Если модуль еще не подключен в модуле extend, то подключить модуль:
protected\modules\extend\config\main.php
return [
	'modules'=>[
		...
		'regions'=>['class'=>'\extend\modules\regions\RegionsModule', 'autoload'=>true],
	],
];

1.2) Подключить конфигурацию CRUD для раздела администрирования.
protected\config\crud.php
return [
	...
	'application.modules.extend.modules.regions.config.crud.main'
];

2) Добавить пункт меню "Регионы" в раздел администрирования
protected\modules\admin\config\menu.php
use crud\components\helpers\HCrud;
...
'modules'=>[
    ...
    HCrud::getMenuItems(Y::controller(), 'regions', 'crud/index', true)
];

3) ОБЯЗАТЕЛЬНО! Добавить регион по умолчанию (основной) (напр., через раздел администрирования)!

4) Сменить настройку использовать данные основного региона, для пустых значений, можно так:
/protected/config/params.php
    ...
    Ниже перечислены доступные параметры и их значения по умолчанию
    'extend'=>['modules'=>['regions'=>['components'=>['helpers'=>['HRegion'=>[
        'baseDomain' => false, // домен, на который будет идти перенаправление при отключении региона.
        'enableForced' => true, // разрешена установка принудительного использования установленного значения.
        'useDefaultIfEmpty' => true, // использовать значение региона по умолчанию, для пустых значений
        'enableFromOrigin' => true, // использовать значения внешней модели для пустых значений для основного региона 
        'enableFromOriginByRegion' => true // использовать значения внешней модели для пустых значений не основного региона
    ]]]]]],

5) Для СЕО необходимо добавить код в protected\components\Controller.php
public function seoTags($metadata) 
{
	...
    	elseif($metadata instanceof CModel) {
    		...
            elseif(Y::hasBehaviorByClass($metadata, ['\MetadataBehavior', 'MetadataBehavior'], true)) {
            	$this->meta_key=$metadata->meta_key?:$this->meta_key;
            	$this->meta_desc=$metadata->meta_desc?:$this->meta_desc;
            	$this->pageTitle=$metadata->meta_title?:$this->pageTitle;
            	$this->contentTitle=$metadata->meta_h1?:$this->contentTitle;
            }

6) Поправить код получения getMetaH1()
protected\components\behaviors\MetadataBehavior.php
public function getMetaH1()
{
	if($this->owner->meta_h1) {
		return $this->owner->meta_h1;
	}
	return $this->owner->{$this->attributeTitle};
}
----------------------------------------------------------------------------
II. ИСПОЛЬЗОВАНИЕ
----------------------------------------------------------------------------
1) Виджет смены региона для административной части \extend\modules\regions\widgets\ChangeRegion
1.1) protected\modules\admin\views\layouts\main.php
Вставить виджет:

<? $this->widget('zii.widgets.CMenu', [
    'items'=>HAdmin::menuItemsMain(),
    ...
?>

<?php $this->widget('\extend\modules\regions\widgets\ChangeRegion'); ?>

<ul class="nav navbar-nav navbar-right">

1.2) \protected\modules\admin\components\AdminController.php
Подключить поведение смены региона:
use common\components\helpers\HArray as A;

public function behaviors()
{
   	return A::m(parent::behaviors(), [
   		'regionBehavior'=>'\extend\modules\regions\behaviors\AdminControllerBehavior'
   	]);
}

2) Подключение к модели.
Модель должны быть наследуемой от \common\components\base\ActiveRecord
На примере модели \Product
    public function behaviors()
    {
       	'regionBehavior' => [
       		'class'=>'\extend\modules\regions\behaviors\RegionAttributeBehavior',
       		'attributes'=>'price, description, alt_title, link_title'
        ]
    }

Для SEO атрибутов добавить в модель Metadata
public function behaviors()
    {
        'regionBehavior' => [
            'class'=>'\extend\modules\regions\behaviors\RegionAttributeBehavior',
            'attributes'=>'meta_h1, meta_title, meta_key, meta_desc, priority, changefreq, lastmod, a_title'
        ]
    }

подробнее о возможностях в комментариях самого поведения \extend\modules\regions\behaviors\RegionAttributeBehavior

При добавлении всех полей СЕО в шаблоне формы СЕО-полей добавить виджет
<?php $this->widget('\extend\modules\regions\widgets\OnlyForRegion'); ?>

3) Возможно использовать события с версии 2.14.5
Пример:
/protected/config/events.php
<?php
use common\components\helpers\HArray as A;
use common\components\helpers\HDb;
use extend\modules\regions\components\helpers\HRegion;

return [
    'onAfterSaveRegion' => [function($event) {
        $model = A::get($event->params, 'model');
        if($model->isNewRecord) {
            // создаем таблицу доставок для нового региона
            HDb::query('CREATE TABLE IF NOT EXISTS `delivery' . HRegion::i()->getPostfix($model->id) .'` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `title` varchar(255) DEFAULT NULL,
                `active` tinyint(1) DEFAULT NULL,
                `price` decimal(15,2) DEFAULT NULL,
                `free_price` decimal(15,2) DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;');
        }
    }]
];

4) Для генерации карты сайта внести правки
/protected/extensions/sitemap/SitemapGenerator.php

public function generateSitemap()
 ...
 $baseUrl=rtrim(\Yii::app()->createAbsoluteUrl('/'), '/'); 
 после добавить
 if(HRegion::i()->isDefault()) {
     $baseUrl = 'https://' . HRegion::i()->region()->domain;
     $prefix = '';
 }
 else {
     $baseUrl = 'http://' . HRegion::i()->region()->domain;
     $prefix = preg_replace('/^([^.]+)\..*$/', '$1', HRegion::i()->region()->domain) . '-';
 }
 $this->setDomain($baseUrl);

4.2) Внести правила в основной .htaccess (заменить example.ru на нужный домен)

RewriteCond %{HTTP_HOST} ^example.ru(\.local)?$ [NC]
RewriteCond %{REQUEST_URI} ^/sitemap-(.*)\.xml$ [NC]
RewriteRule ^(.*)$ /sitemaps/sitemap-%1.xml [L]

RewriteCond %{HTTP_HOST} ^example.ru(\.local)?$ [NC]
RewriteCond %{REQUEST_URI} ^/sitemap\.xml$ [NC]
RewriteRule ^(.*)$ /sitemaps/sitemap.xml [L]

RewriteCond %{HTTP_HOST} ^(.+)\.example.ru(\.local)?$ [NC]
RewriteRule . - [E=subdomain:%1]

RewriteCond %{HTTP_HOST} !^www\.example.ru(\.local)?$ [NC]
RewriteCond %{HTTP_HOST} ^(.*)\.example.ru(\.local)?$ [NC]
RewriteCond %{REQUEST_URI} ^/sitemap\.xml$ [NC]
RewriteRule ^(.*)$ /sitemaps/%{ENV:subdomain}-sitemap.xml [L]

RewriteCond %{HTTP_HOST} !^www\.example.ru(\.local)?$ [NC]
RewriteCond %{HTTP_HOST} ^(.+)\.example.ru(\.local)?$ [NC]
RewriteCond %{REQUEST_URI} !^/sitemaps/.*$ [NC]
RewriteCond %{REQUEST_URI} ^(.*)\.xml$ [NC]
RewriteRule ^(.*)$ /sitemaps/$1 [L]

RewriteCond %{HTTP_HOST} !^www\.example.ru(\.local)?$ [NC]
RewriteCond %{HTTP_HOST} ^(.*)\.example.ru(\.local)?$ [NC]
RewriteCond %{REQUEST_URI} ^/robots\.txt$ [NC]
RewriteRule ^(.*)$ /%{ENV:subdomain}.robots.txt [L]


----------------------------------------------------------------------------
III. ИСПОЛЬЗОВАНИЕ ДЛЯ МОДЕЛЕЙ \CFormModel
----------------------------------------------------------------------------
Особенности для моделей \CFormModel
-----------------------------------
Необходимо: 
1) Добавить атрибут id
    /**
	 * @var integer идентификатор модели.
	 */
	public $id=1;

2) Модель унаследовать от \common\components\base\FormModel
3) Внести соотвествующие правки
public function rules()
{        
    ...
     return $this->getRules($rules);
}

public function attributeLabels()
{
    return $this->getAttributeLabels(array(
        ...
    ));
}

4) Закомментировать соответвующие свой-ва

----------------------------------------------------------------------------
IV. НАСТРОЙКА МОДЕЛИ \SettingsForm
----------------------------------------------------------------------------
1) protected\config\defaults.php
Исправить идентификатор кэша на генерацию уникального идентификатора для регионов.
'settings'=>array(
    'class' => 'CmsSettings',
    'cacheId'   => 'cmscfg_'
        . (preg_match('#^/(cp|admin)/#', $_SERVER['REQUEST_URI']) 
            ? (isset($_GET['rid']) ? $_GET['rid'] 
                : (isset($_COOKIE['current_region']) ? $_COOKIE['current_region'] : '')) 
            : crc32($_SERVER['SERVER_NAME'])
        ),
    'cacheTime' => 84000,
),

2) Добавить в модель атрибут id
    /**
	 * @var integer идентификатор модели.
	 */
	public $id=1;
    
3) Модель унаследовать от \common\components\base\FormModel
4) Внести соотвествующие правки
public function rules()
{        
    ...
     return $this->getRules($rules);
}

public function attributeLabels()
{
    return $this->getAttributeLabels(array(
        ...
    ));
}
    
5) Добавить поведение
 /**
     * {@inheritDoc}
     * @see CModel::behaviors()
     */
    public function behaviors()
    {
   		return A::m(parent::behaviors(), array(
			'regionBehavior' => [
				'class'=>'\extend\modules\regions\behaviors\RegionAttributeBehavior',
				'attributes'=>'slogan, address, sitename, phone, phone2, email, counter, sitemap,'
					. 'emailPublic, firm_name, comments, meta_title, meta_key, meta_desc'
			]
   		));
    }        
6) Закомментировать соответвующие свой-ва
//     public $slogan;
//     public $address;
//     public $sitename;
//     public $phone;
//     public $phone2;
//     public $email;
//     public $emailPublic;
//     public $firm_name;
//     public $counter;    
//     public $comments;
//     public $meta_title;
//     public $meta_key;
//     public $meta_desc;

7) Внести правку в метод 
public function loadSettings()

    foreach($this->attributeNames() as $attr) {
        $this->$attr = Yii::app()->settings->get('cms_settings', $attr);
    заменить на
	$regionBehavior=$this->asa('regionBehavior');
    foreach($regionBehavior->getAttributes() as $attr) {
	    $origin=$regionBehavior->originPrefix . $attr;
        $this->$origin=Yii::app()->settings->get('cms_settings', $attr);
    }        

    $attributeNames=array_merge($this->attributeNames(), $regionBehavior->getAttributes());
    foreach($attributeNames as $attr) {
	    if($this->$attr === null) {
	        $this->$attr = Yii::app()->settings->get('cms_settings', $attr);
        }

8) Добавить метод
 /**
     * Сохранение
     */
    public function save()
    {
    	parent::save();
    	
    	$this->saveSettings();
    	
    	return true;
    }
    
9) Внести правки в файл контроллера admin.controllers.DefaultController    
public function actionSettings()
{
    ...
    if ($model->validate()) {
        заменить $model->saveSettings(); на $model->save();
        ...
        
10) protected\extensions\helpers\ModuleHelper.php
Заменить 
public static function getParam($name, $return = false) 
{
    $value = Yii::app()->settings->get('cms_settings', $name);
    на
    $value = D::cms($name);
        
11) protected\components\DApi.php
Добавить код        
    /**
	 * @var boolean проинициализирован
	 */
	private static $_initialized=false;
    
    и 
    public function init() {
		if(!static::$_initialized) {
			static::$_initialized=true;
    }
    
    ,и
    public function isActive($module)
	{
		if(!static::$_initialized) {
			return false;
		}
        ...
12) protected\components\D.php
    Добавить код в метод cms(), 
    public static function cms($param, $default=null)
	{
		Yii::import('admin.models.SettingsForm');
		$model=SettingsForm::model();
		if($model->$param !== null) {
			return $model->$param;
		}
		
		return \Yii::app()->settings->get('cms_settings', $param) ?: $default;	
	}
    
    при необходимости изменить метод (изменен с версии 2.5)
  	public static function cmsFile($param, $default=null)
	{
		$uploadPath = \Yii::app()->params['uploadSettingsPath'];
		$filename = static::cms($param);
		
		return $filename? ($uploadPath . $filename) : $default;	
	}

    а также public static function shop($param, $default=null)
    
13) protected\components\Controller.php
protected function prepareSeo($page_title = null) {
    $meta_title = Yii::app()->settings->get('cms_settings', 'meta_title');
    заменить на 
    $meta_title = D::cms('meta_title');
}

- В методе public function init() аналогично на D::cms();
- Также при необходимости во всем публичном коде, где идет получение настроек заменить конструкцию
Yii::app()->settings->get('cms_settings', 'название_атрибута'); 
на
D::cms('название_атрибута');
