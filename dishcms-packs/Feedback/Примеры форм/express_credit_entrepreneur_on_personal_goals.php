<?php
/**
 * ОНЛАЙН Заявка
 *
 * Экспресс-кредит предпринимателю на личные цели
 *
 * 1. Имя
 * 2. Контактный телефон
 * 3. Срок ведения бизнеса (мес.)
 * 4. Сфера бизнеса (торговля, оказание услуг и т.п.)
 * 5. Место ведения бизнеса (населенный пункт)
 * 6. Желаемая сумма кредита
 */
return array(
	'express_credit_entrepreneur_on_personal_goals' => array(
		'title' => 'Онлайн-заявка: Экспресс-кредит предпринимателю на личные цели',
		'short_title' => 'Экспресс-кредит предпринимателю на личные цели',
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
				'label' => 'Желаемая сумма кредита',
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