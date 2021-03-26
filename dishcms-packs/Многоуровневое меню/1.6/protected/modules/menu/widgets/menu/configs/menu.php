<?php
/**
 * Main configuration of menu widgets for menu module
 * 
 */
return array(
	/**
	 * @section plugins
	 * Each plugin desctiption as:
	 * 	<plugin id> => array(
	 * 		// for publish assets of base path "<menu module>\menu\widgets\assets\plugins\<plugin id>".
	 * 		'assets' => array('js' => array(), 'css' => array()),
	 * 		// view template name of base path "<menu module>\menu\widgets\views\plugins\<plugin id>".
	 * 		'view' => <view template>,
	 * 		// !NOTE!: Все свойства класса виджета можно переопределить и задать здесь же. 
	 * 		// Например MenuWidget::$options (plugin options)
	 * 		'options' => array()
	 *  )
	 */
	'plugins' => array(
		'dishmanSimpleDropMenu' => array(
			'assets' => array(
				'js' => array('dishmanSimpleDropMenu.js')
			),
		),
		'blank' => array(
		),
		'amenu' => array(
			'assets' => array(
				'js' => array('amenu.js'),
				'css' => array('amenu.css')
			),
			'options' => array(
				'speed' => 100, 
				'animation' => 'none' // animation: show, fade, slide, wind, none
			),
			'cssClass' => 'amenu-widget-list'
		),
	)
);