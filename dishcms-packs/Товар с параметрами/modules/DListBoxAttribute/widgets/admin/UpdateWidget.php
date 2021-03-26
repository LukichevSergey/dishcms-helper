<?php
/**
 * Раздел администрирования.
 * Виджет редактирования модели аттрибута со списком значений.
 *
 * @author BorisDrevetsky
 *
 */
namespace DListBoxAttribute\widgets\admin;

use DListBoxAttribute\widgets\admin\AdminWidget;

class UpdateWidget extends AdminWidget
{
	/**
	 * Id модели аттрибута со списком значений.
	 * @var integer
	 */
	public $id;
	
	/**
	 * (non-PHPdoc)
	 * @see AdminWidget::$defaultTitle
	 */
	protected $defaultTitle = 'Редактирование значения';
	
	/**
	 * (non-PHPdoc)
	 * @see \CWidget::run()
	 */
	public function run()
	{
		$model = $this->getModel('update', $this->id);
		
		if(\Yii::app()->request->isPostRequest) {
			$model->attributes = \Yii::app()->request->getPost(\YiiHelper::slash2_(get_class($model)));
			if($model->save()) {
				\Yii::app()->user->setFlash('success', 'Изменения успешно сохранены');
				\Yii::app()->request->redirect(\Yii::app()->createUrl('/dListBoxAttribute/' . strtolower($this->attribute)));
			}
		}		
		
		$this->render('update', compact('model'));
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