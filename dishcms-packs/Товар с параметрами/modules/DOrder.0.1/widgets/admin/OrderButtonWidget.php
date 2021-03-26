<?php
/**
 * Order button widget
 */
namespace DOrder\widgets\admin;

use \DOrder\models\Order;

class OrderButtonWidget extends \CWidget
{
	/**
	 * (non-PHPdoc)
	 * @see CWidget::run()
	 */
	public function run()
	{
		$model = Order::model();

		$this->render('order_button', compact('model'));
	}
}