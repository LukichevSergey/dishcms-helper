1) Форма подключения
<div class="footer__mailing">
    <span class="footer__mailing-title">Email - рассылка:</span>
    <form action="" class="footer__mailing-form">
        <input type="text" class="footer__mailing-input" placeholder="Ваш email">
        <?= CHtml::ajaxLink('<i class="fas fa-angle-right"></i>', '/uniSender/subscribe', [
        	'type'=>'post',
        	'dataType'=>'json',
        	'beforeSend'=>'js:function(xhr){this.url+="?email="+$(".footer__mailing-input").val();return true;}',
        	'success'=>'js:function(r){if(r.success){$(".footer__mailing-form").addClass("subscribed").html("Подписка оформлена!");}else{$(".footer__mailing-input").addClass("footer__mailing-input_error");}}'
        ], ['encode'=>false, 'class'=>'footer__mailing-submit']); ?>
    </form>
</div>

2) Настройки в разделе администрирования

SettingsForm.php
// UniSender
public $unisender_apikey;
public $unisender_list_ids;

function rules() {
	...
	['unisender_list_ids, unisender_apikey', 'safe']

function attributeLabels() {
	...
	// UniSender
	'unisender_apikey'=>'UniSender. Ключ доступа к API',
	'unisender_list_ids'=>'UniSender. Идентификаторы рассылок (через запятую)',
	
	
views/settings_general.php
<div class="row">
    <?php echo $form->label($model,'unisender_apikey'); ?>
    <?php echo $form->textField($model,'unisender_apikey', array('class'=>'form-control'))?>
    <?php echo $form->error($model,'unisender_apikey'); ?>
</div>

<div class="row">
    <?php echo $form->label($model,'unisender_list_ids'); ?>
    <?php echo $form->textField($model,'unisender_list_ids', array('class'=>'form-control'))?>
    <?php echo $form->error($model,'unisender_list_ids'); ?>
</div>

