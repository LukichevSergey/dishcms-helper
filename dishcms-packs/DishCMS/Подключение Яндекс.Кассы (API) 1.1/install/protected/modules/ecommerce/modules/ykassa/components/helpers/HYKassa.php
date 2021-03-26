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

    const API_STATUS_NEW='new';
    const API_STATUS_PENDING='pending';
    const API_STATUS_WAITING_FOR_CAPTURE='waiting_for_capture';
    const API_STATUS_SUCCEEDED='succeeded';
    const API_STATUS_CANCELED='canceled';

    const MODE_API=1;
	const MODE_API_TEST=2;
	const MODE_HTTP=3;
	const MODE_HTTP_TEST=4;
    
    /**
     * Получить модель настроек Яндекс.Кассы
     *
     * @return \ykassa\models\YKassaSettings
     */
    public static function settings()
    {
        return HSettings::getById('ykassa');        
    }

    public static function isAPIMode()
    {
        return (static::settings()->mode == self::MODE_API);
    }

    public static function isAPITestMode()
    {
        return (static::settings()->mode == self::MODE_API_TEST);
    }

    public static function getApiConfigDefault()
    {
        return static::settings()->api_default_config ?: 'ecommerce_order';
    }
    
    public static function getAdminMenuItem()
    {
        return HCrud::getMenuItems(Y::controller(), 'ykassa_payments', 'crud/index', true);
    }

    /**
     * Получить номер телефона в формате ITU-T E.164
     * @link https://ru.wikipedia.org/wiki/E.164
     */
    public static function getE164Phone($phone) 
    {
        return preg_replace('/^[8]/', '7', preg_replace('/[^+0-9]+/', '', $phone));
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

    public static function getApiStatusLabels()
    {
        return [
            self::API_STATUS_NEW=>'Ожидает перехода на страницу оплаты',
            self::API_STATUS_PENDING=>'Платеж создан и ожидает действий от пользователя',
            self::API_STATUS_WAITING_FOR_CAPTURE=>'Платеж оплачен, деньги авторизованы и ожидают списания',
            self::API_STATUS_SUCCEEDED=>'Платеж успешно завершен',
            self::API_STATUS_CANCELED=>'Платеж отменен'
        ];
    }
    
    /**
     * Проверяет является ли переданный метод платежа ОНЛАЙН
     *
     * @param string $paymentType
     * @return boolean
     */
    public static function isOnlinePaymentType($payment)
    {
    	if(trim($payment) && static::settings()->online_payment_types) {
            return in_array(
                trim(mb_strtoupper($payment)), 
                array_map('mb_strtoupper', array_map('trim', explode("\n", static::settings()->online_payment_types)))
            );
        }

        return false;
    }

    /**
     * Проверяет является ли переданный тип платежом онлайн и если да, 
     * то создает платеж, и перенаправляет на страницу оплаты.
     *
     * @param string $paymentType проверяемый тип платежа
     * @param array $paymentParams дополнительные парамеры для платежа
     * @param [type] $config имя api конфигурации, если не задано, будет 
     * использовано, заданные по умолчанию.
     * @return bool
     */
    public static function checkOnlinePayment($paymentType, $paymentParams=[], $config=null)
    {
        if(!$config) {
            $config=static::getApiConfigDefault();
        }

        if(static::isOnlinePaymentType($paymentType)) {
            if($payment=HYKassaApi::payment($config, $paymentParams)) {
            	if(static::settings()->page_payment_skip) {
            		if($confirmation=$payment->getConfirmation()) {
            			if($confirmationUrl=$confirmation->getConfirmationUrl()) {
            				 Y::controller()->redirect($confirmationUrl);
            			}
            		}
            	}
                Y::controller()->redirect(Y::createUrl('/payment/' . $payment->getId()));
                Y::end();
            }
        }

        return false;
    }
    
    public static function getStatusLabel($status)
    {
        if(static::isApiMode() || static::isApiTestMode()) {
            return A::get(static::getApiStatusLabels(), $status, 'Неопределен');
        }
        else {
            return A::get(static::getStatusLabels(), $status, 'Неопределен');
        }
    }
    
    public static function getStatusCssClass($status)
    {
        if(static::isApiMode() || static::isApiTestMode()) {
            $cssClasses=[
                self::API_STATUS_PENDING=>'info',
                self::API_STATUS_WAITING_FOR_CAPTURE=>'warning',
                self::API_STATUS_SUCCEEDED=>'success',
                self::API_STATUS_CANCELED=>'danger'
            ];
        }
        else {
            $cssClasses=[
                self::STATUS_NEW=>'primary',
                self::STATUS_INPAY=>'info',
                self::STATUS_PAID=>'success',
                self::STATUS_CANCEL=>'warning',
                self::STATUS_ERROR=>'danger'
            ];
        }
        
        return A::get($cssClasses, $status, 'default');
    }
    
    public static function getStatusTag($status)
    {
        if(static::isApiMode() || static::isApiTestMode()) {
            return \CHtml::tag('div', ['style'=>'margin-bottom:0', 'class'=>'alert alert-'.static::getStatusCssClass($status)], static::getStatusLabel($status));
        }
        else {
            return \CHtml::tag('span', ['class'=>'label label-'.static::getStatusCssClass($status)], static::getStatusLabel($status));
        }
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

    public static function getTaxSystem()
    {
        return (int)static::settings()->tax_system;
    }
    
    public static function isDebugMode()
    {
        return ((int)static::settings()->enable_debug_mode === 1);
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
    
    public static function checkPaymentStatusByParams($params)
    {
        return HYKassaApi::checkPaymentStatusByParams($params);
    }

    public static function checkPaymentStatus($paymentId)
    {
        return HYKassaApi::checkPaymentStatus($paymentId);
    }
}
