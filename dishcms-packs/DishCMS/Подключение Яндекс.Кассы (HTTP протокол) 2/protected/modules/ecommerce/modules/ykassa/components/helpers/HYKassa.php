<?php
namespace ykassa\components\helpers;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HRequest as R;
use common\components\helpers\HFile;
use settings\components\helpers\HSettings;
use crud\components\helpers\HCrud;

class HYKassa
{
    const STATUS_NEW='new';
    const STATUS_INPAY='inpay';
    const STATUS_PAID='paid';
    const STATUS_CANCEL='cancel';
    const STATUS_ERROR='error';
    
    public static function settings()
    {
        return HSettings::getById('ykassa');        
    }
    
    public static function getAdminMenuItem()
    {
        if(static::isCustomForm()) {
            return HCrud::getMenuItems(Y::controller(), 'ykassa_custom_payments', 'crud/index', true);
        }
        else {
            return HSettings::getMenuItems(Y::controller(), 'ykassa', 'settings/index', true);
        }
    }
    
    public static function getStatusLabels()
    {
        return [
            self::STATUS_NEW=>'Ожидает оплаты',
            self::STATUS_INPAY=>'В процессе оплаты',
            self::STATUS_PAID=>'Оплачен',
            self::STATUS_CANCEL=>'Отменен',
            self::STATUS_ERROR=>'Ошибка'
        ];
    }
    
    public static function isOnlinePaymentType($payment)
    {
        return ($payment == static::settings()->order_form_payment_type);
    }
    
    public static function getStatusLabel($status)
    {
        return A::get(static::getStatusLabels(), $status, 'Неопределен');
    }
    
    public static function getStatusCssClass($status)
    {
        $cssClasses=[
            self::STATUS_NEW=>'primary',
            self::STATUS_INPAY=>'info',
            self::STATUS_PAID=>'success',
            self::STATUS_CANCEL=>'warning',
            self::STATUS_ERROR=>'danger'
        ];
        
        return A::get($cssClasses, $status, 'default');
    }
    
    public static function getStatusTag($status)
    {
        return \CHtml::tag('span', ['class'=>'label label-'.static::getStatusCssClass($status)], static::getStatusLabel($status));
    }
    
    public static function getPaymentSubjectTypeList()
    {
        return [
            'commodity'=>'товар',
            'excise'=>'подакцизный товар',
            'job'=>'работа',
            'service'=>'услуга',
            'gambling_bet'=>'ставка в азартной игре',
            'gambling_prize'=>'выигрыш в азартной игре',
            'lottery'=>'лотерейный билет',
            'lottery_prize'=>'выигрыш в лотерею',
            'intellectual_activity'=>'результаты интеллектуальной деятельности',
            'payment'=>'платеж',
            'agent_commission'=>'агентское вознаграждение',
            'property_right'=>'имущественные права',
            'non_operating_gain'=>'внереализационный доход',
            'insurance_premium'=>'страховой сбор',
            'sales_tax'=>'торговый сбор',
            'resort_fee'=>'курортный сбор',
            'composite'=>'несколько вариантов',
            'another'=>'другое'
        ];
    }
    
    public static function getPaymentSubjectType()
    {
        return static::settings()->payment_subject_type;
    }
    
    public static function getPaymentMethodTypeList()
    {
        return [
            'full_prepayment'=>'полная предоплата',
            'partial_prepayment'=>'частичная предоплата',
            'advance'=>'аванс',
            'full_payment'=>'полный расчет',
            'partial_payment'=>'частичный расчет и кредит',
            'credit'=>'кредит',
            'credit_payment'=>'выплата по кредиту'
        ];
    }
    
    public static function getPaymentMethodType()
    {
        return static::settings()->payment_method_type;
    }
    
    public static function getTaxList()
    {
        return [
            1=>'без НДС',
            2=>'ставка НДС 0%',
            3=>'ставка 10%',
            4=>'ставка 20%',
            5=>'расчетная ставка 10/110',
            6=>'расчетная ставка 20/120',
        ];
    }
    
