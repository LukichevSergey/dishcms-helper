<?php
namespace ykassa\components\helpers;

use common\components\helpers\HArray as A;
use common\components\helpers\HYii as Y;
use common\components\helpers\HRequest as R;
use common\components\helpers\HFile;
use ykassa\components\ApiConfig;
use YandexCheckout\Client;

/**
 * https://kassa.yandex.ru/developers/using-api/testing
 * https://kassa.yandex.ru/developers/api#payment
 * https://kassa.yandex.ru/developers/54fz/basics#receipt-after-payment
 */
class HYKassaApi
{
    private static $client=null;

    public static function getClient()
    {
        Y::module('ykassa');

        if(static::$client === null) {
            if(\UserHelper::isAdminArea() && (\UserHelper::isManager() || \UserHelper::isAdmin())) {
                $serverName=\crud\models\ar\Region::getCurrentRegion()->domain;
            }
            else {
                $serverName=$_SERVER['SERVER_NAME'];
            }
            
            /*
		    if($serverName == 'xn--e1aner7ci.xn--e1ahbibmpp.xn--p1ai') {
			    $shopId=685843;
			    $secretKey='live_z5pF9pkoycmlyJ7sUAs7iwER4x5N3wBAhGmV1-PeptY';
            }
            elseif($serverName == 'xn--e1ahbibmpp.xn--p1ai') {
		        $shopId=515961;
		        $secretKey='live_o7U-Gxd8yvp7SaUDqXb0HjbjXnWbTD-NF0tF6uqLqvE';
            }
            /**/

            if(HYKassa::isApiMode()) {
                $shopId=HYKassa::settings()->api_shop_id;
                $secretKey=HYKassa::settings()->api_secret_key;
            }
            elseif(HYKassa::isApiTestMode()) {
                $shopId=HYKassa::settings()->api_test_shop_id;
                $secretKey=HYKassa::settings()->api_test_secret_key;
            }
            
            // file_put_contents(dirname(__FILE__).'/log.log', var_export([date('d.m.Y H:i:s'), $shopId, $secretKey, $serverName, $_SERVER['SERVER_NAME']], true), FILE_APPEND);

            if(!empty($shopId) && !empty($secretKey)) {
                static::$client = new Client();
                static::$client->setAuth($shopId, $secretKey);
            }
        }

        return static::$client;
    }

    public static function setClient($client=null)
    {
        static::$client=null;
        
        if($client instanceof Client) {
            static::$client=$client;
        }
    }

    /**
     * Получить ключ идемпотентности
     * @return string
     */
    public static function getIdempotenceKey()
    {
        return uniqid('', true);
    }

    /**
     * Проверить статус по параметрам
     */
    public static function checkPaymentStatusByParams($params)
    {
        if(!empty($params)) {
            if($lastHistory=HYKassaHistory::getByAttributes($params)) {
                return static::checkPaymentStatus($lastHistory->payment_id);
            }
        }

        return false;
    }

    /**
     * Проверяет статус платежа
     * @return bool возвращает true если статус изменился
     */
    public static function checkPaymentStatus($paymentId)
    {
	try {
        if($lastHistory=HYKassaHistory::getByPaymentId($paymentId)) {
            if(!in_array($lastHistory->status, [HYKassa::API_STATUS_SUCCEEDED, HYKassa::API_STATUS_CANCELED])) {
                if($client=static::getClient()) {
                    if($paymentInfo=$client->getPaymentInfo($paymentId)) {
                        if($paymentInfo->getStatus() != $lastHistory->status) {
                            $params=[
                                'int_param_1' => $lastHistory->int_param_1,
                                'int_param_2' => $lastHistory->int_param_2,
                                'int_param_3' => $lastHistory->int_param_3,
                                'string_param_1' => $lastHistory->string_param_1,
                                'string_param_2' => $lastHistory->string_param_2,
                                'string_param_3' => $lastHistory->string_param_3
                            ];

                            HYKassaHistory::addByPayment($paymentInfo, $params, 'Обновление статуса', $lastHistory->configuration_id);
                            
                            if($lastHistory->configuration_id) {
                                if($config=ApiConfig::load($lastHistory->configuration_id)) {
                                    $config->get('on_change_status', A::m($params, ['status'=>$paymentInfo->getStatus()]));
                                }
                            }

                            return true;
                        }
                    }
                }
            }
        }
	}
	catch(\Exception $e) { }

        return false;
    }

    /**
     * Проведение платежа (API)
     * @param string $id идентификатор конфигурации Яндекс.Кассы
     * @param [] $params дополнительные параметры для callable функции получения 
     * данных для запроса регистрации платежа.
     */
    public static function payment($id, $params=[])
    {
        if($config=ApiConfig::load($id)) {
            $paymentParams=$config->get('payment', $params);
            $receiptParams=$config->get('receipt', $params);
            $historyParams=$config->get('history', $params);            

            try {
                $payment=static::createPayment($paymentParams, null, $receiptParams);
            }
            catch(\Exception $e) {
                return false;
            }

            if($payment) {
                if(!empty($historyParams)) {
                    HYKassaHistory::addByPayment($payment, $historyParams, 'Новый платеж', $id);
                }
    
                if($confirmation=$payment->getConfirmation()) {
                    if($confirmationUrl=$confirmation->getConfirmationUrl()) {
                        R::redirect($confirmationUrl);
                    }
                }

                return $payment;
            }
        }

        return false;
    }

    /**
     * Создание чека (API)
     * @param string|callable $config имя конфигурации в application.config.ykassa
     * @param [] $params дополнительные параметры для callable функции получения 
     * данных для запроса регистрации чека.
     */
    public static function receipt($config, $params=[])
    {
        $result=false;

        if($receiptParams=static::getReceiptParams($config, $params)) {
            $result=static::createReceipt($receiptParams);
        }
        
        return $result;
    }

    /**
     * Создание платежа
     * @link https://kassa.yandex.ru/developers/api#payment
     * @param [] $params параметры для создания платежа
     * @return []
     */
    public static function createPayment($params, $idempotenceKey=null, $receiptParams=null) 
    {
        if(!empty($params)) {
            if($idempotenceKey === null) {
                $idempotenceKey = static::getIdempotenceKey();
            }

            if($client=static::getClient()) {
                if(!empty($receiptParams)) {
                    $params['receipt']=$receiptParams;
                }

                if(empty($params['confirmation'])) {
                    $params['confirmation']=[
                        'type' => 'redirect',
                        'return_url' => Y::createAbsoluteUrl('/payment')
                    ];
                }

		// сразу после оплаты платеж успешно завершится и перейдет в статус succeeded
		$params['capture']=true;

                return $client->createPayment($params, $idempotenceKey);
            }
        }

        return false;
    }

    /**
     * Создание чека
     * @link https://kassa.yandex.ru/developers/api#receipt
     * @var [] $params параметры для создания чека.
     * @return []
     */
    public static function createReceipt($params)
    {

    }
}
