<?php
/**
 * Виджет слайдера, используеющего плагин BxSlider.
 */
namespace extend\modules\slider\widgets;

class BxSlider extends \extend\modules\slider\components\base\SliderWidget
{
	/**
	 * {@inheritDoc}
	 * @see \extend\modules\slider\components\base\SliderWidget::$tagOptions
	 */
	public $tagOptions='bxslider';
	
	/**
	 * {@inheritDoc}
	 * @see \extend\modules\slider\components\base\SliderWidget::$js
	 */
	public $js='vendors/jquery.bxslider.min.js';
	
	/**
	 * {@inheritDoc}
	 * @see \extend\modules\slider\components\base\SliderWidget::$css
	 */
	public $css='vendors/css/jquery.bxslider.min.css';
	
	/**
	 * {@inheritDoc}
	 * @see \extend\modules\slider\components\base\SliderWidget::$config
	 */
	public $config='default';
	
	/**
	 * (non-PHPdoc)
	 * @see \CWidget::init()
	 */
	public function init()
	{
		parent::init();
		
		$this->registerScript('$("'.$this->getJQueryItemsSelector().'").bxSlider('.\CJavaScript::encode($this->options).');');
	}	
}