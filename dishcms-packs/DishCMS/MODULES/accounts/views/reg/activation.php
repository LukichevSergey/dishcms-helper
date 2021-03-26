<?php
/** @var \accounts\controllers\RegController $this */
/** @var \crud\models\ar\accounts\models\Account $account */
?>
<h1>Поздравляем, <?= $account->name; ?></h1>

<p>Доступ в личный кабинет успешно активирован.</p>

<br/>
<p>Войти в <?= \CHtml::link('личный кабинет', '/accounts/account/index'); ?> или перейти на <a href="/">главную</a>.</p>