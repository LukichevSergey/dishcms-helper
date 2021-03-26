<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(!empty($arResult['ITEMS'])): 
    ?><ul class="nav navbar-nav"><? 
        foreach($arResult['ITEMS'] as $arItem): 
            ?><li><a href="<?=$arItem['LINK']?:'javascript:;'?>" class="root-item"><?=$arItem['NAME']?></a><? 
            if($arItem['TYPE'] == 'M'): 
            ?><?$APPLICATION->IncludeComponent("bitrix:menu", "top_submenu", Array(
	"ROOT_MENU_TYPE" => $arItem["MENU_TYPE"],	// Тип меню для первого уровня
		"MAX_LEVEL" => $arItem["DEPTH_LEVEL"]?:1,	// Уровень вложенности меню
		"CHILD_MENU_TYPE" => $arItem["MENU_TYPE"],	// Тип меню для остальных уровней
		"USE_EXT" => "N",	// Подключать файлы с именами вида .тип_меню.menu_ext.php
		"DELAY" => "N",	// Откладывать выполнение шаблона меню
		"ALLOW_MULTI_SELECT" => "N",	// Разрешить несколько активных пунктов одновременно
		"MENU_CACHE_TYPE" => "N",	// Тип кеширования
		"MENU_CACHE_TIME" => "3600",	// Время кеширования (сек.)
		"MENU_CACHE_USE_GROUPS" => "Y",	// Учитывать права доступа
		"MENU_CACHE_GET_VARS" => "",	// Значимые переменные запроса
		"COMPONENT_TEMPLATE" => ".default"
	),
	false
);?><?elseif(!empty($arItem['ITEMS'])):
				$prevDepth=0;
				foreach($arItem['ITEMS'] as $i=>$arSection): 
                    $depth=(int)$arSection['DEPTH_LEVEL'];
                    if($depth > $prevDepth) echo '<ul class="submenu submenu-'.$depth.'">';
                    elseif($depth < $prevDepth) echo '</li>' . str_repeat('</ul></li>', $prevDepth-$depth); 
                    else echo '</li>';
                    $prevDepth=$depth;
                    ?><li<?if(!empty($arSection['SELECTED'])) echo ' class="selected"';?>><?
                        if(!empty($arItem['ITEMS'][$i+1]) && ($arSection['DEPTH_LEVEL'] < (int)$arItem['ITEMS'][$i+1]['DEPTH_LEVEL'])):
                            ?><a href="javascript:;"><?=$arSection['NAME']?></a><?
                        else:
                            ?><a href="<?=$arSection['SECTION_PAGE_URL']?>"><?=$arSection['NAME']?></a><?
                        endif;
				endforeach;
                echo str_repeat('</ul>', $prevDepth-1) . '</li></ul>';
			endif;
		?></li>
	<? endforeach; ?>
	</ul>
<? endif; ?>