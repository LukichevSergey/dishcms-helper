<?php
/** @var \accounts\controllers\RegController $this */
/** @var \crud\models\ar\accounts\models\Account $account */

?>
<h1><?= $this->pageTitle; ?></h1>

<?php $this->widget('\common\widgets\form\ActiveForm', [
    'id'=>'accounts__reg-form',
    'model'=>$account,
    'attributes'=>['name', 'phone', 'email', 'password', 'repassword'],
    'types'=>[
        'phone'=>'phone', 
        'password'=>'passwordField', 
        'repassword'=>'passwordField'
    ],
    'tag'=>false,
    'errorSummary'=>false,
    'submitOptions'=>['class'=>'authoriz__submit btn btn_wd_md'],
    'formOptions'=>[
        'clientOptions'=>['hideErrorMessage'=>true]
    ],
    'htmlOptions'=>[
        'class'=>'authoriz authoriz__profile',
        'rowTag'=>'div',
        'rowOptions'=>['class'=>'authoriz__row'],
        'attributeOptions'=>[
            'name'=>[
                'label'=>['class'=>'authoriz__label text text_sz_sm'],
                'input'=>['class'=>'authoriz__input input', 'placeholder'=>'Иванов Иван Иванович'],
            ],
            'phone'=>[
                'label'=>['class'=>'authoriz__label text text_sz_sm'],
                'input'=>['class'=>'authoriz__input input', 'placeholder'=>'+7 ( ___ ) ___ - __ - __'],
            ],
            'email'=>[
                'label'=>['class'=>'authoriz__label text text_sz_sm'],
                'input'=>['class'=>'authoriz__input input', 'placeholder'=>'aaa@domain.com'],
            ],
            'password'=>[
                'label'=>['class'=>'authoriz__label text text_sz_sm'],
                'input'=>['class'=>'authoriz__input input'],
            ],
            'repassword'=>[
                'label'=>['class'=>'authoriz__label text text_sz_sm'],
                'input'=>['class'=>'authoriz__input input'],
            ],
            'privacy'=>[
                'label'=>['class'=>'authoriz__checkbox-label label'],
                'input'=>['class'=>'authoriz__checkbox visually-hidden'],
            ],
        ]
    ]
]); ?>