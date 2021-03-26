<?php
/**
 * Базовый класс для виджетов администриварония со внешней моделью.
 */
namespace DListBoxAttribute\widgets\admin;

use DListBoxAttribute\widgets\BaseModelWidget;

class AdminModelWidget extends BaseModelWidget
{
	/**
	 * (non-PHPdoc)
	 * @see \CWidget::init()
	 */
	public function init()
	{
		parent::init();
	
		// publish assets
		\AssetHelper::publish(array(
			'path' 	=> (__DIR__ . DIRECTORY_SEPARATOR . 'assets'),
			'js' 	=> array('js/DListBoxAttributeAdminWidget.js')
		));
	}
}