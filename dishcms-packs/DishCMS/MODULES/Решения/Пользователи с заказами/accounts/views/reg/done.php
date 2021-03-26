<?php
/** @var \accounts\controllers\RegController $this */
use accounts\components\helpers\HAccount;

?>
<h1><?= $this->pageTitle; ?></h1>

<?= HAccount::settings()->reg_done_text; ?>

<br/>
<p>Войти в <?= \CHtml::link('личный кабинет', '/accounts/account/index'); ?> или перейти на <a href="/">главную</a>.</p>