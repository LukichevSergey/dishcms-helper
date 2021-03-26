<h1>Изменение пароля администратора</h1>
<div class="row">
	<div class="col-md-12">
		<button class="btn btn-info js-generate-password" style="margin-bottom:20px">Сгенерировать пароль</button>
	</div>
</div>
<script>
$(function(){
	$(document).on("click", ".js-generate-password", function(){
		/** @link https://stackoverflow.com/a/1497512 */
		function generatePassword() { 
			var length = 12, charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789", retVal = "";
    		for (var i = 0, n = charset.length; i < length; ++i) { retVal += charset.charAt(Math.floor(Math.random() * n));  }
    		return retVal;
		}
		let pwd=generatePassword();
		$("[name='admin_models_ChangePassword[password]']").val(pwd);
		$("[name='admin_models_ChangePassword[password]']").attr('type', 'text');
		$("[name='admin_models_ChangePassword[repassword]']").val(pwd);
	});
});
</script>
<div class="form">
<? $this->widget('\common\widgets\form\ActiveForm', [
	'model'=>$model,
	'attributes'=>['password', 'repassword'],
	'types'=>[
		'password'=>'passwordField',
		'repassword'=>'passwordField',
	],
	'submitLabel'=>'Сохранить изменения',
	'submitOptions'=>['class'=>'btn btn-primary'],
	'htmlOptions'=>[
		'rowTag'=>'div',
		'rowOptions'=>['class'=>'row'],
		'attributeOptions'=>[
			'password'=>['input'=>['class'=>'form-control w50']],
			'repassword'=>['input'=>['class'=>'form-control w50']],
		],
	]
]); ?>
</div>