Добавить в init.php
<?php
AddEventHandler("main", "OnEndBufferContent", "seoRemoveJavaScriptType");
function seoRemoveJavaScriptType(&$content) 
{
   	$content=str_replace(' type="text/javascript"', "", $content);
	$content=str_replace(' type=\'text/javascript\'', '', $content);
	$content=preg_replace_callback('/<img([^>]+)>/im',function($m){
		if(!preg_match('/alt=[\'"][^"\']+["\']/i', $m[1])) return '<img'.$m[1].' alt="">';
		return '<img'.$m[1].'>';
	}, $content);
}
?>
