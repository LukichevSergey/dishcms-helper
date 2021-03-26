<?php
/**
 * Раздел администрирования.
 * Виджет добавления новой модели аттрибута со списком значений.
 * 
 * @author BorisDrevetsky
 *
 */
namespace DListBoxAttribute\widgets\admin;

use DListBoxAttribute\widgets\admin\AdminWidget;

class CreateWidget extends AdminWidget
{
	/**
	 * (non-PHPdoc)
	 * @see AdminWidget::$defaultTitle
	 */
	protected $defaultTitle = 'Новое значение';
	
	/**
	 * (non-PHPdoc)
	 * @see \CWidget::run()
	 */
	public function run()
	{
		$model = $this->getModel('insert');
		
		if(\Yii::app()->request->isPostRequest) {
			$model->attributes = \Yii::app()->request->getPost(\YiiHelper::slash2_(get_class($model))); 
			if($model->save()) {
				\Yii::app()->user->setFlash('success', 'Значение успешно добавлено');
				\Yii::app()->request->redirect('/cp/dListBoxAttribute/' . strtolower($this->attribute));
			}
		}
		
		$this->render('create', compact('model'));
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \DListBoxAttribute\widgets\admin\AdminWidget::getTitle()
	 */
	public function getTitle()
	{
		return parent::getTitle('listTitle') . ': ' . $this->defaultTitle;
	}
}