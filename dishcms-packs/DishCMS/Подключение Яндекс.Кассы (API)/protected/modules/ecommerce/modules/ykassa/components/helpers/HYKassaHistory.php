<?php
namespace ykassa\components\helpers;

use common\components\helpers\HArray as A;
use crud\models\ar\ykassa\models\History;

/**
 * Класс-помощник для истории платежей
 */
class HYKassaHistory
{
    /**
     * Добавление записи истории платежа.
     * 
     * Для добавления истории необходимо, чтобы был передан
     * либо параметр $paymentId, либо один из параметров $params.
     * 
     * @param \YandexCheckout\Model\Payment $payment статус платежа
     * @param [] $params дополнительные параметры записи истории платежа.
     * Доступны ключи:
     * "int_param_1" - параметр 1 (число);
     * "int_param_2" - параметр 2 (число);
     * "int_param_3" - параметр 3 (число);
     * "string_param_1" - параметр 1 (строка);
     * "string_param_2" - параметр 2 (строка);
     * "string_param_3" - параметр 3 (строка);
     * "comment" переопределит основной комментарий.
     * @param string $comment комментарий записи платежа.
     * @param string $configurationId идентификатор конфигурации
     */
    public static function addByPayment($payment, $params=[], $comment='', $configurationId=null)
    {
        $history=new History;
        $history->configuration_id=(string)$configurationId;
        $history->payment_id=$payment->getId();
        if($paymentMethod=$payment->getPaymentMethod()) {
            $history->payment_type=$paymentMethod->getType();
        }
        $history->status=$payment->getStatus();
        $history->comment=A::get($params, 'comment', $comment);
        $history->amount=$payment->getAmount()->getValue();
        $history->int_param_1=(int)A::get($params, 'int_param_1', 0);
        $history->int_param_2=(int)A::get($params, 'int_param_2', 0);
        $history->int_param_3=(int)A::get($params, 'int_param_3', 0);
        $history->string_param_1=(string)A::get($params, 'string_param_1', '');
        $history->string_param_2=(string)A::get($params, 'string_param_2', '');
        $history->string_param_3=(string)A::get($params, 'string_param_3', '');

        static::log($history);

        return $history->save();
    }

    public static function getByPaymentId($paymentId)
    {
        return History::model()->findByAttributes(['payment_id'=>$paymentId], ['order'=>'`id` DESC']);
    }

    public static function getAllByPaymentId($paymentId)
    {
        return History::model()->findAllByAttributes(['payment_id'=>$paymentId], ['order'=>'`id` DESC']);
    }

    public static function getByAttributes($attributes)
    {
        if(!empty($attributes)) {
            return History::model()->findByAttributes($attributes, ['order'=>'`id` DESC']);
        }

        return null;
    }

    public static function getAllByAttributes($attributes)
    {
        if(!empty($attributes)) {
            return History::model()->findAllByAttributes($attributes, ['order'=>'`id` DESC']);
        }

        return [];
    }

    protected static function log($history)
    {
    	return;

        if(\UserHelper::isAdminArea() && (\UserHelper::isManager() || \UserHelper::isAdmin())) {
            $serverName=\crud\models\ar\Region::getCurrentRegion()->domain;
        }
        else {
            $serverName=$_SERVER['SERVER_NAME'];
        }

        if(HYKassa::isApiMode()) {
            $shopId=HYKassa::settings()->api_shop_id;
            $secretKey=HYKassa::settings()->api_secret_key;
        }
        elseif(HYKassa::isApiTestMode()) {
            $shopId=HYKassa::settings()->api_test_shop_id;
            $secretKey=HYKassa::settings()->api_test_secret_key;
        }

        file_put_contents(dirname(__FILE__).'/log_history.log', var_export([
            date('d.m.Y H:i:s'), 
            $shopId, 
            $secretKey, 
            $serverName,
            $_SERVER['SERVER_NAME'],
            $history->int_param_1,
            $history->amount,
            $history->payment_id
        ], true), FILE_APPEND);
    }
}