<?php
/** @var \accounts\controllers\RegController $this */
/** @var \crud\models\ar\accounts\models\Account $account */
?>
<div class="signup account account-page">
	<div class="account-welcome">Поздравляем, <?= $account->getFullName(); ?></div>
	<br/>
	<center><p>Доступ в личный кабинет успешно активирован.</p></center>

    <br/>
    <p><?= \CHtml::link('Войти в личный кабинет', '/signin', ['class'=>'account-link']); ?></p>
    <p><a href="/" class="account-link">Перейти на главную страницу</a></p>
</div>