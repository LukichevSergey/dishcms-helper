<?php
/**
 * ОНЛАЙН Заявка
 *
 * 3. ипотека
 * 
 * 1 ФИО
 * 2 Дата рождения
 * 3 Контактный телефон
 * 4 Желаемая сумма
 * 5 Фактическое место проживания
 * 6 Объект ипотеки (Первичный рынок, вторичный рынок, гараж, загородный дом и т.д.)
 * 7 Сумма первоначального взноса
 */
return array(
	'hypothec' => array(
		'title' => 'Онлайн-заявка: Ипотека',
		'short_title' => 'Ипотека',
		// Options
		'options' => array(
			'useCaptcha' => false,
			'sendMail' => true,
			'emptyMessage' => 'Заявок нет',
		),
		// Form attributes
		'attributes' => array(
			'name' => array(
				'label' => 'ФИО',
				'type' => 'String',
				'placeholder' => 'Введите ваше имя',
				'rules' => array(
					array('name', 'required')
				),
			),
			'birthday' => array(
				'label' => 'Дата рождения',
				'type' => 'Birthday',
				'rules' => array(
					array('birthday', 'required')
				),
			),
			'phone' => array(
				'label' => 'Контактный телефон',
				'type' => 'Phone',
				'rules' => array(
					array('phone', 'required')
				),
			),
			'sum_of_money' => array(
				'label' => 'Желаемая сумма',
				'type' => 'Numerical',
				'rules' => array(
					array('sum_of_money', 'required')
				),
			),
			'address' => array(
				'label' => 'Фактическое место проживания',
				'type' => 'Text',
				'rules' => array(
					array('address', 'required'),
					array('address', 'length', 'max'=>1500)
				),
			),
			'hypothec_object' => array(
				'label' => 'Объект ипотеки (Первичный рынок, вторичный рынок, гараж, загородный дом и т.д.)',
				'type' => 'String',
				'rules' => array(
					array('hypothec_object', 'required')
				),
			),
			'initial_deposit' => array(
				'label' => 'Сумма первоначального взноса',
				'type' => 'Numerical',
				'rules' => array(
					array('initial_deposit', 'required')
				),
			),
		),
		// Control buttons
		'controls' => array(
			'send' => array(
				'title' => 'Отправить'
			),
		),
	),
);