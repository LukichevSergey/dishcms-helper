1) Заменить скрипт в файле /widget/filters/assets/js/attr_filter.js
после
var data = JSON.stringify(_data);
добавить
var filterUrl=window.location.href.replace(/\/-\/.*$/, '').replace(/^([^?]+)\??.*$/, '$1').replace(/\/$/, '') + '/-/';
_data.forEach(function(item){if(!isNaN(+item.name) && !isNaN(+item.value)){filterUrl+=item.name + '-' + item.value + '/'; }});
window.location.href=filterUrl.replace(/\/-\/?$/, '');
return;

2) В шаблоне фильтра /widget/filters/views/attr_list.php
заменить код выпадающего фильтра на 
		$attr['values']['none'] = 'Любой';
		$selected='none';
		if(!empty($_REQUEST['data_unjson']) && is_array($_REQUEST['data_unjson'])) {
			foreach($_REQUEST['data_unjson'] as $item) {
				if(!empty($item['name']) && ($item['name'] == $attr['id'])) {
					if(!empty($item['value']) && is_numeric($item['value'])) {
						$selected=$item['value'];
					}
					break;
				}
			}
		}

		echo CHtml::dropDownList($attr['id'], $selected, $attr['values'], array('data-id'=>$key, 'selected'=>'none'));  ?>

3) В основной файл /корень_сайта/index.php добавить в самое начало код

// преобразование переменных фильтра товаров
$GLOBALS['__ORIGIN_REQUEST_URI']=$_SERVER['REQUEST_URI'];
if(!preg_match('#^(.*?)/-/(.*?)\?(.*?)$#', $_SERVER['REQUEST_URI'], $m)) {
	preg_match('#^(.*?)/-/(.*?)$#', $_SERVER['REQUEST_URI'], $m);
}
if(!empty($m)) {
	$_SERVER['REQUEST_URI']=$m[1];
	$_REQUEST['data']=[['name'=>'_method', 'value'=>'']];
	$params=explode('/', trim($m[2], '/'));
	if(is_string($params)) { $params=[$params]; }
	foreach($params as $value) {
		$data=explode('-', $value);
		$_REQUEST['data'][]=['name'=>$data[0], 'value'=>$data[1]];
	}
	$_REQUEST['data_unjson']=$_REQUEST['data'];
	$_POST['data']=$_GET['data']=$_REQUEST['data']=json_encode($_REQUEST['data']);
	unset($params);
	unset($data);
	unset($value);
}
unset($m);

4) Скоприровать и подключить CRUD SEO страниц
- скопировать файл /crud/seo_meta_tags.php в /config/crud/seo_meta_tags.php
- в /config/crud.php добавить
'seo_meta_tags'=>'application.config.crud.seo_meta_tags',

5) Добавить в /components/Controller::seoTags($metadata) в начало метода
	if($seoMeta=\crud\models\ar\SeoMetaTag::getMetaTags()) { 
		$metadata=$seoMeta; 
    }

6) Добавить пункт меню в раздел администрирования
HCrud::getMenuItems(Y::controller(), 'seo_meta_tags', 'crud/index', true)

7) Вывод дополнительного текста (пример)
<? if($text=\crud\models\ar\SeoMetaTag::getTextByUrl()): ?>
	<?= $text; ?>
<? else: ?>
	// вывод основного текста
<? endif; ?>
