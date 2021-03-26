<section class="news">	
	<div class="news__container container">
    <?php $this->widget('zii.widgets.CListView', [
        'dataProvider'=>\crud\models\ar\NewsPage::model()->published()->previewColumns()->getDataProvider([
            'criteria'=>['order'=>'`sort` DESC, `create_time` DESC, `id` DESC'],
            'pagination'=>['pageSize'=>6, 'pageVar'=>'p', 'params'=>['cid'=>'news']]
        ]),
        'itemView'=>'//crud/news_listview_item',
        'enableHistory'=>true,
        'emptyText'=>'Новостей нет',
        'itemsTagName'=>'div',
        'itemsCssClass'=>'news__row row',
        'loadingCssClass'=>'loading-content',
        'template'=>'{items}{pager}',
        'pagerCssClass'=>'pagination',
        'pager'=>[
            'class' => 'DLinkPager',
            'maxButtonCount'=>'5',
            'header'=>'',
            'htmlOptions'=>['class'=>'yiiPager yiiPagerNews', 'style'=>'margin-top:10px']            
        ],
        'afterAjaxUpdate'=>'function(){$("html, body").animate({scrollTop: ($(".news__container").offset().top - 50)}, 200);}',
    ]);
    ?>
	</div>
</section>