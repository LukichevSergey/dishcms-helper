<?php
/** @var \crud\models\ar\accounts\models\Account $account */
use common\components\helpers\HYii as Y;

$activationUrl=Y::createAbsoluteUrl('/accounts/reg/activation', ['code'=>$account->confirm_code], Y::param('httpschema', 'http'));
?>
<h1>Уважаемый, <?= $account->getUserName(); ?></h1>

<p>Для активации Вашего доступа к личному кабинету на сайте <strong><?= \Yii::app()->name; ?></strong> перейдите пожалуйста по ссылке: 
<br/><?= \CHtml::link($activationUrl, $activationUrl); ?></p> 