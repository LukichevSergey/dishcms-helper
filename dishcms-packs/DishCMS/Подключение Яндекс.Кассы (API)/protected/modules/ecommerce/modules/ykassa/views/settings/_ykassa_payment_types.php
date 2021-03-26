<?php
use common\components\helpers\HArray as A;

$this->widget('\common\widgets\form\CheckboxListField', A::m(compact('form', 'model'), [
    'attribute'=>'payment_type',
    'data'=>[
        'MC'=>'Баланс телефона',
        'AC'=>'Банковские карты',
        'AB'=>'Альфа-банк (Интернет-банкинг)',
        'EP'=>'Система "Рассчет" (ЕРИП) (Интернет-банкинг)',
        'MA'=>'MasterPass (Интернет-банкинг)',
        'PB'=>'Промсвязьбанк (Интернет-банкинг)',
        'SB'=>'Сбербанк (Интернет-банкинг)',
        'KV'=>'КупиВкредит (Кредитованиe)',
        'QW'=>'QIWI (Электронные деньги)',
        'WM'=>'WebMoney (Электронные деньги)',
        'PC'=>'Яндекс Деньги (Электронные деньги)',
    ],    
]));
?>
       