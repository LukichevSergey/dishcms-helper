<?php
namespace kontur\seo;

\Bitrix\Main\Loader::includeModule('iblock');

if ( !class_exists('\Bitrix\Iblock\Template\Functions\FunctionBase') ) {
	include_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/iblock/lib/template/functions/fabric.php');
}

class SeoFunction extends \Bitrix\Iblock\Template\Functions\FunctionBase 
{
	/**
	 * @var array $config конфигурация вида array(functionName=>array(params))
	 */
    protected static $config=array();	
	protected static $initialized=false;
	
	public static function autoload_register($class)
	{
	    $class=trim( $class, '\\' );
		if(!class_exists('\\'.$class, false) && preg_match('#^kontur\\\\seo\\\\functions\\\\([a-z0-9_]+)$#i', $class, $m)) {
		    eval('namespace kontur\seo\functions;class '.$m[1].' extends \kontur\seo\SeoFunction{}');
			return true;
		}
		return false;
	}
	
	public static function getClassKey($class=null)
	{
	    if( $class === null ) {
	        $class = get_called_class();
	    }
	    
	    return trim( $class, '\\' );
	}
	
	public static function getFunctionClass($functionName)
	{
		if( preg_match('/^[a-z0-9_]+$/i', $functionName) ) {
			return '\kontur\seo\functions\\' . $functionName;
		}
		return null;
	}
	
	/**
	 * @var string $functionName имя функции
	 * @var array|callable $params дополнительные параметры. Если передан параметр типа callable, 
	 * он будет использован вместо параметра calculate.
	 * Доступы следующие параметры:
	 * "calculate" callable функция обработки calculate. По умолчанию не задана.
	 * "add_entity" boolean добавлять объект сущности в параметры. По умолчанию false.
	 */
	public static function register($functionName, $params=array())
	{
		if( $functionClass = static::getFunctionClass($functionName) ) {
			if( !static::$initialized ) {
				spl_autoload_register( array('\kontur\seo\SeoFunction', 'autoload_register') );
				static::$initialized=true;
			}
			
			$eventManager = \Bitrix\Main\EventManager::getInstance();
			$eventManager->addEventHandler('iblock', 'OnTemplateGetFunctionClass', array($functionClass, 'eventHandler'));
			
			if( is_callable($params) ) {
				$params = array( 'calculate'=>$params );
			}
			$params['function_name'] = $functionName;
			static::$config[static::getClassKey($functionClass)] = $params;
		}
	}
	
	public static function getConfigParam($name, $default=null)
	{
		if( isset(static::$config[static::getClassKey()][$name]) ) {
		    return static::$config[static::getClassKey()][$name];
		}
		return $default;
	}

	public static function eventHandler($event)
	{
		$parameters = $event->getParameters();
		if ( $parameters[0] === static::getConfigParam('function_name') ) {
			return new \Bitrix\Main\EventResult(\Bitrix\Main\EventResult::SUCCESS, get_called_class());
		}
	}
	
	public function onPrepareParameters(\Bitrix\Iblock\Template\Entity\Base $entity, $parameters=array())
	{
		$arguments = array();
		if ( (bool)static::getConfigParam('add_entity') ) {
			$arguments[] = array($entity);
		}
		
		foreach ($parameters as $parameter) {
	 		$arguments[] = $parameter->process($entity);
		}
		
		return $arguments;
	}
	
	public function calculate($parameters)
	{
		if ( is_callable(static::getConfigParam('calculate')) ) {
		    $result = $this->parametersToArray($parameters);
		    return call_user_func_array( static::getConfigParam('calculate'), array($parameters, $result) );
		}
		return '';
	}
}
