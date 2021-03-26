<?php
namespace ecommerce\modules\robokassa\components\helpers;

use common\components\helpers\HArray as A;
use common\components\helpers\HRequest as R;
use common\components\helpers\HDb;
use common\components\helpers\HHash;
use common\components\helpers\HFile;
use crud\models\ar\robokassa\models\Payment;
use settings\components\helpers\HSettings;

class HRobokassa
{
    /**
     * Ссылка на форму оплаты
     */
    const API_PAYMENT_URL='https://auth.robokassa.ru/Merchant/Index.aspx';

    /**
     * Новая оплата. Данные еще не отправлены в сервис Робокассу
     */
    const STATUS_NEW=1;

    /**
     * операция только инициализирована, деньги от покупателя не получены.
     * От пользователя ещё не поступила оплата по выставленному ему счёту 
     * или платёжная система, через которую пользователь совершает оплату, 
     * ещё не подтвердила факт оплаты.
     */
    const STATUS_INPAY=5;

    /**
     * операция отменена, деньги от покупателя не были получены. 
     * Оплата не была произведена. Покупатель отказался от оплаты 
     * или не совершил платеж, и операция отменилась по истечении времени ожидания. 
     * Либо платёж был совершён после истечения времени ожидания. В случае возникновения 
     * спорных моментов по запросу от продавца или покупателя, операция будет 
     * перепроверена службой поддержки, и в зависимости от результата 
     * может быть переведена в другое состояние.
     */
    const STATUS_CANCEL=10;
    
    /**
     * деньги от покупателя получены, производится зачисление денег на счет магазина. 
     * Операция перешла в состояние зачисления средств на баланс продавца. 
     * В этом статусе платёж может задержаться на некоторое время. Если платёж «висит» в этом 
     * состоянии уже долго (более 20 минут), это значит, что возникла проблема 
     * с зачислением средств продавцу.
     */
    const STATUS_PAIDING=50;

    /**
     * деньги после получения были возвращены покупателю. 
     * Полученные от покупателя средства возвращены на его 
     * счёт (кошелёк), с которого совершалась оплата.
     */
    const STATUS_REJECT=60;

    /**
     * исполнение операции приостановлено. 
     * Внештатная остановка. Произошла внештатная ситуация в процессе 
     * совершения операции (недоступны платежные интерфейсы в системе, 
     * из которой/в которую совершался платёж и т.д.) Или операция была 
     * приостановлена системой безопасности. Операции, находящиеся в этом 
     * состоянии, разбираются нашей службой поддержки в ручном режиме.
     */
    const STATUS_SUSPENDED=80;

    /**
     * операция выполнена, завершена успешно. 
     * Платёж проведён успешно, деньги зачислены на баланс продавца, 
     * уведомление об успешном платеже отправлено продавцу.
     */
    const STATUS_PAID=100;

    /**
     * возникла системная ошибка платежа
     */
    const STATUS_ERROR=400;

    /**
     * платеж прошел успешно, но требуется 
     * дополнительная проверка статуса платежа
     */
    const STATUS_PAID_NOT_CHECKED=700;
    /**
     * платеж прошел с ошибкой, но требуется дополнительная 
     * проверка статуса платежа
     */
    const STATUS_CANCEL_NOT_CHECKED=900;

    /**
     * Путь к лог файлам
     */
    const LOG_PATH='application.runtime.robokassa';

    /**
     * Получение текстовыx наименований статуса платежа
     
     * @return []
     */
    public static function getStatusLabels()
    {
        return [
            self::STATUS_CANCEL_NOT_CHECKED=>'Отменен',
            self::STATUS_PAID_NOT_CHECKED=>'Оплачено',
            self::STATUS_ERROR=>'Ошибка',
            self::STATUS_PAID=>'Оплачено',
            self::STATUS_SUSPENDED=>'Приостановлено',
            self::STATUS_REJECT=>'Возврат',
            self::STATUS_PAIDING=>'Завершается',
            self::STATUS_CANCEL=>'Отменен',
            self::STATUS_INPAY=>'Оплачивается',
            self::STATUS_NEW=>'Новый',
        ];
    }

    /**
     * Получение текстового наименования статуса платежа

     * @param int $status статус платежа     
     * @return string
     */
    public static function getStatusLabel($status)
    {
        return A::get(static::getStatusLabels(), $status, 'неопределен');
    }

    /**
     * Получение HTML тэг статуса платежа
     
     * @return string
     */
     public static function getStatusTag($status)
     {
        static $options=[
            self::STATUS_CANCEL_NOT_CHECKED=>['class'=>'danger'],
            self::STATUS_PAID_NOT_CHECKED=>['class'=>'success'],
            self::STATUS_ERROR=>['class'=>'danger'],
            self::STATUS_PAID=>['class'=>'Оплачено'],
            self::STATUS_SUSPENDED=>['class'=>'warning'],
            self::STATUS_REJECT=>['class'=>'default'],
            self::STATUS_PAIDING=>['class'=>'primary'],
            self::STATUS_CANCEL=>['class'=>'danger'],
            self::STATUS_INPAY=>['class'=>'info'],
            self::STATUS_NEW=>['class'=>'info'],
        ];
        
        $statusOptions=A::get($options, $status, []);
        
        return \CHtml::tag('span', [
             'class' => 'label label-' . A::get($statusOptions, 'class', 'default'), 
             'style' => A::get($statusOptions, 'style', '')
        ], static::getStatusLabel($status));
     }

