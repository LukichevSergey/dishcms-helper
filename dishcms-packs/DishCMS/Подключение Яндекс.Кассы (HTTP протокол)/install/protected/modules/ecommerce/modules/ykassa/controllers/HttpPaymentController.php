<?php
/**
 * Яндекс.Касса (HTTP-протокол)
 */
namespace ykassa\controllers;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HRequest as R;
use DOrder\models\DOrder;
//use cdek\components\CdekApi;

class HttpPaymentController extends \Controller
{
    /**
     * Отображение страницы оплаты
     * @param string $hash хэш заказа.
     */
    public function actionIndex($hash)
    {        
        if($hash && ($order=DOrder::model()->wcolumns(['hash'=>$hash])->find())) {
			$this->breadcrumbs->add('Оплата Яндекс.Касса');
			$this->render('ykassa.views.httpPayment.index', compact('order'));
            /*$client = new Client();
            $shopId=Y::param('payment.ymhttp.shopId');
            $secretKey=Y::param('payment.ymhttp.secretKey');
            $client->setAuth($shopId, $secretKey);*/
		}
		else {
			new \CHttpException(404);
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
    	$action=A::get($_POST, 'action');
    	$orderNumber=A::get($_POST, 'orderNumber');
    	$shopId=A::get($_POST, 'shopId');
    	$invoiceId=A::get($_POST, 'invoiceId');
    	if($orderNumber && $invoiceId && $shopId) {
    		$code=100;
    		if($shopId===Y::param('payment.ymhttp.shopId')) {
	    		if($order=DOrder::model()->wcolumns(['hash'=>$orderNumber])->find()) {
					$code=200;
	    			if($action=='checkOrder') {
	    			    $code=100;
		    			$order->yandex_payment_id=$invoiceId;
		    			$order->in_paid=1;
		    			if($order->save()) {
        	    			$code=0;
    	                }
	    			}
	    			elseif($action=='cancelOrder') {
	    			    $code=100;
	    				$order->yandex_payment_id=$invoiceId;
		    			$order->in_paid=0;
		    			$order->paid=0;
		    			if($order->save()) {
							$code=0;
						}
	    			}
	    		}
    		}
    	}
    	echo '<?xml version="1.0" encoding="UTF-8"?><checkOrderResponse performedDatetime="'.date('c').'" code="'.(string)$code.'" invoiceId="'.$invoiceId.'" shopId="'.$shopId.'"/>';
    	exit;
    }
    
    /**
     * Боевые платежи:
     * avisoUrl
     */
    public function actionYmaviso()
    {
        $this->log('actionYmaviso');
        
        $code=200;
    	$orderNumber=A::get($_POST, 'orderNumber');
    	$shopId=A::get($_POST, 'shopId');
    	$invoiceId=A::get($_POST, 'invoiceId');
    	$orderSumAmount=(float)A::get($_POST, 'orderSumAmount', 0);
    	if($orderNumber && $invoiceId && $shopId) {
    		if($shopId===Y::param('payment.ymhttp.shopId')) {
	    		if($order=DOrder::model()->wcolumns(['hash'=>$orderNumber, 'yandex_payment_id'=>$invoiceId])->find()) {
	    			$order->paid=1;
	    			$order->in_paid=0;
	    			if(($orderSumAmount == $order->getTotalPrice()) && $order->save()) {
	    			    $code=0;                        
                    }
	    		}
    		}
    	}
    	echo '<?xml version="1.0" encoding="UTF-8"?><paymentAvisoResponse performedDatetime="'.date('c').'" code="'.(string)$code.'" invoiceId="'.$invoiceId.'" shopId="'.$shopId.'"/>';
    	exit;
    }
    
    /**
     * Боевые платежи:
     * shopSuccessUrl
     */
    public function actionYmsuccess()
    {
        $this->log('actionYmsuccess');
        
    	$shopId=A::get($_GET, 'shopId');
        $orderNumber=A::get($_GET, 'orderNumber');
    	//$invoiceId=A::get($_GET, 'invoiceId');
    	$order=false;
    	if($shopId===Y::param('payment.ymhttp.shopId')) {
        	$order=DOrder::model()->wcolumns(['hash'=>$orderNumber/*, 'yandex_payment_id'=>$invoiceId*/])->find();
    	}
    	
    	if(!$order) {
    		R::e404();
    	}
    	
    	$this->prepareSeo('Заказ оплачен');
    	$this->breadcrumbs->add('Заказ оплачен');
        $this->render('success', compact('order'));
    }
    
    /**
     * Боевые платежи:
     * shopFailUrl
     */
    public function actionYmfail()
    {
        $this->log('actionYmfail');
        
        $shopId=A::get($_GET, 'shopId');
        $orderNumber=A::get($_GET, 'orderNumber');
    	//$invoiceId=A::get($_GET, 'invoiceId');
    	$order=false;
    	if($shopId===Y::param('payment.ymhttp.shopId')) {
        	$order=DOrder::model()->wcolumns(['hash'=>$orderNumber/*, 'yandex_payment_id'=>$invoiceId*/])->find();
    	}
    	
    	if(!$order) {
    		R::e404();
    	}
    	
        $this->prepareSeo('Ошибка оплаты');
    	$this->breadcrumbs->add('Ошибка оплаты');
        $this->render('fail', compact('order'));
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
		return false;
        $data=date('Y.m.d H:i:s')."\n";
        $data.="--- {$title} ---\n";
        $data.='$_REQUEST: ' . var_export($_REQUEST, true)."\n";
        $data.='$_POST: ' . var_export($_POST, true)."\n";
        $data.='$_GET: ' . var_export($_GET, true)."\n";
        $data.="\n";
        //file_put_contents(dirname(__FILE__).'/ym.log', $data, FILE_APPEND);
    }
}
