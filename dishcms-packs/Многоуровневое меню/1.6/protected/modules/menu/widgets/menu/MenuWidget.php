<?php 
/**
 * Menu widget
 * 
 */
namespace menu\widgets\menu;

use \AttributeHelper as A;

class MenuWidget extends BaseMenuWidget
{
	/**
	 * Menu plugin id
	 * @var string
	 */
	public $plugin;
	
	/**
	 * Plugin options
	 * @var array
	 */	
	public $options = array(); 

	/**
	 * Widget configuration
	 * @var array
	 */
	protected $config = array();
	
	/**
	 * Plugin assets
	 * @var array
	 */
	protected $assets = array();
	
	/**
	 * (non-PHPdoc)
	 * @see \menu\widgets\BaseMenuWidget::init()
	 */
	public function init()
	{
		parent::init();
	
		// set id
		$this->id = $this->plugin . '-' . $this->id; 
		
		// load config
		$configFile = __DIR__ . DIRECTORY_SEPARATOR . 'configs' . DIRECTORY_SEPARATOR . 'menu.php';
		$this->config = is_file($configFile) ? include($configFile) : array();

		if(isset($this->config['plugins'][$this->plugin])) {
			// set plugin properties
			foreach($this->config['plugins'][$this->plugin] as $property=>$data) {
				if(property_exists($this, $property)) 
					$this->$property = $data;
				
				// notice error exception
				if($property == 'id') { 
					throw new \ErrorException('В конфигурации задано значение параметру id, 
						что может привести к ошибке при двойном использовании виджета на странице', 0, E_NOTICE);
				}
			} 
			
			// publish plugin assets
			\AssetsHelper::publishAssets(array(
				'path' 	=> \Yii::getPathOfAlias("menu.widgets.menu.assets.plugins.{$this->plugin}"),
				'js' 	=> A::get($this->assets, 'js', array()),
				'css'	=> A::get($this->assets, 'css', array())
			));
		}		
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \menu\widgets\BaseMenuWidget::run()
	 */
	public function run()
	{
		$tree = $this->getTree(true);
		$menu = $this->renderItems($tree, 0, true);
		
		$this->render($this->getView(), compact('menu'));
	}
	
	protected function getView()
	{
		$view = isset($this->config['plugins'][$this->plugin]['view'])
			? $this->config['plugins'][$this->plugin]['view']
			: 'default';
		
		$path = \Yii::getPathOfAlias('menu.widgets.menu.views.plugins.' . $this->plugin);
		$filename = $path . DIRECTORY_SEPARATOR . $view . '.php';
		
		// @todo not work "plugins\\{$this->plugin}\\{$view}"
		return is_file($filename) ? "application.modules.menu.widgets.menu.views.plugins.{$this->plugin}.{$view}" : 'menu';   
	}	
}