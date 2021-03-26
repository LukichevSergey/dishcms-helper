<?php
/** @var \settings\modules\admin\controllers\DefaultController $this */
/** @var \settings\components\base\SettingsModel $model */

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HHtml;

$viewPrefix='ecommerce.modules.robokassa.modules.admin.views.settings.';
$tbtn=Y::ct('CommonModule.btn', 'common');
?>
<?= HHtml::onReady('js-settings-tabs'); ?>
<div class="form js-settings-tabs" style="display: none">
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
        // 'Основые'=>['content'=>$this->renderPartial($viewPrefix . '_robokassa_form_main', compact('model', 'form'), true), 'id'=>'tab-main'],
        'Подключение'=>['content'=>$this->renderPartial($viewPrefix . '_robokassa_form_connect', compact('model', 'form'), true), 'id'=>'tab-connect'],
        'Страница формы оплаты'=>['content'=>$this->renderPartial($viewPrefix . '_robokassa_form_text_payment_form', compact('model', 'form'), true), 'id'=>'tab-text-payment-form'],
        'Страница успешной оплаты'=>['content'=>$this->renderPartial($viewPrefix . '_robokassa_form_text_success', compact('model', 'form'), true), 'id'=>'tab-text-success'],
        'Страница неуспешной оплаты'=>['content'=>$this->renderPartial($viewPrefix . '_robokassa_form_text_fail', compact('model', 'form'), true), 'id'=>'tab-text-fail'],
    ];
    
    if(D::isDevMode()) {
        $tabs['Дополнительно']=['content'=>$this->renderPartial($viewPrefix . '_robokassa_debug', compact('model', 'form'), true), 'id'=>'tab-debug'];
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
<style>ul.ui-tabs-nav.ui-widget-header{font-size:12px !important;}</style>
</div>