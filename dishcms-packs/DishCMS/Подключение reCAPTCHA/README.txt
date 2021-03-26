На примере Вопрос-Ответ

1) Скопировать расширение reCaptcha2 в /protected/extensions


2) В параметры \Yii::app()->params

'params'=>[
	...
	'reCaptcha2PrivateKey'=>'<Секретный ключ>',
	'reCaptcha2PublicKey'=>'<Публичный ключ>',
]

3) В модели Question.php
public $captcha;

function rules() 
{
	...
	['captcha', 'ext.reCaptcha2.SReCaptchaValidator', 'secret' => \Yii::app()->params['reCaptcha2PrivateKey'], 'message' => 'Не пройдена проверка.']
}

4) В шаблоне формы добавления question/_form.php
	<div class="row js-captcha">
		<?php Yii::import('application.extensions.reCaptcha2.SReCaptcha'); ?>
		<?php $this->widget('SReCaptcha', [
	        'name' => 'captcha', //is requred
	        'siteKey' => \Yii::app()->params['reCaptcha2PublicKey'], //is requred
	        'widgetOptions' => ['class' => 'col-lg-3'],
	        'theme' => SReCaptcha::THEME_LIGHT,
	        'size' => SReCaptcha::SIZE_NORMAL,
	        'jsCallback' => '',
	        'jsExpiredCallback' => 'console.log("reCaptcha is expired!");',
	        'lang'=>'ru'
		]); ?>
	</div>

5) В шаблоне question/index.php
	function submitForm(form, hasError) {
        if (!hasError) {
            $.post($(form).attr('action'), $(form).serialize(), function(data) {
                if (data == 'ok') $('#question-form-div').html('<h2>Ваш вопрос отправлен</h2>');
                else if(data == 'captcha') $('#question-form-div .js-captcha').addClass("error");
                else $('#question-form-div').html('<h2>При отправке вопроса возникла ошибка</h2>');
            });
        }
    }
