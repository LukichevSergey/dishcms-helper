Подключение подписки сервиса NotiSend

1) Скопировать папку protected

2) Добавить настройки в admin.models.SettingsForm
- свойства класса
	// NotiSend
	public $notisend_apikey;
	public $notisend_list_id;
	public $notisend_unconfirmed;

- правило валидации
	['notisend_apikey, notisend_list_id, notisend_unconfirmed', 'safe']

- подписи
	// notisend
    'notisend_apikey'=>'API ключ',
    'notisend_list_id'=>'ID группы рассылки',
    'notisend_unconfirmed'=>'Требуется подтверждение E-Mail'

3) Пример формы
<form class="mailing__form">
	<input type="text" name="subscribe_email" class="mailing__input" placeholder="Ваш e-mail">
    <?= CHtml::ajaxLink('подписаться', '/notiSend/add', [
		'dataType'=>'json',
		'beforeSend'=>'js:function(xhr){this.url+=((this.url.indexOf("?")>0)?"&":"?")+"email="+$(".header__mailing .mailing__input").val();return true;}',
		'success'=>'js:function(r){if(r.success){$(".header__mailing .mailing__form").addClass("subscribed").html(r.data.msg);}else{$(".header__mailing .mailing__input").addClass("mailing__input-error");}}'
	], ['encode'=>false, 'class'=>'mailing__submit']); ?>
</form>

