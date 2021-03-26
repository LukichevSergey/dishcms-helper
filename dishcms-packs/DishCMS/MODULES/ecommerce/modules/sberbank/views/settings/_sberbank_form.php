<?php
/** @var \settings\modules\admin\controllers\DefaultController $this */
/** @var \settings\components\base\SettingsModel $model */
use common\components\helpers\HYii as Y;

$tbtn=Y::ct('CommonModule.btn', 'common');
?>
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
	
	$this->widget('zii.widgets.jui.CJuiTabs', [
		'tabs'=>[
			'Основые'=>['content'=>$this->renderPartial('ecommerce.modules.sberbank.views.settings._sberbank_form_main', compact('model', 'form'), true), 'id'=>'tab-main'],
		],
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