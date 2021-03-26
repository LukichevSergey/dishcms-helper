<?php
class CarouFredSelSlider extends CWidget
{
	public $id=null;

	public $selector=null;

	public $data;
	
	public $attributeLink='link';
	
	public $attributeSrc='src';
	
	public $view='carou_fred_sel';

	public $options=array();
	
	public function init()
	{
		if(!$this->id) $this->id=uniqid('carouFredSel');
		if(!$this->selector) $this->selector='#'.$this->id;

		Yii::app()->clientScript->registerScriptFile('/js/jquery.carouFredSel-6.2.1.js');
		Yii::app()->clientScript->registerScript(
			uniqid('carouFredSel'), 
			'if($("'.$this->selector.'").length) $("'.$this->selector.'").carouFredSel('.CJavaScript::encode($this->options).');',
			CClientScript::POS_READY
	    );
	}
	
	public function run()
	{
		$this->render($this->view);
	}
}