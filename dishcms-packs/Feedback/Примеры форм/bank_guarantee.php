<?php
/**
 * ОНЛАЙН Заявка
 *
 * Банковская гарантия
 *
 * 1. Имя
 * 2. Контактный телефон
 * 3. Срок ведения бизнеса (мес.)
 * 4. Сфера бизнеса (торговля, оказание услуг и т.п.)
 * 5. Сумма требуемой гарантии, тыс. руб.
 */
return array(
	'bank_guarantee' => array(
		'title' => 'Онлайн-заявка: Банковская гарантия',
		'short_title' => 'Банковская гарантия',
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
			'sum_of_money' => array(
				'label' => 'Сумма требуемой гарантии, тыс. руб.',
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