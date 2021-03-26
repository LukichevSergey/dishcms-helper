<?php
namespace ecommerce\modules\sberbank\controllers;

use common\components\helpers\HRequest as R;
use ecommerce\modules\order\models\Order;
use ecommerce\modules\sberbank\components\helpers\HSberbank;

/**
 * Контроллер оплаты
 * 
 */
class PayController extends \ecommerce\modules\sberbank\components\base\BaseController
{
    public function actionPay()
    {
        
    }
    
    public function actionSuccess()
    {
        if($orderId=R::get('orderId')) {
            if($order=Order::model()->findByAttributes(['payment_id'=>$orderId])) {
                $status=HSberbank::api()->getOrderStatus($orderId, true);
                if((int)$status['OrderStatus'] === 2) {
			$this->prepareSeo('Заказ успешно оплачен');
	                $this->breadcrumbs->add('Оплата заказа');
                    $this->render('success');
                }
                else {
			$this->prepareSeo('Произошла ошибка');
			$this->breadcrumbs->add('Оплата заказа');
                    $this->render('wait', ['message'=>$status['ErrorMessage']]);
                }
            }
            else {
                R::e404();
            }
        }
        else {
            R::e404();
        }
    }
    
    public function actionFail()
    {
	$this->prepareSeo('Произошла ошибка');
        $this->breadcrumbs->add('Оплата заказа');
        $this->render('fail');
    }
}