    /**
     * Проверяет установлен ли модуль Робокассы или нет.
     *
     * @return boolean
     */
    public static function isInstalled()
    {
        return (bool)HDb::getTable('ecommerce_robokassa_payments');
    }

    /**
     * Получить модель настроек
     *
     * @return \ecommerce\modules\robokassa\models\RobokassaSettings
     */
    public static function settings()
    {
        return HSettings::getById('robokassa');        
    }

    public static function isDebugMode()
    {
        return ((int)static::settings()->enable_debug_mode === 1);
    }

    /**
     * Включен тестовый режим
     *
     * @return boolean
     */
    public static function isTestMode()
    {
        return (bool)static::settings()->enable_test_mode;
    }

    /**
     * Получить идентификатор магазина
     * 
     * @return string
     */
    public static function getMerchantLogin()
    {
        return static::isTestMode() ? static::settings()->test_merchant_login : static::settings()->merchant_login;
    }

    /**
     * Получить пароль #1
     * 
     * @return string
     */
    public static function getPassword1()
    {
        return static::isTestMode() ? static::settings()->test_password1 : static::settings()->password1;
    }

    /**
     * Получить пароль #2
     * 
     * @return string
     */
    public static function getPassword2()
    {
        return static::isTestMode() ? static::settings()->test_password2 : static::settings()->password2;
    }

    /**
     * Создание сигнатуры платежа
     *
     * @param Payment $payment объект платежа
     * 
     * @return string
     */
    public static function createSignature($payment)
    {
        $parts=[static::getMerchantLogin(), $payment->getSum(), $payment->getInvoiceId(), static::getPassword1()];
        foreach($payment->getShps() as $k=>$v) {
            $parts[]="Shp_{$k}={$v}";
        }

        return md5(implode(':', $parts));
    }

    /**
     * Создание сигнатуры проверки статуса платежа
     *
     * @param Payment $payment объект платежа
     * 
     * @return string
     */
    public static function createOpSignature($payment)
    {
        $parts=[static::getMerchantLogin(), $payment->getInvoiceId(), static::getPassword2()];
        foreach($payment->getShps() as $k=>$v) {
            // $parts[]="Shp_{$k}={$v}";
        }

        return md5(implode(':', $parts));
    }

    /**
     * Создание сигнатуры проверки платежа
     *
     * @param int $outSum сумма платежа
     * @param int $invoiceId идентификатор платежа
     * @param [] $shps дополнительные параметры платежа
     * 
     * @return string
     */
    public static function createCheckSignatureByRequest()
    {
        $parts=[R::get('OutSum'), R::get('InvId'), static::getPassword2()];
        foreach($_REQUEST as $k=>$v) {
            if(substr($k, 0, 4) == 'Shp_') {
                $parts[]="{$k}={$v}";
            }
        }

        return md5(implode(':', $parts));
    }

    /**
     * Создание нового платежа
     * 
     * @param int $sum сумма платежа
     * @param string $description описание платежа
     * @param array $params дополнительные параметры платежа. 
     * Доступны следующие параметры:
     * "name" ФИО плательщика
     * "phone" телефон плательщика
     * "email" email плательщика
     * "comment" дополнительный комментарий к платежу
     * 
     * @return Payment
     */
    public static function createPayment($sum, $description, $params=[])
    {
        $payment=new Payment;
        $payment->status=self::STATUS_NEW;
        $payment->payment_id=HHash::guid();
        $payment->sum=$sum;
        $payment->description=mb_substr($description, 0, 100);
        $payment->name=A::get($params, 'name', '');
        $payment->phone=A::get($params, 'phone', '');
        $payment->email=A::get($params, 'email', '');
        $payment->comment=A::get($params, 'comment', '');
        
        if($payment->save()) {
            return $payment;
        }

        static::log(['ошибка создание платежа', $payment->getErrors()]);

        return null;
    }

    /**
     * Проверка сигнатуры платежа
     * @param bool $updateStatus обновить статус платежа
     * @return Payment|false в случае успеха возвращает объект платежа
     */
    public static function checkSignatureByRequest($updateStatus=false)
    {
        if($invoiceId=R::get('InvId')) {
            if($payment=Payment::modelByInvoiceId($invoiceId)) {
                $success=(strtoupper(R::get('SignatureValue')) === strtoupper(static::createCheckSignatureByRequest()));
                
                if($updateStatus) {
                    $payment->updateStatus($success ? self::STATUS_PAID_NOT_CHECKED : self::STATUS_CANCEL_NOT_CHECKED);
                }

                return $success ? $payment : false;
            }
        }
        return false;
    }

    /**
     * Проверка статуса платежа
     *
     * @link https://docs.robokassa.ru/#2338
     *
     * @param Payment $payment объект платежа
     */
    public static function checkStatus($payment)
    {
        return;
        // только в боевом режиме
        // HEvent::raise('onRobokassaStatusChanged', compact('payment'));
        // $xml=file_get_contents('https://auth.robokassa.ru/Merchant/WebService/Service.asmx/OpState'
        //    . '?MerchantLogin=' . static::getMerchantLogin() 
        //    . '&InvoiceID=' . $payment->getInvoiceId() 
        //    . '&Signature=' . static::createOpSignature($payment));
    }

    /**
     * Запись логов
     */
    public static function log($data)
    {
        if(static::isDebugMode()) {
            $filename=HFile::path([\Yii::getPathOfAlias(self::LOG_PATH), date('Y_m_d').'.log'], true);
            
            file_put_contents($filename, date('[Y.m.d H:i:s] ') . var_export($data, true) . "\n\n", FILE_APPEND);
        }
    }
}