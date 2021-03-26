<?php
/**
 * Виджет слайдера, используеющего плагин OwlCarousel.
 */
namespace extend\modules\slider\widgets;

use common\components\helpers\HArray as A;

class OwlCarousel extends \extend\modules\slider\components\base\SliderWidget
{
	/**
	 * {@inheritDoc}
	 * @see \extend\modules\slider\components\base\SliderWidget::$js
	 */
	public $js='vendors/owl.carousel.min.js';
	
	/**
	 * {@inheritDoc}
	 * @see \extend\modules\slider\components\base\SliderWidget::$css
	 */
	public $css=['vendors/owl.carousel.min.css', 'vendors/owl.theme.default.min.css'];	
	
	/**
	 * {@inheritDoc}
	 * @see \extend\modules\slider\components\base\SliderWidget::$config
	 */
    public $config='default';
	
	/**
	 * {@inheritDoc}
	 * @see \CWidget::init()
	 */
	public function init()
	{
		parent::init();
		
		$this->itemsOptions['class']=trim(A::get($this->itemsOptions, 'class', '') . ' owl-carousel');

		$this->registerScript('$("'.$this->getJQueryItemsSelector().'").owlCarousel('.\CJavaScript::encode($this->options).');');
	}
}