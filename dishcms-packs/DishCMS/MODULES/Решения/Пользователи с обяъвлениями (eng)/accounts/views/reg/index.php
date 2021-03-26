<?php
/** @var \accounts\controllers\RegController $this */
/** @var \crud\models\ar\accounts\models\Account $account */
?>
<?php /* ?>
<div class="signup account">
	<div class="account-welcome">Добро пожаловать!</div>
	<?php $this->widget('\common\widgets\form\ActiveForm', [
        'id'=>'accounts__reg-form',
        'model'=>$account,
        'attributes'=>['lastname', 'name', 'midname', 'phone', 'email', 'privacy', 'captcha'],
        'types'=>[
            'lastname'=>function($widget, $form, $attribute) {
                echo \CHtml::openTag('div', ['class'=>'account-form-inner']);
                $widget->renderAttribute($form, $attribute, 'textField');
            },
            'phone'=>'phone',
            'privacy'=>function($widget, $form, $attribute) {
                ?>
                <div class="form-code__agree">
                	<?= $form->checkBox($widget->model, $attribute, ['class'=>'form-code__agree-checkbox']); ?>
                	<?= $form->labelEx($widget->model, $attribute, ['class'=>'form-code__agree-label', 'label'=>D::cms('privacy_policy_text'), 'required'=>false]); ?>
                	<?= $form->error($widget->model, $attribute); ?>
                </div>
                <?php 
            },
            'captcha'=>function($widget, $form, $attribute) {
                ?><div class="form-code__captcha<?php if($widget->model->getError('captcha')) echo ' error'; ?>"><?php 
                \Yii::import('application.extensions.reCaptcha2.SReCaptcha');
                \Yii::app()->controller->widget('SReCaptcha', [
                    'name'=>\CHtml::resolveName($widget->model, $attribute='captcha'), //is requred
                    'siteKey' => \Yii::app()->params['reCaptcha2PublicKey'], //is requred
                    'widgetOptions' => ['class' => 'col-lg-3'],
                    'theme' => SReCaptcha::THEME_LIGHT,
                    'size' => SReCaptcha::SIZE_NORMAL,
                    'jsCallback' => '',
                    'jsExpiredCallback' => 'console.log("reCaptcha is expired!");',
                    'lang'=>'ru'
                ]); 
                ?>
                </div>
                <?php 
            }
        ],
        // 'privacyLabel'=>'Нажимая на кнопку Вы даете свое согласие на обработку персональных данных',
        'tag'=>false,
        'errorSummary'=>true,
        'submitLabel'=>function() {
	        echo \CHtml::tag('div', ['class'=>'account-form-submit'], \CHtml::tag('button', ['class'=>'btn', 'type'=>'submit'], 'Создать аккаунт'));
	        echo \CHtml::closeTag('div');
	    },
        'formOptions'=>[
            'clientOptions'=>['hideErrorMessage'=>true]
        ],
        'htmlOptions'=>[
            'class'=>'account-form',
            'rowTag'=>'div',
            'rowOptions'=>['class'=>'account-form-item'],
            'attributeOptions'=>[
                'phone'=>[
                    'input'=>['placeholder'=>'+7 ( ___ ) ___ - __ - __'],
                ],
                'privacy'=>[
                    'input'=>['style'=>'display:none !important', 'checked'=>'checked'],
                    'label'=>['style'=>'font-size:15px;']
                ]
            ]
        ]
    ]); ?>	
	<?= \CHtml::link('Есть аккаунт? Вернитесь на экран входа', '/signin', ['class'=>'account-link']); ?>
</div>
<? /**/ ?>
