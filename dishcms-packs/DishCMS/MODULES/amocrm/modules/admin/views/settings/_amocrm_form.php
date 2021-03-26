<?php
/** @var \settings\modules\admin\controllers\DefaultController $this */
/** @var \settings\components\base\SettingsModel $model */
use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;

$tbtn=Y::ct('CommonModule.btn', 'common');

$accessToken=\amocrm\components\helpers\HAmoCRM::getAccessToken();
?>
<?php if(!$accessToken): ?>
	<div class="alert alert-info">После успешной авторизации будет доступна вкладка "Дополнительные поля" для настройки соотвествия дополнительных полей</div>
<?php endif; ?>

<div class="form"><? 
	$form=$this->beginWidget('\CActiveForm', [
		'id'=>'settings-form',
		'enableClientValidation'=>true,
		'clientOptions'=>[
			'validateOnSubmit'=>true,
			'validateOnChange'=>false
		],
		// 'htmlOptions'=>['enctype'=>'multipart/form-data'],
	]); 
	
	echo $form->errorSummary($model); 
	
	$tabs=[
	    'Основые'=>['content'=>$this->renderPartial('amocrm.modules.admin.views.settings._amocrm_form_main', compact('model', 'form'), true), 'id'=>'tab-main'],
	];
	
	if($accessToken) {
	    $tabs['Дополнительные поля']=['content'=>$this->renderPartial('amocrm.modules.admin.views.settings._amocrm_form_fields', compact('model', 'form'), true), 'id'=>'tab-fields'];
	}
	
	$this->widget('zii.widgets.jui.CJuiTabs', [
		'tabs'=>$tabs,
		'options'=>[]
	]);
	?>
	<div class="row buttons">
      <div class="left">
        <?= CHtml::submitButton($tbtn('save'), ['class'=>'btn btn-primary']); ?>
      </div>
      <div class="clr"></div>
    </div>
	<? $this->endWidget(); ?>
</div>