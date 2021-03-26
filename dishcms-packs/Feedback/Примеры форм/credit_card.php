<?php
/**
 * ОНЛАЙН Заявка
 *
 * 5.кредитные карты
 *
 * 1 ФИО
 * 2 Дата рождения
 * 3 Контактный телефон
 * 4 Желаемая сумма (лимит кредитной карты)
 * 5 Фактическое место проживания 
 */
return array(
	'credit_card' => array(
		'title' => 'Онлайн-заявка: Кредитные карты',
		'short_title' => 'Кредитные карты',
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
				'label' => 'Желаемая сумма (лимит кредитной карты)',
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