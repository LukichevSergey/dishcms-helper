<?php
/**
 * Виджет оформления заказа.
 * 
 * @use \YiiHelper (>=1.02)
 * @use \CmsCore
 * @use \DCart\components\DCart \Yii::app()->cart
 */
namespace DOrder\widgets\actions;

use common\components\helpers\HYii as Y;
use \DOrder\models\CustomerForm;
use \DOrder\models\DOrder;

class OrderWidget extends BaseActionWidget
{
	/**
	 * Аттрибуты товара для отображения в письме уведомления для покупателя.
	 * Если установлено в null, будут отображены все аттрибуты
	 * @var array|null
	 */
	public $mailAttributes = null;
	
	/**
	 * Аттрибуты товара для отображения в письме уведомления для администратора.
	 * Если установлено в null, будут отображены все аттрибуты
	 * @var array|null
	 */
	public $adminMailAttributes = null;
	
	/**
	 * (non-PHPdoc)
	 * @see CWidget::run()
	 */
	public function run()
	{
		
		$customerForm = new CustomerForm('payment');
		
		$attributes = \Yii::app()->request->getPost(\YiiHelper::slash2_($customerForm));
		if($attributes) {
			$customerForm->attributes = $attributes; 
			if($customerForm->validate()) {
				if(\Yii::app()->cart->isEmpty()) {
					\Yii::app()->user->setFlash('dorder', 'Ваша корзина пуста!');
					$this->owner->refresh();
				}
				
				$order = new DOrder();
				if($customerForm->scenario=='payment') {
					$payment=$customerForm->payment;
					$customerForm->paymentType=$payment;
					//$customerForm->payment=Y::param('payment.types.'.$payment);
    			}
				$order->customer_data = $customerForm->getAttributes(null, true, true, true);
				$order->order_data = \Yii::app()->cart->getData(true, true);

				if($order->save()) {
					\Yii::app()->cart->clear();
					
					$messageAdmin = $this->renderInternal(__DIR__ . DS . 'views' . DS . 'email' . DS . '_admin.php', array('model'=>$order), true);
					$messageClient = $this->renderInternal(__DIR__ . DS . 'views' . DS . 'email' . DS . '_client.php', array('model'=>$order), true);
					
					if (\CmsCore::sendMail($messageAdmin) && $notificationEmail = $customerForm->getEmailForNotification()) {
						\CmsCore::sendMail(
							$messageClient, 
							'Заказ #'. $order->id .' на сайте ' . \Yii::app()->name, 
							$notificationEmail
						);
					}
					
					if(($customerForm->scenario == 'payment') && (in_array($customerForm->paymentType, Y::param('payment.online', [])))) {
						$this->owner->redirect($this->owner->createUrl(Y::param('payment.action'), ['hash'=>$order->hash]));
					}
					else {
						\Yii::app()->user->setFlash('dorder', 'Спасибо, Ваш заказ отправлен!');
						$this->owner->refresh();				
					}
				}
				elseif($customerForm->scenario == 'payment') {
					$customerForm->payment=$payment;
				}
			}			
		}
		
		$this->render('order', compact('customerForm'));
	}
}
