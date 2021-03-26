<?php
class PageViewWidget extends CWidget
{
	/**
	 * @var int page id.
	 */
	public $id;
	
	public function run()
	{
		$page = Page::model()->findByPk($this->id);
		
		ContentDecorator::decorate($page);
		
		if($page) {
			$this->render('default', compact('page'));
		}
	}
}
