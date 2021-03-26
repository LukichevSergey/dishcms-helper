<?php
/**
 * ОНЛАЙН Заявка
 *
 * Кредит на покупку оборудования, транспорта и спецтехники
 *
 * 1. Имя
 * 2. Контактный телефон
 * 3. Срок ведения бизнеса (мес.)
 * 4. Сфера бизнеса (торговля, оказание услуг и т.п.)
 * 5. Место ведения бизнеса (населенный пункт)
 * 6. Стоимость приобретаемого оборудования, спецтехники, тыс. руб
 * 7. Собственный первоначальный взнос, тыс. руб.
 */
return array(
	'equipment_credit' => array(
		'title' => 'Онлайн-заявка: Кредит на покупку оборудования, транспорта и спецтехники',
		'short_title' => 'Кредит на покупку оборудования, транспорта и спецтехники',
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
				'placeholder' => 'Введите ваше имя',
				'rules' => array(
					array('name', 'required')
				),
			),
			'phone' => array(
				'label' => 'Контактный телефон',
				'type' => 'Phone',
				'rules' => array(
					array('phone', 'required')
				),
			),
			'business_term' => array(
				'label' => 'Срок ведения бизнеса (мес.)',
				'type' => 'Numerical',
				'rules' => array(
					array('business_term', 'required')
				),
			),
			'business_sphere' => array(
				'label' => 'Сфера бизнеса (торговля, оказание услуг и т.п.)',
				'type' => 'String',
				'rules' => array(
					array('business_sphere', 'required')
				),
			),
			'business_place' => array(
				'label' => 'Место ведения бизнеса (населенный пункт)',
				'type' => 'String',
				'rules' => array(
					array('business_place', 'required')
				),
			),
			'sum_of_money' => array(
				'label' => 'Стоимость приобретаемого оборудования, спецтехники, тыс. руб',
				'type' => 'Numerical',
				'rules' => array(
					array('sum_of_money', 'required')
				),
			),
			'initial_deposit' => array(
				'label' => 'Собственный первоначальный взнос, тыс. руб.',
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
