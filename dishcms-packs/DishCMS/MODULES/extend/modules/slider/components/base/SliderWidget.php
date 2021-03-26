<?php
/**
 * Базовый класс для виджетов слайдера
 * @author kontur
 *
 */
namespace extend\modules\slider\components\base;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HFile;
use common\components\helpers\HCache;
use common\components\helpers\HTools;
use common\components\helpers\HHash;
use extend\modules\slider\components\helpers\HSlider;
use extend\modules\slider\models\Slider;
use extend\modules\slider\models\Slide;

class SliderWidget extends \common\components\base\Widget
{
	/**
	 * @var string символьный код слайдера. 
	 */
	public $code;
	
	/**
	 * @var string имя контейнера слайдера. Если не задано, будет сгенерирован.
	 * По умолчанию (NULL) - не задано.
	 */
	public $container=null;
	
	/**
	 * @var string имя категории сортировки слайдов.
	 * По умолчанию "slider_slides".
	 */
	public $sort='slider_slides';
	
	/**
	 * @var string|NULL|FALSE ключ сортировки слайдов.
	 * По умолчанию (NULL) в качестве ключа будет использован 
	 * идентификатор слайдера.
	 * Если будет передано FALSE, ключ использован не будет. 
	 */
	public $sortKey=null;
	
	/**
	 * @var string|null имя файла конфигурации настроек виджета.
	 * Формат файла конфигурации:
	 * array(
	 *     'view'=>шаблон отображения,
	 *     'options'=>(array) массив параметров инициализации плагина слайдера
	 *     ... все доступные public свойства класса виджета
	 * )
	 */
	public $config=null;
	
	/**
	 * (non-PHPdoc)
	 * @see \common\components\base\Widget::$tagOptions
	 */
	public $tagOptions=['class'=>'slider'];
	
	/**
	 * @var string имя тэга списка элементов. По умолчанию "ul".
	 */
	public $itemsTagName='ul';
	
	/**
	 * @var array дополнительные HTML-атрибуты для тэга списка элементов. 
	 * По умолчанию пустой массив.
	 */
	public $itemsOptions=[];	
	
	/**
	 * @var string имя тэга элемента списка. По умолчанию "li".
	 */
	public $itemTagName='li';
	
	/**
	 * @var array атрибуты для тэга элемента списка. По умолчанию пустой массив.
	 */
	public $itemOptions=[];	
	
	/**
	 * @var array дополнительные HTML-атрибуты для ссылки
	 */
	public $linkOptions=[];
	
	/**
	 * @var array дополнительные HTML-атрибуты для изображения
	 */
	public $imageOptions=[];

	/**
	 * @var bool|null изображение адаптивное
	 */
	public $imageAdaptive=null;
	
	/**
	 * @var boolean подключать js библиотеки слайдера.
	 * По умолчанию (TRUE) - подключать.
	 */
	public $jsLoad=true;
	
	/**
	 * @var boolean инициализировать слайдер. 
	 * По умолчанию (TRUE) - инициализировать.
	 */
	public $jsInit=true;
	
	/**
	 * @var string|boolean файлы скриптов. Может быть передано (FALSE), либо пустое значение, 
	 * в этом случае, скрипты подключены не будут. 
	 */
	public $js=false;
	
	/**
	 * @var string|boolean файл стилей. Может быть передано (FALSE), либо пустое значение, 
	 * в этом случае, стили подключены не будут. 
	 */
	public $css=false;
	
	/**
	 * @var string|boolean файл LESS-стилей. Может быть передано (FALSE), либо пустое значение, 
	 * в этом случае, стили подключены не будут. 
	 */
	public $less=false;
	
	/**
	 * @var array параметры для инициализации плагина слайдера.
	 */
	public $options=[];
	
	/**
	 * @var string|callable|null дополнительный код слайда.
	 * Может быть передана callable значение function($slide, $widget){}. 
	 */
	public $content=null;

	/**
 	 * @var string|null тэг обертки блока дополнительного контента слайда
	 */
	public $contentTag=null;

	/**
     * @var array атрибуты для тэга обертки блока дополнительного контента слайда.
     */
    public $contentOptions=[];
	
	/**
	 * @var boolean использовать кэширование.
	 */
	public $cache=true;
	
