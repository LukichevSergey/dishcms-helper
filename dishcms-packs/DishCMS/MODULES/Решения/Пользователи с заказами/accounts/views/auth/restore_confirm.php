<?php
/** @var \accounts\controllers\AuthController $this */
/** @var \crud\models\ar\accounts\models\Account $account */

?>
<h1><?= $this->pageTitle; ?></h1>

<p>На указанную Вами почту <b><?= $account->email; ?></b> выслано письмо с ссылкой восстановления доступа в личный кабинет.</p>

<br/>
<p>Вернуться на <a href="/">главную страницу</a></p>