<?php
/**
 * @event onAccountRegSuccessed
 * Вызывается после успешной регистрации пользователя
 *
 * @param \common\components\events\Event $event объект события
 *
 * Параметры события:
 * account: \crud\models\ar\accounts\models\Account
 *
 */
use accounts\components\helpers\HAccount;
use accounts\components\helpers\HAccountEmail;

return function(&$event) {
    if($account=$event->getParam('account')) {
        if($account->email) {
            if(HAccount::settings()->isRegConfirmMode()) {
                HAccountEmail::sendMail($account->email, 'reg_confirm', compact('account'));              
            }
            else {
                HAccountEmail::sendMail($account->email, (HAccount::isWholesaleBuyer($account) ? 'reg_successed_wholesale' : 'reg_successed'), compact('account'));
            }        
        }
        if(\D::cms('email') && filter_var(\D::cms('email'), FILTER_VALIDATE_EMAIL)) {
            HAccountEmail::sendMail(\D::cms('email'), 'admin_reg_successed', compact('account'));
        }
    }
};