<?php
/**
 * /local/components/kontur/project.map/templates/index/images/set_id.php
 */
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
global $USER;
if(!empty($USER) && $USER->IsAdmin()) {
	file_put_contents(dirname(__FILE__).'/map.id.svg', preg_replace_callback('/(title="\{i\}")([^>]+)\/>/mUs', function($m) {
		static $id=0; $id++;
		return 'data-id="'.$id.'" title="'.$id.'" alt="'.$id.'"' . $m[2] . '><title>ID: '.$id.'</title></path>';
	}, file_get_contents(dirname(__FILE__).'/map.template.svg')));
	echo 'done!';
}