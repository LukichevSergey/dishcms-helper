<?php
/** @var \accounts\controllers\AuthController $this */
/** @var \crud\models\ar\accounts\models\Account $account */

?>
<h1><?= $this->pageTitle; ?></h1>

<?php $this->widget('\common\widgets\form\ActiveForm', [
    'id'=>'accounts__auth-form',
    'model'=>$account,
    'attributes'=>['phone', 'password', 'remember_me'],
    'types'=>[
        'phone'=>'phone',
        'password'=>'passwordField', 
        'remember_me'=>'checkBox'
    ],
    'tag'=>false,
    'errorSummary'=>false,
    'submitLabel'=>'Войти',
    'submitOptions'=>['class'=>'authoriz__submit btn btn_wd_lg'],
    'formOptions'=>[
        'clientOptions'=>['hideErrorMessage'=>false]
    ],
    'htmlOptions'=>[
        'class'=>'authoriz authoriz__profile',
        'rowTag'=>'div',
        'rowOptions'=>['class'=>'authoriz__row'],
        'attributeOptions'=>[
            'phone'=>[
                'label'=>['class'=>'authoriz__label text text_sz_sm'],
                'input'=>['class'=>'authoriz__input input', 'placeholder'=>'+7 ( ___ ) ___ - __ - __'],
            ],
            'password'=>[
                'label'=>['class'=>'authoriz__label text text_sz_sm'],
                'input'=>['class'=>'authoriz__input input'],
            ],
            'remember_me'=>[
                'label'=>['class'=>'authoriz__checkbox-label label'],
                'input'=>['class'=>'authoriz__checkbox visually-hidden'],
            ],
        ]
    ]
]); ?>
   
<br/>
<p><?= \CHtml::link('Забыли свой пароль?', '/accounts/auth/restore'); ?></p>
<p><?= \CHtml::link('Регистрация', '/accounts/reg/index'); ?></p>