<?php
/**
 * Helper for assets.
 */
use \AttributeHelper as A;

class AssetHelper extends \CComponent
{
	/**
	 * Register script files
	 *
	 * @see CClientScript::registerScriptFile
	 *
	 * @param string|array $files
	 *
	 * @return void
	 */
	public static function registerScriptFiles($files)
	{
		if(!is_array($files)) $files = array($files);
		 
		$cs = Yii::app()->clientScript;
		foreach ($files as $file) {
			$cs->registerScriptFile($file);
		}
	}
	
	/**
	 * Register CSS files
	 *
	 * @see CClientScript::registerCssFile
	 *
	 * @param string|array $files
	 *
	 * @return void
	 */
	public static function registerCssFiles($files)
	{
		if(!is_array($files)) $files = array($files);
	
		$cs = Yii::app()->clientScript;
		foreach ($files as $file) {
			$cs->registerCssFile($file);
		}
		 
	}
	
	/**
	 * Publish assets.
	 *
	 * @param string|array $path Путь до публикуемого assets.
	 * Может быть передан массив, вида:
	 * 	array('path'=> '<path>', 'js'=>(string|array), 'css'=>(string|array))
	 * ВАЖНО: Если $path передан массивом, то параметры $js и $css игнорируются.
	 * @param string|array|null $js Файл (массив файлов) JS-скрипта(ов), который(ые) необходимо подключить.
	 * @param string|array|null $css Файл (массив файлов) CSS-стилей, который(ые) необходимо подключить.
	 *
	 * @return an absolute URL to the published asset
	 * @see CAssetManager::publish()
	 */
	public static function publish($path, $js=null, $css=null)
	{
		if(is_array($path)) {
			$js = A::get($path, 'js');
			$css = A::get($path, 'css');
			$path = A::get($path, 'path');
		}
		 
		if(!$path || !is_dir($path)) return false;
		 
		$baseUrl = Yii::app()->assetManager->publish($path, false, -1, (defined('YII_DEBUG') && (YII_DEBUG === true)));
	
		// Регистрируем скрипты и стили
		$func = function(&$item, $key) use ($baseUrl) { $item = "{$baseUrl}/$item"; };
		 
		if($js) {
			if(!is_array($js)) $js = array($js);
			array_walk($js, $func);
			self::registerScriptFiles($js);
		}
		 
		if($css) {
			if(!is_array($css)) $css = array($css);
			array_walk($css, $func);
			self::registerCssFiles($css);
		}
		 
		return $baseUrl;
	}
}