	/**
	 * @var integer время кэширования в секундах. 
	 * По умолчанию 60 секунд.
	 */
	public $cacheTime=HCache::MINUTE;

	/**
	 * @var string имя шаблона представления по умолчанию.
	 */
	public $view='default';
	
	/**
	 * @var string|array путь к ресурсам виджета для публикации
	 */
	public $assetsPath=null;
	
	/**
	 * @var \extend\modules\slider\models\Slider модель слайдера.
	 */
	protected $slider;
	
	/**
	 * @var array[\extend\modules\slider\models\Slide] массив моделей слайдов.
	 */
	protected $slides=[];
	
	/**
	 * (non-PHPdoc)
	 * @see \CWidget::init()
	 */
	public function init()
	{
		if(!$this->getSlider()) {
			return false;
		}
		
		$this->normalizeHtmlOptions();
		
		$this->loadConfig();
		
		if(!$this->container) {
			$this->container='slider_'.$this->code;
		}
		
		if(!A::exists('id', $this->htmlOptions)) {
			$this->htmlOptions['id']=$this->container;
		}
		
		$this->publishAssets();
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \common\components\base\Widget::run()
	 */
	public function run()
	{
		if(!$this->getSlider() || !$this->getSlides()) {
			return false;
		}
				
		if($this->cache) {
			if(!($content=Y::cache()->get($this->getCacheId()))) {
				$content=$this->render($this->view, $this->params, true);
				Y::cache()->set($this->getCacheId(), $content, $this->cacheTime);
			}
			echo $content;
		}
		else {
			$this->render($this->view, $this->params);
		}
	}
	
	/**
	 * Регистрация javascript кода.
	 * @param string $code код скрипта.
	 * @param boolean $forcy принудительно зарегистрировать скрипт.
	 * По умолчанию (FALSE) будет опубликован, если SliderWidget::$jsInit
	 * установлено в TRUE.
	 * @param string|null $id идентификатор кода. 
	 * По умолчанию (NULL) будет взят из параметра SliderWidget::$container
	 */
	public function registerScript($code, $forcy=false, $id=null)
	{
	    if($forcy || $this->jsInit) {
	        if(!$id) $id=$this->container;
	        Y::js($id, $code, \CClientScript::POS_READY);
	    }
	}
	
	/**
	 * Получить jQuery выражение выборки DOM-элемента списка слайдов. 
	 * @return string
	 */
	public function getJQueryItemsSelector()
	{
	    if(A::exists('class', $this->itemsOptions)) {
	        $selector='.' . preg_replace('/\s+/', '.', $this->itemsOptions['class']);
	    }
	    else {
	        if(!A::exists('id', $this->itemsOptions)) $this->itemsOptions['id']=HHash::u('id');
	        $selector='#' . $this->itemsOptions['id'];
	    }
	    
	    return $selector;
	}
	
	/**
	 * Получить модель слайдера.
	 * @return \extend\modules\slider\models\Slider
	 */
	public function getSlider()
	{
		if(!$this->slider) {
		    $this->slider=HSlider::getSlider($this->code, ['scopes'=>['activly', 'utcache'=>$this->cacheTime]]);
		}
		
		return $this->slider;
	}
	
	/**
	 * Получить слайды модели слайдера.
	 * @return array
	 */
	public function getSlides()
	{
		if(!$this->slides && $this->getSlider()) {
			if($this->sortKey === false) $this->sortKey=null;
			elseif($this->sortKey === null) $this->sortKey=$this->getSlider()->id;
			
			$this->slides=$this->getSlider()->slides([
				'scopes'=>[
					'scopeSort'=>[$this->sort, $this->sortKey, false, 'slides'], 
				    'utcache'=>[$this->cacheTime, ['condition'=>'slider_id=:id', 'params'=>[':id'=>$this->getSlider()->id]]]
				]
			]);
		}
		
		return $this->slides;
	}
	
	/**
	 * Получить имя конфигурации слайдера.
	 * @return number
	 */
	public function getConfig()
	{
	    $config=$this->config;
	    
	    if(!$config) {
	        $config=$this->slider->getOption('config');
	    }
	    
	    return $config;
	}	
	
	/**
	 * Получить ширину изображения слайдера.
	 * @return number
	 */
	public function getWidth()
	{
		return (int)$this->slider->getOption('width') ?: Slider::WIDTH;
	}
	
	/**
	 * Получить высоту изображения слайдера.
	 * @return number
	 */
	public function getHeight()
	{
		return (int)$this->slider->getOption('height') ?: Slider::HEIGHT;
	}
	
	/**
	 * Получить значение пропорционального преобразования изображения слайдера.
	 * @return boolean
	 */
	public function isProportional()
	{
		return $this->slider->isOptionYes('proportional', true);
	}

	/**
     * Получить значение параметра использования адаптивного изображения слайдера.
     * @return boolean
     */
    public function isAdaptive()
    {
		if($this->imageAdaptive !== null) {
			return (bool)$this->imageAdaptive;
		}

		return $this->slider->isOptionYes('adaptive');
    }
    
    /**
     * Получить значение параметра использования дополнительного текста для слайда.
     * @return boolean
     */
    public function isDescription()
    {
        return $this->slider->isOptionYes('description');
    }
    
    /**
     * Получить значение параметра использования ссылки для слайда.
     * @return boolean
     */
    public function isLink()
    {
        return $this->slider->isOptionYes('link', true);
    }
    
    /**
     * Получить тэг <img> для слайда
     * @param Slide $slide модель слайда
     * @return string
     */
    public function getSlideImage($slide)
    {
        return $slide->imageBehavior->img($this->getWidth(), $this->getHeight(), $this->isProportional(), $this->imageOptions, false, '', $this->isAdaptive());
    }
    
    /**
     * Получить url изображения слайда
     * @param Slide $slide модель слайда
     * @return string
     */
    public function getSlideSrc($slide)
    {
        return $slide->imageBehavior->getTmbSrc($this->getWidth(), $this->getHeight(), $this->isProportional(), false, '', $this->isAdaptive());
    }
	
	/**
	 * Загрузка конфигурации плагина
	 */
	protected function loadConfig()
	{
	    $config=$this->getConfig();
	    if($config) {
	        if(strpos($config, '.') !== false) {
	            $config=HFile::includeByAlias($config, []);
	        }
	        else {
	            $folder=HTools::getShortClassName(get_called_class(), true);
	            $config=HFile::includeByAlias(
	                "extend.modules.slider.widgets.configs.{$folder}.{$config}",
	                [],
	                ['widget'=>$this]
	            );
	        }
	        
	        if(!is_string($config) && is_callable($config)) {
	            $config=call_user_func($config, $this);
	        }
	        
	        if(is_array($config)) {
	            foreach($config as $property=>$value) {
	                if(property_exists($this, $property)) {
	                    if(($property == 'options') && is_array($this->options)) {
	                        $this->$property=A::m($value, $this->options);
	                    }
	                    else {
	                       $this->$property=$value;
	                    }
	                }
	            }
	        }
	        
	        $this->normalizeHtmlOptions();
	    }
	}
	
	/**
	 * Получить идентификатор кэша.
	 */
	protected function getCacheId()
	{
		return $this->container . $this->code;
	}
	
	/**
	 * Публикация ресурсов виджета
	 * @param string|array $path путь к ресурсам виджета
	 * @param string|array $js javascript файлы для публикации
	 * @param string|array $css CSS-файлы стилей для публикации
	 * @param string|array $less LESS-файлы стилей для публикации
	 * @param boolean $forcy принудительно публиковать ресурсы
	 */
	protected function publishAssets()
	{
	    $js=false;
	    if($this->jsLoad) $js=$this->js;
	    
	    return $this->publish($js, A::m(A::toa($this->css), A::toa($this->less)));
	}
	
	/**
	 * Нормализовать значения параметров htmlOptions
	 * @param array $names массив имен параметров. 
	 * Если пуст будет использован массив основных имен параметров:
	 * ['htmlOptions', 'tagOptions', 'itemsOptions', 'itemOptions', 'linkOptions', 'imageOptions']
	 */
	protected function normalizeHtmlOptions($names=[])
	{
	    if(empty($names)) {
	        $names=['htmlOptions', 'tagOptions', 'itemsOptions', 'itemOptions', 'linkOptions', 'imageOptions'];
	    }
	    
	    foreach($names as $property) {
	        if(is_string($this->$property)) {
	            $this->$property=['class'=>$this->$property];
            }
        }
	}
}
