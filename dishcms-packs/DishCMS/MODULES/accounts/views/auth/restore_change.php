<?php
/** @var \accounts\controllers\AuthController $this */
/** @var \crud\models\ar\accounts\models\Account $account */

?>
<h1><?= $this->pageTitle; ?></h1>

<?php $this->widget('\common\widgets\form\ActiveForm', [
    'id'=>'accounts__restore-change-form',
    'model'=>$account,
    'attributes'=>['password', 'repassword'],
    'types'=>[
        'password'=>'passwordField',
        'repassword'=>'passwordField'
    ],
    'tag'=>false,
    'errorSummary'=>false,
    'submitLabel'=>'Сохранить изменения',
    'submitOptions'=>['class'=>'authoriz__submit btn btn_wd_lg'],
    'formOptions'=>[
        'clientOptions'=>['hideErrorMessage'=>false]
    ],
    'htmlOptions'=>[
        'class'=>'authoriz authoriz__profile',
        'rowTag'=>'div',
        'rowOptions'=>['class'=>'authoriz__row'],
        'attributeOptions'=>[
            'password'=>[
                'label'=>['class'=>'authoriz__label text text_sz_sm'],
                'input'=>['class'=>'authoriz__input input'],
            ],
            'repassword'=>[
                'label'=>['class'=>'authoriz__label text text_sz_sm'],
                'input'=>['class'=>'authoriz__input input'],
            ],
        ]
    ]
]); ?>