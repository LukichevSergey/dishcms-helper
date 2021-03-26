<?php
/** @var \crud\models\ar\accounts\models\Account $account */
use common\components\helpers\HYii as Y;
use common\components\helpers\HTools;
use settings\components\helpers\HSettings;

$activationUrl=Y::createAbsoluteUrl('/signin', [], Y::param('httpschema', 'http'));
$settings=HSettings::getById('accounts');
?>
<?= $settings->reg_email_before; ?>

<p>
    Ваши регистрационные данные:
    <br/><br/>
    <strong>ваш логин</strong>: <?= HTools::formatPhone($account->phone); ?><br/>
    <strong>ваш пароль</strong>: <?= $account->plain_password; ?><br/>
    <strong>адрес сайта</strong>: <?= \CHtml::link(Y::createAbsoluteUrl('/'), Y::createAbsoluteUrl('/')); ?>
</p>

<?= $settings->reg_email_after; ?>