    public static function getTaxSystemList()
    {
        return [
            1=>'общая СН',
            2=>'упрощенная СН (доходы)',
            3=>'упрощенная СН (доходы минус расходы)',
            4=>'единый налог на вмененный доход',
            5=>'единый сельскохозяйственный налог',
            6=>'патентная СН',
        ];
    }
    
    public static function getTax()
    {
        return (int)static::settings()->tax;
    }
    
    public static function isDebugMode()
    {
        return ((int)static::settings()->enable_debug_mode === 1);
    }
    
    public static function isTestMode()
    {
        return ((int)static::settings()->enable_test_mode === 1);
    }
    
    public static function isCustomForm()
    {
        return ((int)static::settings()->is_custom_form === 1);
    }
    
    public static function getFormAction()
    {
        if(static::isTestMode()) {
            return 'https://money.yandex.ru/eshop.xml';
        }
        else {
            return 'https://money.yandex.ru/eshop.xml';
        }
    }
    
    public static function getShopId()
    {
        if(static::isTestMode()) {
            return (string)static::settings()->test_shop_id;
        }
        else {
            return (string)static::settings()->shop_id;
        }
    }
    
    public static function getScId()
    {
        if(static::isTestMode()) {
            return (string)static::settings()->test_scid;
        }
        else {
            return (string)static::settings()->scid;
        }
    }
    
    public static function getShopPassword()
    {
        if(static::isTestMode()) {
            return (string)static::settings()->test_shop_password;
        }
        else {
            return (string)static::settings()->shop_password;
        }
    }
    
    public static function log($data)
    {
        if(static::isDebugMode()) {
            $filename=HFile::path([\Yii::getPathOfAlias('application.runtime.ykassa'), date('Y_m_d').'.log'], true);
            
            file_put_contents($filename, date('[Y.m.d H:i:s] ') . var_export($data, true) . "\n\n", FILE_APPEND);
        }
    }
    
    public static function normalizeSumAmount($amount)
    {
        return round((float)$amount, 2, PHP_ROUND_HALF_UP);
    }
    
    public static function getSumAmountFormatted($amount)
    {
        return sprintf('%0.2f', static::normalizeSumAmount($amount));
    }
    
    /**
     * Проверить хэш операции
     * @param mixed $orderSumAmount сумма заказа. 
     * По умолчанию (null) будет получена из POST запроса.
     * Рекомендуется использовать режим NULL только для 
     * предварительной проверки запроса. 
     * @return boolean
     */
    public static function checkMd5ByPost($orderSumAmount=null)
    {
        if($orderSumAmount === null) {
            $orderSumAmount=R::post('orderSumAmount');
        }
        
        $md5=mb_strtoupper(md5((string)implode(';', [
            R::post('action'),
            static::getSumAmountFormatted($orderSumAmount),
            R::post('orderSumCurrencyPaycash'),
            R::post('orderSumBankPaycash'),
            static::getShopId(),
            R::post('invoiceId'),
            R::post('customerNumber'),
            static::getShopPassword()
        ])));
        
        return ((string)R::post('md5') === $md5);
    }
    
    public static function sendCheckOrderResponse($invoiceId, $code=200)
    {
        echo '<?xml version="1.0" encoding="UTF-8"?><checkOrderResponse performedDatetime="'.date('c').'" code="'.(string)$code.'" invoiceId="'.$invoiceId.'" shopId="'.static::getShopId().'"/>';
        exit;
    }
    
    public static function sendPaymentAvisoResponse($invoiceId, $code=200)
    {
        echo '<?xml version="1.0" encoding="UTF-8"?><paymentAvisoResponse performedDatetime="'.date('c').'" code="'.(string)$code.'" invoiceId="'.$invoiceId.'" shopId="'.static::getShopId().'"/>';
        exit;
    }
}
