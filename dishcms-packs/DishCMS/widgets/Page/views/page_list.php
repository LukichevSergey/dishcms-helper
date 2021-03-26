<?php
use common\components\helpers\HHtml;

if($dataProvider->getTotalItemCount()):
?>
<div class="sitebar-block">
	<section>
		<div class="sitebar">
			<?php foreach($dataProvider->getData() as $data): 
				if($data->mainImg) $src=$data->mainImg->getUrl();
				else $src=HHtml::phSrc(['w'=>300, 'h'=>260]);
				?><div class="sitebar-item">
					<a href="<?= Yii::app()->createUrl('site/page', ['id' => $data->id]); ?>">
						<div class="sitebar-images"><img src="<?= $src; ?>" alt="" /></div>
						<div class="sitebar-description">
							<p><?= $data->title; ?></p>
						</div>
					</a>
				</div><?php
				endforeach;
			?>
		</div>
	</section>
</div>
<?php
endif;
?>