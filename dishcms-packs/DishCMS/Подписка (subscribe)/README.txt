
ПОДКЛЮЧЕНИЕ МОДУЛЯ ПОДПИСКИ

-----------------------------------------------
Скопировать файлы
-----------------------------------------------
extensions/phpMailer если нет.
modules/*
migrations/*

-----------------------------------------------
РАЗДЕЛ АДМИНИСТРИРОВАНИЯ
-----------------------------------------------
1) Пункт меню в 
      	[
      		'label'=>'Рассылка', 
      		'url'=>['subscribe/index']
   		],

-----------------------------------------------
ПУБЛИЧНАЯ ЧАСТЬ
-----------------------------------------------
1) Стили
.module-email {

	.btn {
		font-size: 10px;
		padding: 9px 8px;
	}
	form#user-form {
 	   	text-align: center;
    	color: #719133;
	}
}	

.module-email__head {
	font-size: 14px;
	font-weight: 700;
	text-align: center;
	margin-bottom: 10px;
}

2) Код
		<div class="module module-email">
			<div class="module-email__head">Email - рассылка</div>
			<? $this->widget('\subscribe\widgets\SubscribeWidget'); ?>
		</div>

3) В Yii::app()->params
    'domain' => 'example.com',

4) defaults.php
	'import'=>array(
        'ext.phpMailer.*',
        'ext.phpMailer.PHPMailer'
	),

	'modules'=>array(
		'subscribe',
