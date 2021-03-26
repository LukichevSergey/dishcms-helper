<?php
namespace extend\modules\polls\components\helpers;

use crud\models\ar\extend\modules\polls\models\Result;

class HPoll
{
    /**
     * Проверяет, пройден ли уже пользователем опрос или нет
     * @var integer $pollId идентификатор опроса
     */
    public static function isPassed($pollId)
    {
        return (bool)Result::model()->findByAttributes(['poll_id'=>$pollId, 'user_hash'=>static::getUserHash()]);
    }
    
    public static function getUserHash()
    {
        return crc32(static::getUserIp() . '@@@' . $_SERVER['HTTP_USER_AGENT'] . '@@@' . @$_SERVER['X_FORWARDED_FOR']);
    }
    
    public static function getUserIp()
    {
        return $_SERVER['REMOTE_ADDR'];
    }
}