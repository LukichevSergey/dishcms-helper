<?php
/** @var \crud\widgets\ListWidget $this */
/** @var \CActiveDataProvider $dataProvider */
use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;

$tcl=Y::ct('CommonModule.labels', 'common');
if($dataProvider->getTotalItemCount()):
?><h2>Банки партнеры</h2><?
$this->widget('\common\widgets\listing\SizerListView', A::m([
	'dataProvider'=>$dataProvider,
	'itemView'=>$this->itemView,
	'enableHistory'=>true,
	'sorterHeader'=>$tcl('sort').':',
	'pagerCssClass'=>'pagination',
 	'pager'=>[
 		'class' => 'DLinkPager',
 		'maxButtonCount'=>'5',
 		'header'=>''
 	],
	'loadingCssClass'=>'loading-content',
	'itemsTagName'=>$this->itemsTagName,
	'tagName'=>$this->tag,
	'htmlOptions'=>$this->htmlOptions,
	'emptyText' => $this->emptyText,
	'itemsCssClass'=>$this->itemsCssClass,
	'sortableAttributes'=>false,
	'id'=>'ajaxCrudListView',
	'sizerHeader'=>$tcl('showAt').': ',
	'sizerVariants'=>[15, 30, 60, 120],
	'template'=>'{items}{pager}' // {sizer}{sorter}
], $this->listViewOptions));

endif;
?>
