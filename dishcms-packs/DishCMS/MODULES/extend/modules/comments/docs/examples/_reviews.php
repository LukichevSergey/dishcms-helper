<?php
/** @var \extend\modules\comments\widgets\CommentsList $this */
use extend\modules\comments\components\helpers\HComment;

$t=HComment::t();
?>
<div class="reviews js__reviews-list">
 	<div  class="reviews__header">
        <h2 class="reviews__title">Комментарии</h2>
        <a href="javascript:;" class="reviews__add-review btn">Добавить комментарий</a>
    </div>
    
    <?php $this->widget('zii.widgets.CListView', [
        'dataProvider'=>$this->getDataProvider(),
        'itemView'=>$this->itemView,
        'viewData'=>$this->params,
        'enableHistory'=>true,
        'emptyText'=>$t('widgets.commentsList.empty'),
        'itemsTagName'=>'ul',
        'itemsCssClass'=>'reviews__list',
        'loadingCssClass'=>'loading-content',
        'template'=>'{items}{pager}',
        'pagerCssClass'=>'pagination',
        'pager'=>[
            'class' => 'DLinkPager',
            'maxButtonCount'=>'5',
            'header'=>'',
            'htmlOptions'=>['class'=>'yiiPager yiiPagerComments'],
        ],
        'afterAjaxUpdate'=>'function(){$("html, body").animate({scrollTop: $(".js__reviews-list").offset().top}, 200);}',
    ]);
    ?>
    
    <?php $this->widget('\extend\modules\comments\widgets\AddCommentForm', [
        'model'=>$this->model, 
        'model_id'=>$this->model_id,
        'tagOptions'=>['class'=>'form', 'id'=>'review-form-div'],
        'view'=>'application.views.shop._reviews_form'
    ]); ?>
</div>
<script>$(':radio.star').rating();$('.reviews__add-review').click(function(){$.fancybox.open({'src': '#review-form-div', 'scrolling': 'no', 'titleShow': false, 'onComplete': function(a, b, c) {$('#fancybox-wrap').addClass('formBox');}});});</script>
