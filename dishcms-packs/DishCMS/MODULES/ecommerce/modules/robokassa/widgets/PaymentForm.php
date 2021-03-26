<?php
namespace ecommerce\modules\robokassa\widgets;

use crud\models\ar\robokassa\models\Payment;
use ecommerce\modules\robokassa\components\helpers\HRobokassa;

class PaymentForm extends \common\components\base\Widget
{
    public $paymentId;
    public $payment;

    public $action=null;
    public $method='POST';
    public $culture='ru';
    public $currency='';

    public $submitLabel='Оплатить';
    public $submitOptions=['class'=>'btn robokassa__btn-pay'];

    public function run()
    {
        if(!$this->action) {
            $this->action=HRobokassa::API_PAYMENT_URL;
        }

        if($this->paymentId) {
            $this->payment=Payment::modelByPaymentId($this->paymentId);
        }

        if($this->payment instanceof Payment) {
            $this->render('payment_form');
        }
        else {
            throw new \CException('Incorrect robokassa payment');
        }
    }
}