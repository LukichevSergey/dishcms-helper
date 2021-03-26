<?php
namespace ecommerce\modules\robokassa\controllers;

use common\components\helpers\HYii as Y;
use common\components\helpers\HRequest as R;
use common\components\helpers\HEvent;
use crud\models\ar\robokassa\models\Payment;
use ecommerce\modules\robokassa\components\base\Controller;
use ecommerce\modules\robokassa\components\helpers\HRobokassa;

class PaymentController extends Controller
{
    /**
	 * @var string путь к шаблонам контроллера.
	 */
    public $viewPathPrefix='ecommerce.modules.robokassa.views.payment.';
    
    /**
     * Страница оплаты
     *
     * @param int $id UUID оплаты
     * @return void
     */
    public function actionDo($id)
    {
        if($payment=Payment::modelByPaymentId($id)) {
            $this->prepareSeo(HRobokassa::settings()->title_payment_form);
            $this->render($this->viewPathPrefix . 'do', compact('payment'));
        }
        else {
            R::e404();
        }
    }

    /**
     * Проверка платежа
     */
    public function actionResult()
    {
        if(!R::get('SignatureValue')) {
            R::e404();
        }

        HRobokassa::log(['Проверка платежа', $_REQUEST]);

        if($payment=HRobokassa::checkSignatureByRequest(true)) {
            HEvent::raise('onRobokassaResultOk', compact('payment'));
            echo "OK{$payment->getInvoiceId()}\n";
        }
        else {
            echo "bad sign\n";
        }

        Y::end();
    }
    
    /**
     * Страница успешной оплаты
     */
    public function actionSuccess()
    {
        $this->prepareSeo(HRobokassa::settings()->title_success);
        $this->render($this->viewPathPrefix . 'success');
    }
    
    /**
     * Страница ошибки платежа
     */
    public function actionFail()
    {
        $this->prepareSeo(HRobokassa::settings()->title_fail);
        $this->render($this->viewPathPrefix . 'fail');
    }
}