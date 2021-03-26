<?php
/**
 * ОНЛАЙН Заявка
 *
 * Кредит на покупку коммерческой недвижимости
 *
 * 1. Имя
 * 2. Контактный телефон
 * 3. Срок ведения бизнеса (мес.)
 * 4. Сфера бизнеса (торговля, оказание услуг и т.п.)
 * 5. Объект недвижимости (офис, склад и т.п.-указать)
 * 6. Стоимость приобретаемого объекта, млн. руб
 * 7. Собственный первоначальный взнос, млн. руб.
 */
return array(
	'commercial_real_estate' => array(
		'title' => 'Онлайн-заявка: Кредит на покупку коммерческой недвижимости',
		'short_title' => 'Кредит на покупку коммерческой недвижимости',
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
			'real_estate' => array(
				'label' => 'Объект недвижимости (офис, склад и т.п.-указать)',
				'type' => 'String',
				'rules' => array(
					array('real_estate', 'required')
				),
			),
			'sum_of_money' => array(
				'label' => 'Стоимость приобретаемого объекта, млн. руб',
				'type' => 'Numerical',
				'rules' => array(
					array('sum_of_money', 'required')
				),
			),
			'initial_deposit' => array(
				'label' => 'Собственный первоначальный взнос, млн. руб.',
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
