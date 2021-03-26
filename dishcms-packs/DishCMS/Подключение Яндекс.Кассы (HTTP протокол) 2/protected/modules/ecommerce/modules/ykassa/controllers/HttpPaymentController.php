<?php
/**
 * Яндекс.Касса (HTTP-протокол)
 *  
 * Оплата заказа
 */
namespace ykassa\controllers;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HRequest as R;
use ykassa\components\helpers\HYKassa;
use DOrder\models\DOrder;

class HttpPaymentController extends \Controller
{
    /**
     * Отображение страницы оплаты
     * @param string $hash хэш заказа.
     */
    public function actionIndex($hash)
    {        
        if(!empty($hash) && ($order=DOrder::model()->wcolumns(['hash'=>$hash])->find()) && (!$order->paid)) {
			$this->breadcrumbs->add(HYKassa::settings()->title_payment_form);
			
			$this->render('ykassa.views.httpPayment.index', compact('order'));
		}
		else {
			R::e404();
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
            if($order=DOrder::model()->wcolumns(['hash'=>R::post('orderNumber')])->find()) {
                if(HYKassa::checkMd5ByPost($order->getTotalPrice())) {
                    $code=200;
                    switch(R::post('action')) {
                        case 'checkOrder':
                            $code=100;
                            $order->yandex_payment_id=$invoiceId;
                            $order->in_paid=1;
                            if($order->save()) {
                                $code=0;
                            }
                            break;
                            
                        case 'cancelOrder':
                            $code=100;
                            $order->yandex_payment_id=$invoiceId;
                            $order->in_paid=0;
                            $order->paid=0;
                            if($order->save()) {
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
            if($order=DOrder::model()->wcolumns(['hash'=>R::post('orderNumber')])->find()) {
                if(HYKassa::checkMd5ByPost($order->getTotalPrice())) {
                    $order->paid=1;
                    $order->in_paid=0;
                    if($order->save()) {
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
        
    	$order=null;
    	if(R::get('shopId') === HYKassa::getShopId()) {
        	$order=DOrder::model()->wcolumns(['hash'=>R::get('orderNumber')])->find();
    	}
    	
    	if(empty($order)) {
    		R::e404();
    	}
    	
    	$this->prepareSeo(HYKassa::settings()->title_success);
    	
    	$this->breadcrumbs->add(HYKassa::settings()->title_success);
    	
        $this->render('ykassa.views.httpPayment.success', compact('order'));
    }
    
    /**
     * Боевые платежи:
     * shopFailUrl
     */
    public function actionYmfail()
    {
        $this->log('actionYmfail');
        
        $order=null;
        if(R::get('shopId') === HYKassa::getShopId()) {
            $order=DOrder::model()->wcolumns(['hash'=>R::get('orderNumber')])->find();
        }
        
        if(empty($order)) {
            R::e404();
        }
        
        $this->prepareSeo(HYKassa::settings()->title_fail);
        
        $this->breadcrumbs->add(HYKassa::settings()->title_fail);
        
        $this->render('ykassa.views.httpPayment.fail', compact('order'));        
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
            'title'=>$title,
            '$_GET'=>$_GET,
            '$_POST'=>$_POST,
            '$_REQUEST'=>$_REQUEST,
        ]);		
    }
}
