<?php
/**
protected/models/Page.php
	'activeBehavior'=>[
		'class'=>'\common\ext\active\behaviors\ActiveBehavior',
		'attribute'=>'on_index_page',
		'attributeLabel'=>'Отображать на главной',
		'scopeActivlyName'=>'onIndexPage'
	],

protected/modules/admin/views/page/_form_general.php
use common\components\helpers\HArray as A;
$this->widget('\common\widgets\form\CheckboxField', A::m(compact('form', 'model'), ['attribute'=>'on_index_page']));

$this->widget('widget.page.PageListWidget');
*/
class PageListWidget extends CWidget
{
	public function run()
	{
		$dataProvider = Page::model()->onIndexPage()->getDataProvider(['select'=>'id, title']);

		$this->render('page_list', compact('dataProvider'));
	}
}
