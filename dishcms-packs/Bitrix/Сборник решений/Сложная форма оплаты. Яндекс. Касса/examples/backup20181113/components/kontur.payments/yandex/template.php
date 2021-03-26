<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?php if($arResult['PAYMENT_STATUS'] === true): ?>
<div id="payment-success">
    <div class="payment-success">
        <div class="payment-success__text">
            <p>Ваше Заявление об отказе от взаимодействия <br>отправлено на электронную почту: sidorovich@stop-dolg.ru</p>
        </div>
        <div class="payment-success__download">
            <a href="javascript:;">Скачать Ваше Заявление об отказе от взаимодействия</a>
        </div>
    </div>
</div> 
<?php elseif($arResult['PAYMENT_STATUS'] === false): ?>
<div id="payment-fail">
    <div class="payment-fail">
        <div class="payment-fail__text">
            <p>Произошла ошибка при проведении Вашего платежа.<br/>
            Если деньги были списаны и не были возвращены обратитесь, пожалуйста, в техническую службу Вашего банка.</p>
        </div>
    </div>
</div> 
<?php else: ?>
<div id="content">
    <div id="payment-success" class="js-payment-success" style="display:none">
        <div class="payment-success">
            <div class="payment-success__text">
                <p>Ваше Заявление об отказе от взаимодействия <br>отправлено на электронную почту: sidorovich@stop-dolg.ru</p>
            </div>
            <div class="payment-success__download">
                <a href="javascript:;">Скачать Ваше Заявление об отказе от взаимодействия</a>
            </div>
        </div>
    </div> 
    <div class="form-no" class="js-payment-form-wrapper">
        <div class="form-no__title">
            Заявление об отказе от взаимодействия
        </div>
        <form action="" method="post" class="js-disclaimer-form">
            <div class="form-no-inputs">
                <div class="form-no-row">
                    <div class="form-no-col">
                        <div class="form-no-input">
                            <label for="">Введите промокод</label>
                            <input type="text" name="promocode" placeholder="ПРОМОКОД">
                        </div>
                    </div>
                    <div class="form-no-col">
                        <div class="form-no-price">
                            <div class="form-no-price__label">Цена:</div> <div class="form-no-price__value"><span><?= $arParams['PRICE_DEFAULT']; ?></span> руб.</div>
                        </div>
                    </div>
                </div>

                <div class="form-no-row">
                    <div class="form-no-col">
                        <div class="form-no-input">
                            <input type="text" name="name" placeholder="ФИО">
                        </div>
                    </div>
                    <div class="form-no-col">
                        <div class="form-no-input">
                            <input type="text" name="email" placeholder="Email">
                        </div>
                    </div>
                    <div class="form-no-col">
                        <div class="form-no-input">
                            <input type="text" name="phone" placeholder="Контактный телефон">
                        </div>
                    </div>
                </div>

                <div class="form-no-row">
                    <div class="form-no-col">
                        <div class="form-no-input">
                            <input type="text" name="passport_number" placeholder="Серия номер паспорта">
                        </div>
                    </div>
                    <div class="form-no-col">
                        <div class="form-no-input">
                            <input type="text" name="passport_org" placeholder="Кем выдан паспорт">
                        </div>
                    </div>
                    <div class="form-no-col">
                        <div class="form-no-input">
                            <input type="text" name="passport_date" placeholder="Дата выдачи паспорта">
                        </div>
                    </div>
                    <div class="form-no-col">
                        <div class="form-no-input">
                            <input type="text" name="passport_address" placeholder="Адрес проживания Заявителя">
                        </div>
                    </div>
                </div>

                <div class="form-no-row js-creditor-block">
                	<div class="form-no-col">
                        <div class="form-no-input">
                            <input type="text" name="creditor_name" placeholder="Наименование Кредитора">
                        </div>
                    </div>
                    <div class="form-no-col">
                       <div class="form-no-input">
                           <input type="text" name="creditor_number" placeholder="Номер кредитного договора">
                       </div>
                    </div>
                    <div class="form-no-col">
                        <div class="form-no-input">
                            <input type="text" name="creditor_date" placeholder="Дата кредитного договора">
                        </div>
                    </div>
                    <div class="form-no-col">
                       <div class="form-no-input">
                           <input type="text" name="creditor_address" placeholder="Адрес кредитора">
                       </div>
                   </div>
              	</div>
              	<div class="form-no-row">
                   <div class="form-no-col">
                        <div class="form-no-add">
                            <a href="javascript:;" class="js-add-creditor">+ Добавить Кредитора</a>  (+ <?= $arParams['PRICE_CREDITOR']; ?> р. к цене)
                       </div>
                   </div>
                </div>

                <div class="form-no-row">
                    <div class="form-no-col">
                        <div class="form-no-input">
                            <input type="text" name="date" placeholder="Заявление составлено">
                        </div>
                    </div>
                    <div class="form-no-col form-no-col_100">
                        <div class="form-no-checkbox">
                            <input id="agree" name="agree" type="checkbox" value="1">
                            <label for="agree">Подтверждаю свое согласие с Политикой обработки данных</label>
                        </div>
                    </div>
                </div>

                <div class="form-no-submit">
                    <button type="submit"><span>Оплатить</span></button>
                </div>
            </div>
        </form>
    </div>
</div>
<script>;window.konturSbiForm.init(<?php echo CUtil::PhpToJSObject([
    "JS_FORM"=>"js-disclaimer-form",
    "AJAX_URL"=>"{$templateFolder}/ajax.php",
    "PRICE"=>".form-no-price > .form-no-price__value > span",
    "PRICE_DEFAULT"=>$arParams['PRICE_DEFAULT'],
    "PRICE_PROMOCODE"=>$arParams['PRICE_PROMOCODE'],
    "PRICE_CREDITOR"=>$arParams['PRICE_CREDITOR'],
    "CURRENT_DATE"=>date('d/m/Y'),
    "PARAMS"=>$component::encrypt($arParams)
]); ?>);</script>
<?php endif; ?>