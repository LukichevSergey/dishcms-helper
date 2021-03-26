<?php
?>
<section class="news-inner">
	<div class="news-inner__row row">
		<div class="news-inner__content-col col-md-8">
			<div class="news-inner__content">
				<h1 class="news-inner__title title title_sz_xl"><?= $model->seo_h1?:$model->title; ?></h1>
				<date class="news-inner__date text text_sz_sm text_cl_gray">&nbsp;</date>
				<?= $model->text; ?>
			</div>
			<?php $this->widget('\extend\modules\comments\widgets\CommentsList', [
                'model'=>$model,
			    'model_id'=>$model->id,
                'pageSize'=>5,
                'view'=>'webroot.themes.template.views.crud._comments',
                'itemView'=>'webroot.themes.template.views.crud._comments_item',
			    'dataProviderOptions'=>['pagination'=>['params'=>['cid'=>'news', 'id'=>$model->id]]]
            ]); ?>
		</div>
		<div class="news-inner__images-col col-md-4">
			<div class="news-inner__image-wrap">
				<?= $model->img(350, 350, true, ['class'=>'news-inner__image']); ?>
			</div>
			<?php \extend\modules\slider\components\helpers\HSlider::widget('r-sidebar', ['view'=>'rsidebar', 'config'=>'sidebar']); ?>
		</div>			
	</div>
</section>