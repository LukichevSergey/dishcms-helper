<?php
/** @var \accounts\controllers\AuthController $this */
/** @var \crud\models\ar\accounts\models\Account $account */

?>
<h1><?= $this->pageTitle; ?></h1>

<?php $this->widget('\common\widgets\form\ActiveForm', [
    'id'=>'accounts__restore-form',
    'model'=>$account,
    'attributes'=>['phone'],
    'types'=>[
        'phone'=>'phone'
    ],
    'tag'=>false,
    'errorSummary'=>false,
    'submitLabel'=>'Продолжить',
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
        ]
    ]
]); ?>