<?php
/** @var \settings\modules\admin\controllers\DefaultController $this */
/** @var \settings\components\base\SettingsModel $model */
use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HHtml;

$tbtn=Y::ct('CommonModule.btn', 'common');
?>
<?= HHtml::onReady('js-settings-tabs'); ?>
<div class="form js-settings-tabs" style="display: none">
<? 
    $form=$this->beginWidget('\CActiveForm', [
        'id'=>'ykassa-settings-form',
        'enableClientValidation'=>true,
        'clientOptions'=>[
            'validateOnSubmit'=>true,
            'validateOnChange'=>false
        ],
        // 'htmlOptions'=>['enctype'=>'multipart/form-data'],
    ]); 
    
    echo $form->errorSummary($model); 
    
    $tabs=[
        'Основые'=>['content'=>$this->renderPartial('ykassa.views.settings._ykassa_form_main', compact('model', 'form'), true), 'id'=>'tab-main'],
        'Подключение'=>['content'=>$this->renderPartial('ykassa.views.settings._ykassa_form_connect', compact('model', 'form'), true), 'id'=>'tab-connect'],
        'Страница оплаты'=>['content'=>$this->renderPartial('ykassa.views.settings._ykassa_form_page_payment', compact('model', 'form'), true), 'id'=>'tab-page-payment'],
        'Страница успешной оплаты'=>['content'=>$this->renderPartial('ykassa.views.settings._ykassa_form_page_success', compact('model', 'form'), true), 'id'=>'tab-page-success'],
        'Страница неуспешной оплаты'=>['content'=>$this->renderPartial('ykassa.views.settings._ykassa_form_page_fail', compact('model', 'form'), true), 'id'=>'tab-page-fail'],
        'Дополнительно'=>['content'=>$this->renderPartial('ykassa.views.settings._ykassa_form_extra', compact('model', 'form'), true), 'id'=>'tab-extra']
    ];
    
    if(D::isDevMode()) {
        $tabs['Системные']=['content'=>$this->renderPartial('ykassa.views.settings._ykassa_form_system', compact('model', 'form'), true), 'id'=>'tab-system'];
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
<style>ul.ui-tabs-nav.ui-widget-header{font-size:<?=D::isDevMode()?11:13?>px !important;}</style>
