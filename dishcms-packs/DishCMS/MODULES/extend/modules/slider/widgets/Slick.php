<?php
/**
 * Виджет слайдера, используеющего плагин Slick.
 * 
 */
namespace extend\modules\slider\widgets;

class Slick extends \extend\modules\slider\components\base\SliderWidget
{
    /**
     * {@inheritDoc}
     * @see \extend\modules\slider\components\base\SliderWidget::$tagOptions
     */
    public $tagOptions=['class'=>'slick__slider'];
    
    /**
     * {@inheritDoc}
     * @see \extend\modules\slider\components\base\SliderWidget::$tagOptions
     */
    public $itemsTagName='div';
    
    /**
     * {@inheritDoc}
     * @see \extend\modules\slider\components\base\SliderWidget::$itemsOptions
     */
    public $itemsOptions=['class'=>'slick__slider-items'];
    
    /**
     * {@inheritDoc}
     * @see \extend\modules\slider\components\base\SliderWidget::$tagOptions
     */
    public $itemTagName='div';
    
    /**
     * {@inheritDoc}
     * @see \extend\modules\slider\components\base\SliderWidget::$itemOptions
     */
    public $itemOptions=['class'=>'slick__slider-item'];
    
    /**
     * {@inheritDoc}
     * @see \extend\modules\slider\components\base\SliderWidget::$css
     */
    public $css='vendors/slick.min.css';
    
    /**
     * {@inheritDoc}
     * @see \extend\modules\slider\components\base\SliderWidget::$js
     */
    public $js='vendors/slick.min.js';

	/**
     * {@inheritDoc}
     * @see \extend\modules\slider\components\base\SliderWidget::$config
     */
    public $config='default';
    
    /**
     * {@inheritDoc}
     * @see \extend\modules\slider\components\base\SliderWidget::init()
     */
    public function init()
    {
        parent::init();

        $this->registerScript('$("'.$this->getJQueryItemsSelector().'").slick('.\CJavaScript::encode($this->options).');');   
    }
}
