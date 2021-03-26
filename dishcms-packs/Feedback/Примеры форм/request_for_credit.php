<?php
/**
 * ОНЛАЙН Заявка (общая)
 *
 * 1 Имя
 * 2 Телефон
 * 3 Тип кредита
 */
return array(
	'request_for_credit' => array(
		'title' => 'Онлайн-заявка',
		'short_title' => 'Онлайн-заявка',
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
				'placeholder' => 'Введите имя',
				'rules' => array(
					array('name', 'required')
				),
			),
			'phone' => array(
				'label' => 'Телефон',
				'type' => 'Phone',
				'rules' => array(
					array('phone', 'required')
				),
			),
			'type' => array(
				'label' => 'Тип кредита',
				'title' => 'Выберите тип кредита:',
				'type' => 'List',
				'rules' => array(
					array('type', 'required'),
				),
				'items'=>array(
					'working_capital' => 'Кредит без залога на пополнение оборотных средств',
					'pensioner' => 'Кредит пенсионерам',
					'overdraft' => 'Овердрафт',
					'loan_with_collateral_for_any_purpose' => 'Кредит с залогом на любы цели',
					'hypothec' => 'Ипотека',
					'consumer_credit' => 'Потребительский кредит',
					'credit_card' => 'Кредитные карты',
					'bank_refinancing' => 'Рефинансирование кредитов в других банках',
					'bank_guarantee' => 'Банковская гарантия',
					'avto_credit' => 'Автокредит',
					'commercial_real_estate' => 'Кредит на покупку коммерческой недвижимости',
					'equipment_credit' => 'Кредит на покупку оборудования, транспорта и спецтехники',
					'express_credit_entrepreneur_on_personal_goals' => 'Экспресс-кредит предпринимателю на личные цели',
				),
			),
		),
		// Control buttons
		'controls' => array(
			'send' => array(
				'title' => 'Отправить заявку'
			),
		),
	),
);