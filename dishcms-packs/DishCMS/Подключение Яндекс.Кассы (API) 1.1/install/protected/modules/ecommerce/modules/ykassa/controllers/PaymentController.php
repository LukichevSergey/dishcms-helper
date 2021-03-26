<?php
/**
 * Яндекс.Касса (API)
 *
 * Страницы оплаты
 */
namespace ykassa\controllers;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HRequest as R;
use common\components\helpers\HModel;
use common\components\helpers\HHash;
use crud\models\ar\ykassa\models\History;
use crud\models\ar\ykassa\models\PaymentExtra;
use ecommerce\modules\order\models\Order;
use ykassa\components\helpers\HYKassa;
use ykassa\components\helpers\HYKassaHistory;

class PaymentController extends \Controller
{
    /**
     * Отображение страницы оплаты
     * @param $id идентификатор платежа в Яндекс.Кассе
     */
    public function actionIndex($id)
    {
        if($payment=History::model()->findByAttributes(['payment_id'=>$id])) {
        	if($confirmationUrl=HYKassaHistory::getConfirmationUrl($payment)) {
            	$this->prepareSeo(HYKassa::settings()->page_payment_title);
                $this->breadcrumbs->add(HYKassa::settings()->page_payment_title);
                $this->render('ykassa.views.payment.index', compact('payment', 'confirmationUrl'));
                    
                return true;
            }
        }

        R::e400();
    }

    /**
     * Отображение страницы возврата после оплаты
     * @param $id внутренний UUID идентификатор платежа
     */
    public function actionConfirm($id)
    {
        if($payment=History::model()->findByAttributes(['uuid'=>$id])) {
            HYKassa::checkPaymentStatus($payment->payment_id);
            $payment=HYKassaHistory::getByPaymentId($payment->payment_id);
            
            $viewData=['payment'=>$payment];
            switch($payment->status) {
                case HYKassa::API_STATUS_SUCCEEDED:
                case HYKassa::API_STATUS_WAITING_FOR_CAPTURE:
                    $pageTitle=HYKassa::settings()->page_success_title;
                    $view='success';
                break;

                case HYKassa::API_STATUS_PENDING:
                    $pageTitle='Ожидает оплаты';
                    $view='pending';
                    $viewData['confirmationUrl']=HYKassaHistory::getConfirmationUrl($payment);
                break;

                case HYKassa::API_STATUS_CANCELED:
                    $pageTitle=HYKassa::settings()->page_fail_title;
                    $view='fail';
                break;

                default:
                    R::e400();
                break;
            }

            $this->prepareSeo($pageTitle);
            $this->breadcrumbs->add($pageTitle);
            
            $this->render("ykassa.views.payment.{$view}", $viewData);

            return true;
        }

        R::e400();
    }
}