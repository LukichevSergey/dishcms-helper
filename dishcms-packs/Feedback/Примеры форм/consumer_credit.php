<?php
/**
 * ОНЛАЙН Заявка
 * 
 * 1.потребительский кредит
 *		
 * 1 ФИО
 * 2 Дата рождения
 * 3 Контактный телефон
 * 4 Желаемая сумма
 * 5 Фактическое место проживания
 */
return array(
	'consumer_credit' => array(
		'title' => 'Онлайн-заявка: Потребительский кредит',
		'short_title' => 'Потребительский кредит',
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
		),
		// Control buttons
		'controls' => array(
			'send' => array(
				'title' => 'Отправить'
			),
		),
	),
);