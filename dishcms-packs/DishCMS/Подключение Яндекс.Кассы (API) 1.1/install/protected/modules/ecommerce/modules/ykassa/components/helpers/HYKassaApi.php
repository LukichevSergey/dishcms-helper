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
            if(HYKassa::isApiMode()) {
                $shopId=HYKassa::settings()->api_shop_id;
                $secretKey=HYKassa::settings()->api_secret_key;
            }
            elseif(HYKassa::isApiTestMode()) {
                $shopId=HYKassa::settings()->api_test_shop_id;
                $secretKey=HYKassa::settings()->api_test_secret_key;
            }

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
     * @return \YandexCheckout\Request\Payments\CreatePaymentResponse|false
     */
    public static function payment($configId, $params=[])
    {
        if($config=ApiConfig::load($configId)) {
            $paymentParams=$config->get('payment', $params);
            $receiptParams=$config->get('receipt', $params);
            $historyParams=$config->get('history', $params);            

            try {
                if(!empty($historyParams)) {
                    if($history=HYKassaHistory::create($historyParams, 'Новый платеж', $configId)) {
                        if(A::rget($paymentParams, 'confirmation.type') === 'redirect') {
                            $paymentParams['confirmation']['return_url']=Y::createAbsoluteUrl('/payment/confirm/' . $history->uuid);
                        }                        

                        if($payment=static::createPayment($paymentParams, null, $receiptParams)) {
                            $history->payment_id=$payment->getId();
                            if($paymentMethod=$payment->getPaymentMethod()) {
                                $history->payment_type=$paymentMethod->getType();
                            }
                            $history->status=$payment->getStatus();                        
                            $history->amount=$payment->getAmount()->getValue();
                            $history->save();

                            $config->get('on_after_create_payment', A::m($params, ['payment'=>$payment]));
                            
                            if($confirmation=$payment->getConfirmation()) {
                                if($confirmationUrl=$confirmation->getConfirmationUrl()) {
                                    HYKassaPayment::setConfirmationUrl($payment);
                                }
                            }
                            
                            HYKassa::log(['HYKassaApi::payment|$payment', $history]);
                            
                            return $payment;
                        }
                    }
                    
                    HYKassa::log(['HYKassaApi::payment|$history', $history]);
                }
                
                HYKassa::log(['HYKassaApi::payment|$historyParams', $historyParams]);
            }
            catch(\Exception $e) {
            	HYKassa::log(['HYKassaApi::payment|$e', $e->getMessage()]);
            	
                return false;
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

        // @todo

        /*
        if($receiptParams=static::getReceiptParams($config, $params)) {
            $result=static::createReceipt($receiptParams);
        }
        */
        
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
                        'return_url' => Y::createAbsoluteUrl('/')
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
        // @todo
    }
}
