<?php
use common\components\helpers\HArray as A;

echo A::rget($result, 'errors.error.0.text');
/*
foreach(A::get(A::get($result, 'errors', []), 'error', []) as $error): 
    ?>Код ошибки: <?=$error['code']?><br/><?
    ?>Текст ошибки: <?=$error['text']?><br/><?    
endforeach;
*/
