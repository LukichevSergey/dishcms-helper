<?php
/**
 * @event onAccountAdvertResponse
 * Вызывается при отклике на объявление
 *
 * @param \common\components\events\Event $event объект события
 *
 * Параметры события:
 * advert: \crud\models\ar\accounts\models\Advert
 * account: \crud\models\ar\accounts\models\Account
 *
 */
use accounts\components\helpers\HAccount;
use accounts\components\helpers\HAccountEmail;
use crud\models\ar\accounts\models\AdvertEmail;

return function(&$event) {
    if($advert=$event->getParam('advert')) {
        if(\D::cms('email') && filter_var(\D::cms('email'), FILTER_VALIDATE_EMAIL)) {
            $account=HAccount::account();
            
            $sended=HAccountEmail::sendMail(\D::cms('email'), 'admin_advert_response', compact('advert', 'account'));
            
            AdvertEmail::add($advert->id, 'admin_advert_response', compact('advert', 'account'), $sended, \D::cms('email'));
        }
    }
};