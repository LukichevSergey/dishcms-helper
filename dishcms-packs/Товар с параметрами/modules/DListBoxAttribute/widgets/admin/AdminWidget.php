<?php
/**
 * Базовый класс для виджетов управления моделями аттрибута со списком значений.
 * 
 * @author BorisDrevetsky
 *
 * @use \YiiHelper;
 * @use \AssetsHelper;
 */
namespace DListBoxAttribute\widgets\admin;

use \AttributeHelper as A;
use DListBoxAttribute\models\DListBoxAttribute;
use DListBoxAttribute\widgets\BaseWidget;

abstract class AdminWidget extends BaseWidget
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