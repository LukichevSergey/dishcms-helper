<?php
/**
 * Яндекс.Касса (HTTP-протокол)
 *
 * Оплата произвольной суммы
 */
namespace ykassa\controllers;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HRequest as R;
use ykassa\components\helpers\HYKassa;
use common\components\helpers\HModel;
use common\components\helpers\HHash;
use crud\models\ar\ykassa\models\CustomPayment;

class HttpPaymentCustomController extends \Controller
{
    /**
     * Отображение страницы оплаты
     * @param string $hash хэш заказа.
     */
    public function actionIndex()
    {
        /*
        if(\D::isDevMode() && isset($_REQUEST['payment-id'])) {
            if($payment=CustomPayment::model()->findByAttributes(['order_number'=>$_REQUEST['payment-id']])) {
                $paymentFormId=HHash::u('ymhttp');
                $this->render('ykassa.views.httpPaymentCustom._payment_form', compact('payment', 'paymentFormId'));
                Y::end();
            }
        }
        /**/
        
        $payment=HModel::massiveAssignment('\crud\models\ar\ykassa\models\CustomPayment', true);
        
        $payment->sum=(int)$payment->sum;
        
        if(R::isAjaxRequest() && !$payment->validate()) {
            echo \CActiveForm::validate($payment);
        }
        elseif(R::isAjaxRequest()) {
            if($payment->save()) {
                $paymentFormId=HHash::u('ymhttp');
                echo json_encode([
                    'yf'=>$this->renderPartial('ykassa.views.httpPaymentCustom._payment_form', compact('payment', 'paymentFormId'), true),
                    'yfid'=>$paymentFormId,
                    'yftxt'=>'Идет перенаправление на сайт сервиса <b>Яндекс.Касса</b> для продолжения платежа...<br/>Если этого не произошло нажмите повторно кнопку "Оплатить".'
                ], JSON_UNESCAPED_UNICODE);
            }
            else {
                echo \CActiveForm::validate($payment);
            } 
        }
        else {
            $this->prepareSeo(HYKassa::settings()->title_payment_form);
            
            $this->breadcrumbs->add(HYKassa::settings()->title_payment_form);
                
            $this->render('ykassa.views.httpPaymentCustom.index', compact('payment'));
        }
    }
    
    /**
     * Боевые платежи:
     * checkUrl
     */
    public function actionYmcheck()
    {
        $this->log('actionYmcheck');
        
        $code=200;
        $invoiceId=R::post('invoiceId');
        
        if(HYKassa::checkMd5ByPost()) {
            $code=100;
            if($payment=CustomPayment::model()->wcolumns(['order_number'=>R::post('orderNumber')])->find()) {
                if(HYKassa::checkMd5ByPost((float)$payment->sum)) {
                    $code=200;
                    switch(R::post('action')) {
                        case 'checkOrder':
                            $code=100;
                            $payment->invoice_id=$invoiceId;
                            $payment->status=HYKassa::STATUS_INPAY;
                            if($payment->save()) {
                                $code=0;
                            }
                            break;
                            
                        case 'cancelOrder':
                            $code=100;
                            $payment->invoice_id=$invoiceId;
                            $payment->status=HYKassa::STATUS_CANCEL;
                            if($payment->save()) {
                                $code=0;
                            }
                            break;
                    }
                }
                else {
                    $code=1;
                }
            }
        }
        else {
            $code=1;
        }
        
        HYKassa::sendCheckOrderResponse($invoiceId, $code);
    }
    
    /**
     * Боевые платежи:
     * avisoUrl
     */
    public function actionYmaviso()
    {
        $this->log('actionYmaviso');
        
        $code=200;
        $invoiceId=R::post('invoiceId');
        
        if(HYKassa::checkMd5ByPost()) {
            if($payment=CustomPayment::model()->wcolumns(['order_number'=>R::post('orderNumber'), 'invoice_id'=>$invoiceId])->find()) {
                if(HYKassa::checkMd5ByPost((float)$payment->sum)) {
                    $payment->status=HYKassa::STATUS_PAID;
                    if($payment->save()) {
                        $code=0;
                    }
                }
                else {
                    $code=1;
                }
            }
        }
        else {
            $code=1;
        }
        
        HYKassa::sendPaymentAvisoResponse($invoiceId, $code);
    }
    
    /**
     * Боевые платежи:
     * shopSuccessUrl
     */
    public function actionYmsuccess()
    {
        $this->log('actionYmsuccess');
        
        $payment=null;
        if(R::get('shopId') === HYKassa::getShopId()) {
            $payment=CustomPayment::model()->wcolumns(['order_number'=>R::get('orderNumber')])->find();
        }
        
        if(empty($payment)) {
            R::e404();
        }
        
        $this->prepareSeo(HYKassa::settings()->title_success);
        
        $this->breadcrumbs->add(HYKassa::settings()->title_success);
        
        $this->render('ykassa.views.httpPayment.success', compact('payment'));
    }
    
    /**
     * Боевые платежи:
     * shopFailUrl
     */
    public function actionYmfail()
    {
        $this->log('actionYmfail');
        
        $payment=null;
        if(R::get('shopId') === HYKassa::getShopId()) {
            $payment=CustomPayment::model()->wcolumns(['order_number'=>R::get('orderNumber')])->find();
        }
        
        if(empty($payment)) {
            R::e404();
        }
        
        $this->prepareSeo(HYKassa::settings()->title_fail);
        
        $this->breadcrumbs->add(HYKassa::settings()->title_fail);
        
        $this->render('ykassa.views.httpPayment.fail', compact('payment'));
    }
    
    /**
     * Тестовые платежи:
     * checkUrl
     */
    public function actionYmdemocheck()
    {
        $this->log('actionYmdemocheck');
        
        $this->actionYmcheck();
    }
    
    /**
     * Тестовые платежи:
     * avisoUrl
     */
    public function actionYmdemoaviso()
    {
        $this->log('actionYmdemoaviso');
        
        $this->actionYmaviso();
    }
    
    /**
     * Тестовые платежи:
     * shopSuccessUrl
     */
    public function actionYmdemosuccess()
    {
        $this->log('actionYmdemosuccess');
        
        $this->actionYmsuccess();
    }
    
    /**
     * Тестовые платежи:
     * shopFailUrl
     */
    public function actionYmdemofail()
    {
        $this->log('actionYmdemofail');
        
        $this->actionYmfail();
    }
    
    protected function log($title)
    {
        HYKassa::log([
            'title'=>'custom_' . $title,
            '$_GET'=>$_GET,
            '$_POST'=>$_POST,
            '$_REQUEST'=>$_REQUEST,
        ]);
    }
}