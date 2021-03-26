Вставить в init.php

/**
 * ОЧИСТКА ОСНОВНОГО КЭША И ВСЕХ РЕГИОНОВ
 */
if(isset($_GET['LKtzwcrgb8ZbKxAn6XhBbNNAjmCEesRY']) && ($_GET['LKtzwcrgb8ZbKxAn6XhBbNNAjmCEesRY']==='7zHuDNMChBjexz28JpERf2CCHqGMhTdR')) {
	exec('find /var/www/vhosts/enetra.ru/*/bitrix/cache -type f -exec rm {} \;');
	echo 'done!';
	die;
}

Запуск:
/?LKtzwcrgb8ZbKxAn6XhBbNNAjmCEesRY=7zHuDNMChBjexz28JpERf2CCHqGMhTdR

----------------------------------------------------------------------
Добавление сео свойств для разделов
----------------------------------------------------------------------

1) Выполнить скрипт add_meta.php (поправив соответствующие настройки в нем)

2) Скопировать код из php_interface в /bitrix/php_interface

3) Добавить код в /bitrix/php_interface/init.php
include_once dirname(__FILE__) . '/kontur/autoload.php';

AddEventHandler('main', 'OnEpilog', 'onEpilog', 1);
function onEpilog(){
    global $APPLICATION;
    if(preg_match('/(catalog)/', $APPLICATION->GetCurDir())){
        if(isset($GLOBALS["PAGE_META_TITLE"])) {
    	    $APPLICATION->SetPageProperty("title", $GLOBALS["PAGE_META_TITLE"]);
        }
        if(isset($GLOBALS["PAGE_META_DESCRIPTION"])) {
    	    $APPLICATION->SetPageProperty("description", $GLOBALS["PAGE_META_DESCRIPTION"]);
        }
        if(isset($GLOBALS["PAGE_META_KEYWORDS"])) {
    	    $APPLICATION->SetPageProperty("keywords", $GLOBALS["PAGE_META_KEYWORDS"]);
        }
    }
}

Установка мета-тэгов
Добавить код в component_epilog.php шаблона компонента catalog.section
$arSectionResult=array('IBLOCK_ID'=>$arParams['IBLOCK_ID'], 'ID'=>$arParams['SECTION_ID']);
\kontur\GetRegionMetaKey($arSectionResult);
\kontur\GetRegionMetaDesc($arSectionResult);
\kontur\GetRegionMetaTitle($arSectionResult);

Пример для переопределения описания (добавить в программный код перед выводом описания раздела):
<?php if($sectionCustomDescription=\kontur\GetRegionDesc($arResult['SECTION'])): ?>
    <?php $arResult['SECTION']['DESCRIPTION']=$sectionCustomDescription; ?>
<?php endif; ?>

-----------------------------------------------------------------
Установка для bitrix:news
section.php
мета-тэги
<?php
$arSectionResult=array('IBLOCK_ID'=>$arParams['IBLOCK_ID'], 'ID'=>$arResult['VARIABLES']['SECTION_ID']);
\kontur\GetRegionMetaKey($arSectionResult);
\kontur\GetRegionMetaDesc($arSectionResult);
\kontur\GetRegionMetaTitle($arSectionResult);
?>

описание: /news.list
<?php
$arCurSectionPath = end($arResult["SECTION"]["PATH"]);
if($sectionCustomDescription=\kontur\GetRegionDesc($arCurSectionPath)) {
    $arResult['SECTION']['PATH'][count($arResult["SECTION"]["PATH"])-1]['DESCRIPTION']=$sectionCustomDescription;
}
?>


----------------------------------------------------------------------
Добавление сео свойств для элементов
----------------------------------------------------------------------
1) Выполнить скрипт add_meta_to_elements.php (поправив соответствующие настройки в нем)

2) Скопировать код из php_interface в /bitrix/php_interface (см. выше) если еще не добавлен.

3) Если AddEventHandler('main', 'OnEpilog', 'onEpilog', 1); (см. выше) если еще не добавлен.

4) Добавить в шаблон отображения элемента код

global $APPLICATION;
if(!empty($arResult['PROPERTIES'][CITY_PREFIX_UF.'META_TITLE']['VALUE'])) {
    $APPLICATION->SetPageProperty("title", $arResult['PROPERTIES'][CITY_PREFIX_UF.'META_TITLE']['VALUE']);
    $GLOBALS["PAGE_META_TITLE"] = $arResult['PROPERTIES'][CITY_PREFIX_UF.'META_TITLE']['VALUE'];
}
if(!empty($arResult['PROPERTIES'][CITY_PREFIX_UF.'META_KEY']['VALUE'])) {
    $APPLICATION->SetPageProperty("keywords", $arResult['PROPERTIES'][CITY_PREFIX_UF.'META_KEY']['VALUE']);    
    $GLOBALS["PAGE_META_KEYWORDS"] = $arResult['PROPERTIES'][CITY_PREFIX_UF.'META_KEY']['VALUE'];
}
if(!empty($arResult['PROPERTIES'][CITY_PREFIX_UF.'META_DESC']['VALUE'])) {
    $APPLICATION->SetPageProperty("description", $arResult['PROPERTIES'][CITY_PREFIX_UF.'META_DESC']['VALUE']);    
    $GLOBALS["PAGE_META_DESCRIPTION"] = $arResult['PROPERTIES'][CITY_PREFIX_UF.'META_DESC']['VALUE'];
}

5) Подменить описание в result_modifier.php
if(!empty($arResult['PROPERTIES'][CITY_PREFIX_UF.'DETAIL_TEXT']['VALUE']['TEXT'])) {
    $arResult['DETAIL_TEXT'] = $arResult['PROPERTIES'][CITY_PREFIX_UF.'DETAIL_TEXT']['VALUE']['TEXT'];
}
