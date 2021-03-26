<?php
/** @var \ecommerce\modules\robokassa\controllers\PaymentController $this */

use ecommerce\modules\robokassa\components\helpers\HRobokassa;
?>
<h1><?=HRobokassa::settings()->title_fail?></h1>

<?= HRobokassa::settings()->text_fail; ?>