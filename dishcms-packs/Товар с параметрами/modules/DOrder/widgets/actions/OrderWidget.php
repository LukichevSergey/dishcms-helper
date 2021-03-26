<?php
/**
 * Виджет оформления заказа.
 * 
 * @use \YiiHelper (>=1.02)
 * @use \CmsCore
 * @use \DCart\components\DCart \Yii::app()->cart
 */
namespace DOrder\widgets\actions;

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
		$customerForm = new CustomerForm;
		
		$attributes = \Yii::app()->request->getPost(\YiiHelper::slash2_($customerForm));
		if($attributes) {
			$customerForm->attributes = $attributes; 
			if($customerForm->validate()) {
				$order = new DOrder();
				$order->customer_data = $customerForm->getAttributes(null, true, true);
				$order->order_data = \Yii::app()->cart->getData(true, true);
				
				if($order->save()) {
					\Yii::app()->cart->clear();
					
					$messageAdmin = $this->renderInternal(__DIR__ . DS . 'views' . DS . 'email' . DS . '_admin.php', array('model'=>$order), true);
					$messageClient = $this->renderInternal(__DIR__ . DS . 'views' . DS . 'email' . DS . '_client.php', array('model'=>$order), true);
					
					if (\CmsCore::sendMail($messageAdmin)) {
						\CmsCore::sendMail(
							$messageClient, 
							'Заказ #'. $order->id .' на сайте ' . \Yii::app()->name, 
							$customerForm->email
						);
					}
					
					\Yii::app()->user->setFlash('dorder', 'Спасибо, Ваш заказ отправлен!');
					
					$this->owner->refresh();				
				}
			}			
		}
		
		$this->render('order', compact('customerForm'));
	}
}