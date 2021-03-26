<?php $this->widget('\reviews\widgets\ReviewsList', ['limit'=>3]); ?>

1) listView
'itemView'=>'webroot.themes.'.\Yii::app()->theme->name.'.views.reviews_widgets_ReviewsList._reviews_list_item',

Скрипт
use common\components\helpers\HYii as Y;
if($dataProvider->getPagination()->getPageCount() > 1) {
	echo \CHtml::link('Больше отзывов', 'javascript:;', ['class'=>'all-reviews', 'data-js'=>'reviews-btn-more', 'data-page'=>2]);
	Y::js(
		'reviews-more', 
		'$(document).on("click", "[data-js=\'reviews-btn-more\']", function(e) {
			e.preventDefault();
			$.post("/reviews/default/getItems", {page: $(e.target).attr("data-page"), limit: '.(int)$dataProvider->getPagination()->getPageSize().'}, function(response){
				if(response.success) {
					$(".reviews__list").append($(response.data.html).find(".reviews__list").html());
					if((+$(e.target).attr("data-page") + 1) > ' . $dataProvider->getPagination()->getPageCount() . ') {
						$(e.target).hide();
					}
					else {
						$(e.target).attr("data-page", +$(e.target).attr("data-page") + 1);
					}
				}
				else { $(e.target).hide(); }
			}, "json");
		});',
		\CClientScript::POS_READY
	);
}

2) reviews\controllers\DefaultController

	public function actionGetItems()
	{
		$ajax=HAjax::start();

		$page=(int)\Yii::app()->request->getPost('page');
		if($page > 1) {
			$limit=(int)\Yii::app()->request->getPost('limit');
			$dataProvider=Review::model()->actived()->getDataProvider(null, ['pageSize'=>$limit]);
			$_GET[$dataProvider->getPagination()->pageVar]=$page;
			$ajax->data['html']=$this->renderPartial(
				'webroot.themes.' . \Yii::app()->theme->name . '.views.reviews_widgets_ReviewsList._reviews_list', 
				compact('dataProvider'), 
				true, 
				true
			);
			$ajax->success=true;
		}

		$ajax->end();
	}
