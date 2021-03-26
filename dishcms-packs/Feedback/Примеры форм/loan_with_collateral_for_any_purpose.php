<?php
/**
 * ОНЛАЙН Заявка
 *
 * Кредит с залогом на любы цели
 *
 * 1. Имя
 * 2. Контактный телефон
 * 3. Форма ведения бизнеса (ИП,ООО)
 * 4. Срок ведения бизнеса (мес.)
 * 5. Сфера бизнеса (торговля, оказание услуг и т.п.)
 * 6. Место ведения бизнеса (населенный пункт)
 * 7. Желаемая сумма кредита
 * 8. Возможное обеспечение по кредиту (виды обеспечения)
 */
return array(
	'loan_with_collateral_for_any_purpose' => array(
		'title' => 'Онлайн-заявка: Кредит с залогом на любы цели',
		'short_title' => 'Кредит с залогом на любы цели',
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
			'business_form' => array(
				'label' => 'Форма ведения бизнеса (ИП,ООО)',
				'type' => 'String',
				'rules' => array(
					array('business_form', 'required')
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
			'possible_collateral' => array(
				'label' => 'Возможное обеспечение по кредиту (виды обеспечения)',
				'type' => 'Text',
				'rules' => array(
					array('possible_collateral', 'required'),
					array('possible_collateral', 'length', 'max'=>1500)
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
