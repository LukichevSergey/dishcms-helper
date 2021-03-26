1) Скопировать файлы из папки /protected

2) Добавить в /protected/config/crud.php
return [
	'articles'=>'application.config.crud.articles'
	...


3) Добавить правила в /protected/config/urls.php
return [
	...	
	'stati'=>'article/list',
	'stati/index'=>'article/list',
	'stati/<id:\d+>'=>'article/view',
	
4) Добавить настройку в правило разбора ЧПУ /protected/components/rules/DAliasRule.php

public $config=array(
	...
	'Article'=>array('url'=>'article/view', 'replaceUrl'=>'stati'),
	
5) Добавить пункт меню в разделе администрирования /protected/modules/admin/config/menu.php
use crud\components\helpers\HCrud;

'modules'=>array_merge([
	...
	HCrud::getMenuItems(Y::controller(), 'articles', 'crud/index', true),

