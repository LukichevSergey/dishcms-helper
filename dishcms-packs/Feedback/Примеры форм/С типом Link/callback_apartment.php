<?php
/**
 * Обратный звонок
*
* 1 Имя
* 3 Контактный телефон
*/
return array(
	'callback_apartment' => array(
		'title' => 'Заявка на квартиру',
		'short_title' => 'Заявка на квартиру',
		// Options
		'options' => array(
			'useCaptcha' => false,
			'sendMail' => true,
			'emptyMessage' => 'Заявок нет',
		),
		// Form attributes
		'attributes' => array(
			'name' => array(
				'label' => 'Имя',
				'type' => 'String',
				'placeholder' => 'Имя',
				'rules' => array(
					array('name', 'required')
				),
			),
			'phone' => array(
				'label' => 'Контактный телефон',
				'type' => 'Phone',
				'placeholder' => 'Телефон',
				'rules' => array(
					array('phone', 'required')
				),
			),
			'email' => array(
				'label' => 'E-mail',
				'type' => 'Email',
				'placeholder' => 'E-mail',
				'rules' => array(
					array('email', 'required')
				)
			),
			'apartmentLink' => array(
				'label' => 'Ссылка',
				'type' => 'Link',
				'rules' => array(
					array('apartmentLink', 'required')
				)
			),
		),
		// Control buttons
		'controls' => array(
			'send' => array(
				'title' => 'Оставить заявку'
			),
		),
	),
);