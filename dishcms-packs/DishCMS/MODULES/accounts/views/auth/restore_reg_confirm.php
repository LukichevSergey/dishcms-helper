<?php
/** @var \accounts\controllers\AuthController $this */
/** @var \crud\models\ar\accounts\models\Account $account */
/** @var string $code */

?>
<h1><?= $this->pageTitle; ?></h1>

<p>Ваш доступ еще не был активирован.</p>
<p><?= \CHtml::link('Отправить дополнительное письмо', ['/accounts/reg/confirm', 'c'=>$code], ['class'=>'authoriz__submit btn btn_wd_lg']); ?></p>
<br/>
<p>Вернуться на <a href="/">главную страницу</a></p>