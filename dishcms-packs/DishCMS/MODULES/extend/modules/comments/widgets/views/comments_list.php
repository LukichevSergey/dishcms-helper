<?php
/** @var \extend\modules\comments\widgets\CommentsList $this */
use extend\modules\comments\components\helpers\HComment;

$t=HComment::t();
?>
<?php $this->widget('zii.widgets.CListView', [
    'dataProvider'=>$this->getDataProvider(),
    'itemView'=>$this->itemView,
    'viewData'=>$this->params,
    'enableHistory'=>true,
    'emptyText'=>$t('widgets.commentsList.empty'),
    'itemsTagName'=>'div',
    'itemsCssClass'=>'row extend__comments-items',
    'loadingCssClass'=>'loading-content',
    'template'=>'{items}{pager}',
    'pagerCssClass'=>'pagination',
    'pager'=>[
        'class' => 'DLinkPager',
        'maxButtonCount'=>'5',
        'header'=>'',
        'htmlOptions'=>['class'=>'yiiPager yiiPagerComments'],
    ],
    'afterAjaxUpdate'=>'function(){$("html, body").animate({scrollTop: ($(".extend__comments-items").offset().top - 50)}, 200);}',
]);
?>