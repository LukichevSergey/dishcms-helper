<?php
/** @var \ykassa\controllers\PaymentController $this */
/** @var \crud\models\ar\ykassa\models\History $payment */

use ykassa\components\helpers\HYKassa;

?>
<h1><?= HYKassa::settings()->page_success_title; ?></h1>

<div class="payment__text">
    <?= HYKassa::settings()->page_success_text; ?>
</div>