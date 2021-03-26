<?php
use common\components\helpers\HYii as Y;
?>
<div class="news__col col-4">
	<a href="<?=$data->getPageUrl()?:'javascript:;'?>" class="block block_sized block_bordered news__block">
		<div class="block__image-wrap">
			<?= $data->img($data->getTmbWidth(), $data->getTmbHeight(), true, ['class'=>'block__image']); ?>
		</div>
		<div class="block__content">
			<h3 class="block__title"><?= $data->title; ?></h3>
			<p class="block__text"><?= $data->preview_text; ?></p>
			<date class="block__subtext"><?= Y::formatDateVsRusMonth($data->create_time); ?></date>
		</div>
	</a>
</div>