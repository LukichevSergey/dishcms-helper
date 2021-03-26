<?php
/**
 * DCart base widget class for actions.
 *   
 * @use \AjaxHelper
 */
namespace DCart\widgets\actions;

class BaseActionWidget extends \CWidget
{
	/**
	 * Подготовить данные к отдаче
	 * @param \AjaxHelper &$ajaxHelper объект ajax-помощника.  
	 * @param array|string $keys массив ключей , дополнительных данных. 
	 * Ключи могут быть переданы в строке, разделенные запятой. 
	 */
	protected function prepareAjaxData(\AjaxHelper &$ajaxHelper, $keys=array())
	{
		if(\Yii::app()->request->getPost('dcart-cart-widget', false)) {
 			$ajaxHelper->data['cartItems'] = \DCart\widgets\CartWidget::renderItems(true);
		}
		
		$ajaxHelper->data['miniCartSummary'] = \DCart\widgets\MiniCartWidget::renderSummary(true);
		$ajaxHelper->data['miniCartItems'] = \DCart\widgets\MiniCartWidget::renderItems(true);
		$ajaxHelper->data['cartTotalPrice'] = \Yii::app()->cart->getTotalPrice();
		$ajaxHelper->data['cartHashes'] = \Yii::app()->cart->getHashes();
	}
}