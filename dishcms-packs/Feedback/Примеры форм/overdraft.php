<?php
/**
 * ОНЛАЙН Заявка
 *
 * Овердрафт
 *
 * 1. Имя
 * 2. Контактный телефон
 * 3. Срок ведения бизнеса (мес.)
 * 4. Сфера бизнеса (торговля, оказание услуг и т.п.)
 * 5. Место ведения бизнеса (населенный пункт)
 * 6. Среднемесячный оборот по расчетному счету, тыс. руб.
 * 7. Желаемая сумма овердрафта
 */
return array(
	'overdraft' => array(
		'title' => 'Онлайн-заявка: Овердрафт',
		'short_title' => 'Овердрафт',
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
			'average_monthly_turnover' => array(
				'label' => 'Среднемесячный оборот по расчетному счету, тыс. руб.',
				'type' => 'Numerical',
				'rules' => array(
					array('average_monthly_turnover', 'required')
				),
			),
			'sum_of_money' => array(
				'label' => 'Желаемая сумма овердрафта',
				'type' => 'Numerical',
				'rules' => array(
					array('sum_of_money', 'required')
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