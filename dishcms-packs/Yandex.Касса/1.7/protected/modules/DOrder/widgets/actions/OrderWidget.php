<?php
/**
 * Виджет оформления заказа.
 * 
 * @use \YiiHelper (>=1.02)
 * @use \CmsCore
 * @use \DCart\components\DCart \Yii::app()->cart
 */
namespace DOrder\widgets\actions;

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
	 * Тип формы, по умолчанию OrderWidget::TYPE_CUSTOMER
	 * @var integer
	 */
	public $type; 
	
	/**
	 * (non-PHPdoc)
	 * @see CWidget::run()
	 */
	public function run()
	{
		$emailPrefix = '';
		switch($this->type) {
			case \DOrder\models\DOrder::TYPE_YANDEX:
				$modelClass = '\DOrder\models\YandexForm';
				$emailPrefix = '_ym';
				break;
			default: 
				$modelClass = '\DOrder\models\CustomerForm';
		}
		$modelForm = new $modelClass;
		
		$attributes = \Yii::app()->request->getPost(\YiiHelper::slash2_($modelForm));
		if($attributes) {
			$modelForm->attributes = $attributes; 
			if($modelForm->validate()) {
				$order = new DOrder();
				
				// @hook Yandex.Money
				$modelForm->Sum = \Yii::app()->cart->getTotalPrice();
				
				$order->customer_data = $modelForm->getAttributes(null, true, true);
				$order->order_data = \Yii::app()->cart->getData(true, true);

				if($order->save()) {
					\Yii::app()->cart->clear();
					
					// @hook Yandex.Money
					$modelForm->orderNumber = $order->id;
					$order->customer_data = $modelForm->getAttributes(null, true, true);
					
					$messageAdmin = $this->renderInternal(__DIR__ . DS . 'views' . DS . 'email' . DS . $emailPrefix . '_admin.php', array('model'=>$order), true);
					$messageClient = $this->renderInternal(__DIR__ . DS . 'views' . DS . 'email' . DS . $emailPrefix . '_client.php', array('model'=>$order), true);
					
					if (\CmsCore::sendMail($messageAdmin) && $modelForm->email) {
						\CmsCore::sendMail(
							$messageClient, 
							'Заказ #'. $order->id .' на сайте ' . \Yii::app()->name, 
							$modelForm->email
						);
					}
					
					\Yii::app()->user->setFlash('dorder', 'Спасибо, Ваш заказ отправлен!');
					
					// @hook Yandex.Money
					if($modelForm->paymentType == 'AC' || $modelForm->paymentType == 'PC') {
						$this->render('_ym_form', array('model'=>$modelForm));
						\Yii::app()->end();
					}
					
					$this->owner->refresh();				
				}
			}			
		}
		
		$this->render('order', compact('modelForm'));
	}
}