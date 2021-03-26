<?php

AddEventHandler('main', 'OnBuildGlobalMenu', 'kontur_admin_menu_hide_broken_iblocks');
if(!function_exists('kontur_admin_menu_hide_broken_iblocks')) {
        function kontur_admin_menu_hide_broken_iblocks(&$arGlobalMenu, &$arModuleMenu) {
                foreach($arModuleMenu as $k => $v) { if(empty($v['title'])) { unset($arModuleMenu[$k]); }}
        }
}
