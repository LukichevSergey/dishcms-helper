<?php
/**
 * ОНЛАЙН Заявка
*
* 2.автокредит
*
* 1 ФИО
* 2 Дата рождения
* 3 Контактный телефон
* 4 Фактическое место проживания
* 5 Стоимость транспортного средства
* 6 Категория транспортного средства
* 7 Марка и год выпуска транспортного средства
* 8 Сумма первоначального взноса
*/
return array(
	'avto_credit' => array(
		'title' => 'Онлайн-заявка: Автокредит',
		'short_title' => 'Автокредит',
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
			'address' => array(
				'label' => 'Фактическое место проживания',
				'type' => 'Text',
				'rules' => array(
					array('address', 'required'),
					array('address', 'length', 'max'=>1500)
				),
			),
			'vehicle_cost' => array(
				'label' => 'Стоимость транспортного средства',
				'type' => 'Numerical',
				'rules' => array(
					array('vehicle_cost', 'required')
				),
			),
			'vehicle_category' => array(
				'label' => 'Категория транспортного средства',
				'type' => 'String',
				'rules' => array(
					array('vehicle_category', 'required')
				),
			),
			'vehicle_model_and_year' => array(
				'label' => 'Марка и год выпуска транспортного средства',
				'type' => 'String',
				'rules' => array(
					array('vehicle_model_and_year', 'required')
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