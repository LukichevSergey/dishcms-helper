<?php
/**
 * ОНЛАЙН Заявка
 *
 * 4.кредит пенсионерам
 * 
 * 1 ФИО
 * 2 Дата рождения
 * 3 Контактный телефон
 * 4 Желаемая сумма
 * 5 Фактическое место проживания
 * 6 Размер ежемесячного дохода (пенсии, заработка).
 */
return array(
	'pensioner' => array(
		'title' => 'Онлайн-заявка: Кредит пенсионерам',
		'short_title' => 'Кредит пенсионерам',
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
			'monthly_income' => array(
				'label' => 'Размер ежемесячного дохода (пенсии, заработка)',
				'type' => 'Numerical',
				'rules' => array(
					array('monthly_income', 'required')
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