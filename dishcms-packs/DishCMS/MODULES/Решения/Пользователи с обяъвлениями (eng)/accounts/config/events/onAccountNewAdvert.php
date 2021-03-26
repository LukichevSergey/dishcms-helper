<?php
/**
 * @event onAccountAdvertEdit
 * Вызывается после успешного редактирования объявления
 *
 * @param \common\components\events\Event $event объект события
 *
 * Параметры события:
 * advert: \crud\models\ar\accounts\models\Advert
 *
 */
use accounts\components\helpers\HAccount;
use accounts\components\helpers\HAccountEmail;
use crud\models\ar\accounts\models\AdvertEmail;

return function(&$event) {
    if($advert=$event->getParam('advert')) {
        if(\D::cms('email') && filter_var(\D::cms('email'), FILTER_VALIDATE_EMAIL)) {
            $account=HAccount::account();
            
            $sended=HAccountEmail::sendMail(\D::cms('email'), 'admin_new_advert', compact('advert', 'account'));
            
            AdvertEmail::add($advert->id, 'admin_new_advert', compact('advert', 'account'), $sended, \D::cms('email'));
        }
    }
};