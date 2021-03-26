<?php
/**
 * Order button widget
 */
namespace DOrder\widgets\admin;

use \DOrder\models\DOrder;

class OrderButtonWidget extends \CWidget
{
	/**
	 * (non-PHPdoc)
	 * @see CWidget::run()
	 */
	public function run()
	{
		$model = DOrder::model();

		$this->render('order_button', compact('model'));
	}
}