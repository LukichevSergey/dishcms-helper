<?php
/** @var \crud\models\ar\accounts\models\Account $account */
use common\components\helpers\HYii as Y;

$restoreUrl=Y::createAbsoluteUrl('/accounts/auth/restoreChange', ['code'=>$account->confirm_code], Y::param('httpschema', 'http'));
?>
<h1>Уважаемый, <?= $account->name; ?></h1>

<p>Для восстановления Вашего доступа к личному кабинету на сайте <strong><?= \Yii::app()->name; ?></strong> перейдите пожалуйста по ссылке: 
<br/><?= \CHtml::link($restoreUrl, $restoreUrl); ?></p> 