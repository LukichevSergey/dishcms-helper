<?php
/**
 * Раздел администрирования.
 * Виджет списка моделей аттрибута со списком значений.
 *
 * @author BorisDrevetsky
 *
 */
namespace DListBoxAttribute\widgets\admin;

use DListBoxAttribute\widgets\admin\AdminWidget;

class ListWidget extends AdminWidget
{
	/**
	 * (non-PHPdoc)
	 * @see AdminWidget::$defaultTitle
	 */
	protected $defaultTitle = 'Список значений';
	
	/**
	 * (non-PHPdoc)
	 * @see \CWidget::run()
	 */
	public function run()
	{
		$dataProvider = new \CActiveDataProvider($this->modelClass); 
		
		$this->render('index', compact('dataProvider'));
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \DListBoxAttribute\widgets\admin\AdminWidget::getTitle()
	 */
	public function getTitle()
	{
		return parent::getTitle('listTitle');
	}
}