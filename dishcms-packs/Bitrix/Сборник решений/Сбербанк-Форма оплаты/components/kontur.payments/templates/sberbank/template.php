<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?php if($arResult['PAYMENT_STATUS'] === true): ?>
Ваш платеж успешно завершен.<br/>
Перейти на <a href="/">главную страницу</a> 
<?php elseif($arResult['PAYMENT_STATUS'] === false): ?>
Не удалось завершить платеж.<br/>
Перейти на <a href="/">главную страницу</a>
<?php else: ?>
<div class="pay-block">
    <div class="pay-block__h">Форма оплаты</div>
    <div class="pay-block__in">
        <div class="pay-block__position js-pbi-form">
            <div class="pay-block__inputs pbi">
                <div class="pbi__item">
                    <input type="text" class="pbi__input " id="pbi-fio" name="pbi-fio" value="" placeholder="ФИО пациента">
                </div>
                <div class="pbi__item">
                    <input type="text" class="pbi__input " id="pbi-sum" name="pbi-sum" value="" placeholder="Сумма оплаты (руб.)">
                </div>
                <div class="pbi__item">
                    <input type="text" class="pbi__input " id="pbi-phone" name="pbi-phone" value="" placeholder="Телефон">
                </div>
                <div class="pbi__item">
                    <input type="text" class="pbi__input " id="pbi-mail" name="pbi-mail" value="" placeholder="Email">
                </div>
            </div>
            <div class="pay-block__btn">
                <input class='pbb' type="submit" value=""/>
            </div>
            <div class="pay-block__privacy">
                Все данные конфиденциальны и служат лишь для связи с админитратором
            </div>
        </div>
    </div>
</div>
<script src="https://3dsec.sberbank.ru/demopayment/docsite/assets/js/ipay.js"></script>
<script>;window.konturSbiForm.init(<?php echo CUtil::PhpToJSObject([
    "JS_FORM"=>"js-pbi-form",
    "AJAX_URL"=>"{$templateFolder}/ajax.php",
    "FIELD_NAME"=>"pbi-fio",
    "FIELD_AMOUNT"=>"pbi-sum",
    "FIELD_PHONE"=>"pbi-phone",
    "FIELD_EMAIL"=>"pbi-mail",
    "PARAMS"=>$component::encrypt($arParams)
]); ?>);</script>
<?php endif; ?>