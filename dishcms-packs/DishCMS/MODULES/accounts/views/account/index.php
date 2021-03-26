<?php
/** @var \accounts\controllers\AccountController $this */
/** @var \crud\models\ar\accounts\models\Account $profile */
use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use accounts\components\helpers\HAccount;

Y::module('accounts')->publishJs('controllers/account/scripts.js');
Y::module('accounts')->publishLess('controllers/account/styles.less');
?>
<h1><?= $this->pageTitle; ?></h1>

<?php if(Y::hasFlash(HAccount::FLASH_SUCCESS)): ?>
	<div class="private__flash-success"><?= Y::getFlash(HAccount::FLASH_SUCCESS); ?></div>
<?php elseif(Y::hasFlash(HAccount::FLASH_FAIL)): ?>
	<div class="private__flash-fail"><?= Y::getFlash(HAccount::FLASH_FAIL); ?></div>
<?php endif; ?>

<div class="private__tabs tabs">
	<ul class="private__tabs-links tabs__links">
		<li class="private__tabs-link-wrap tabs__link-wrap tabs__link-wrap_active">
			<a href="javascript:;" class="private__tabs-link title title_cl_gray tabs__link" data-tabs="1">Профиль</a>
		</li>
		<li class="private__tabs-link-wrap tabs__link-wrap">
			<a href="javascript:;" class="private__tabs-link title title_cl_gray tabs__link" data-tabs="2">Подписки</a>
		</li>
		<li class="private__tabs-link-wrap tabs__link-wrap">
			<a href="javascript:;" class="private__tabs-link title title_cl_gray tabs__link" data-tabs="3">Скидка</a>
		</li>
	</ul>
	<div class="private__tabs-items tabs__items">
		<div class="private__tabs-item tabs__item tabs__item_visible" data-tabs="1">
		<?php $this->widget('\common\widgets\form\ActiveForm', [
            'id'=>'accounts__profile-form',
            'model'=>$profile,
            'attributes'=>['name', 'phone', 'email', 'password', 'repassword', 'lastpassword'],
            'types'=>[
                'phone'=>'phone', 
                'password'=>'passwordField', 
                'repassword'=>'passwordField',
                'lastpassword'=>'passwordField',
            ],
            'tag'=>false,
            'errorSummary'=>false,
		    'submitLabel'=>function() {
                echo \CHtml::submitButton('Сохранить изменения', ['class'=>'private-profile__submit btn']);
                echo \CHtml::resetButton('Отмена', ['class'=>'private-profile__reset btn btn_tp_reset']);
		    },
            'formOptions'=>[
                'clientOptions'=>['hideErrorMessage'=>false]
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
                        'input'=>['class'=>'authoriz__input input js-profile-password'],
                    ],
                    'repassword'=>[
                        'label'=>['class'=>'authoriz__label text text_sz_sm'],
                        'input'=>['class'=>'authoriz__input input'],
                    ],
                    'lastpassword'=>[
                        'label'=>['class'=>'authoriz__label text text_sz_sm'],
                        'input'=>['class'=>'authoriz__input input'],
                    ],
                ]
            ]
        ]); ?>
        </div>
		<div class="private__tabs-item tabs__item" data-tabs="2">
			<form class="private-subscribe private__profile">
				<label for ="private-subscribe__input-1" class="private-subscribe__label text text_sz_sm">E-mail рассылка</label>
				<input type="text" id="private-subscribe__input-1" class="private-subscribe__input input" value="aaa@domain.com" disabled>
				<a href="#!" class="private-subscribe__link">Подписаться</a>
				<label for ="private-subscribe__input-2" class="private-subscribe__label text text_sz_sm">SMS рассылка</label>
				<input type="text" id="private-subscribe__input-2" class="private-subscribe__input input" value="+7 989 989-98-89" disabled>
				<a href="#!" class="private-subscribe__link">Отписаться</a>
			</form>
		</div>
		<div class="private__tabs-item tabs__item" data-tabs="3">
			<form class="private-sale private__profile">
				<label for ="private-sale__input-1" class="private-sale__label text text_sz_sm">Номер карты</label>
				<input type="text" id="private-sale__input-1" class="private-sale__input input" placeholder="372 65 6456">
				<button type="submit" class="private-profile__submit btn">Сохранить изменения</button>
				<button type="reset" class="private-profile__reset btn btn_tp_reset">Отмена</button>
				<b class="private-sale__sale title">Ваша скидка 0%</b>
			</form>
		</div>
	</div>
</div>