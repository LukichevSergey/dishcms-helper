<?php
/**
 * Helper for CUrlManager
 * 
 * @version 1.2
 */
class UrlManagerHelper extends CComponent
{
	/**
	 * Added module url rules, before CWebApplication::processRequest
	 * 
	 * Список модулей из которых будут добавлятся правила можно объявить 
	 * в настройках приложения в разделе 
	 * 'params'=>array(
	 * 		'UrlManagerHelper'=>array(
	 * 			'modules'=><список модулей>,
	 * 			'defaultRules'=><boolean>
	 * 		),
	 *  	...
	 *  )
	 *  
	 * 'modules': Список можно передать как массив со список имен, либо строкой разделенных запятой.
	 * 'defaultRules': Правила приложения по умолчанию. Будут добавлены в конец.
	 * 
	 * Если список не задан, то получаются все модули.
	 * 
	 * Thanks http://yiiframework.ru/forum/viewtopic.php?f=8&t=1304
	 */
	public function onBeginRequest()
	{
		// get modules
		if(isset(Yii::app()->params['UrlManagerHelper']['modules'])) {
			$modules = Yii::app()->params['UrlManagerHelper']['modules'];
			if(is_string($modules)) $modules = explode(',', str_replace(' ', '', $modules));
			elseif(!is_array($modules)) $modules = array(); 
			$modules = array_flip($modules);
		}
		else {
			$modules = Yii::app()->getModules();
		}
		
		foreach($modules as $name=>$data) {
			if(Yii::app()->hasModule($name)) {
				$module = Yii::app()->getModule($name);
				if(isset($module->urlRules))
					Yii::app()->getUrlManager()->addRules(method_exists($module, 'getUrlRules') ? $module->getUrlRules() : $module->urlRules);
			}
		}
		
		// add default rules
		if(isset(Yii::app()->params['UrlManagerHelper']['defaultRules'])) {
			Yii::app()->getUrlManager()->addRules(Yii::app()->params['UrlManagerHelper']['defaultRules']);
		}
		
		return true;
	}
}
