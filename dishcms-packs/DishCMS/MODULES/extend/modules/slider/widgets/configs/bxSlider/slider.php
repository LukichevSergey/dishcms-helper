<?php
/**
 * Конфигурация для BxSlider.
 * "Слайдер"
 * 
 * @var \extend\modules\slider\widgets\BxSlider $widget  
 */
use common\components\helpers\HArray as A;

$selector='.'.preg_replace('/\s+/', '.', A::get($widget->tagOptions, 'class', 'slides'));

return [
	'view'=>'bx_slider',
	'css'=>'',
	'options'=>[
		'auto'=>true,
		'autoHover'=>true,
		'useCSS'=>false,
		'prevSelector'=>$selector,
		'nextSelector'=>$selector,
		'minSlides'=>1,
		'maxSlides'=>1,
		'moveSlides'=>1
	]		
];