Данный компонент дополнен изображением и глобальными переменными (см. п. II).

I) Пример использования доп. параметров.

Добавление ссылки на изображение
1) Копируем шаблон в тему (напр. my_default)
2) Создаем файл .parameters.php

<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(!empty($arCurrentValues['MENU_ITEMS_COUNT'])) { 
	for($i=1; $i<=(int)$arCurrentValues['MENU_ITEMS_COUNT']; $i++) {
		$arTemplateParameters['MENU_ITEMS_IMAGE_'.$i]=array(
            'PARENT'=>'MENU_ITEMS_'.$i,
            'NAME'=>'Изображение',
            'TYPE'=>'FILE',
            "FD_TARGET" => "F",
		    "FD_EXT" => 'jpg,jpeg,gif,png',
		    "FD_UPLOAD" => true,
		    "FD_USE_MEDIALIB" => true
        );
    } 
}

3) Создаем файл result_modifier.php

<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arResult['ITEMS']=$this->getComponent()->GetMenuItems($arParams, array('IMAGE'=>'MENU_ITEMS_IMAGE_'));

4) добавляем в шаблон вывод

<?if(!empty($arItem['IMAGE'])):
	$sImage=str_replace('#SITE_TEMPLATE_PATH#', SITE_TEMPLATE_PATH, $arItem['IMAGE']);
    ?><img src="<?=$sImage?>" title="<?=$arItem['NAME']?>" /><? 
endif; ?>

II) Как внедрить глобальный каталог