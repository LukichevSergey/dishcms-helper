<?php
/** @var \accounts\controllers\RegController $this */
/** @var \crud\models\ar\accounts\models\Account $account */

?>
<div class="signup account account-page">
	<div class="account-welcome">Произошла ошибка</div>
	<br/>
	<center>
		<p>Не удалось отправить на указанную Вами почту <b style="white-space:nowrap"><?= $account->email; ?></b> письмо с ссылкой активации Вашего доступа в личный кабинет.</p>
		<p>Просим обратиться в службу тех.поддержки нашего сайта для дальнейших инструкций.</p>	
	</center>

    <br/>
    <p><a href="/" class="account-link">Перейти на главную страницу</a></p>
</div>