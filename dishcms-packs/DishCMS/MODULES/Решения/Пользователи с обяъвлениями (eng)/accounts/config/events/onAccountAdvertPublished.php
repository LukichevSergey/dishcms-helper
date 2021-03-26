<?php
/**
 * @event onAccountAdvertPublished
 * Вызывается после публикации объявления администратором
 *
 * @param \common\components\events\Event $event объект события
 *
 * Параметры события:
 * account: \crud\models\ar\accounts\models\Account
 * advert: \crud\models\ar\accounts\models\Advert
 *
 */
use accounts\components\helpers\HAccount;
use accounts\components\helpers\HAccountEmail;
use crud\models\ar\accounts\models\AdvertEmail;

return function(&$event) {
    if($account=$event->getParam('account')) {
        if($advert=$event->getParam('advert')) {
            if($account->email) {
                $sended=HAccountEmail::sendMail($account->email, 'advert_published', compact('account', 'advert'));
                
                AdvertEmail::add($advert->id, 'advert_published', compact('advert', 'account'), $sended, $account->email);
            }
        }
    }
};