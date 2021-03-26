<?php
/** @var \crud\models\ar\extend\points\models\Point $data */
?>
<li class="results__item" data-item="point_item" data-id="<?=$data->id?>" data-point="{lat:<?=$data->lat?>,lon:<?=$data->lon?>}">
	<div class="results__left">
		<h3 class="results__title" data-item="title" style="cursor:pointer"><?= $data->title; ?></h3>
		<p class="results__text"><?= nl2br($data->address); ?></p>
		<a href="javascript:;" data-item="detail-link" style="display:none">подробнее</a>
	</div>
	<div class="results__right">
		<span class="results__distance text" data-item="distance">0 км</span>
		<button class="results__btn btn" data-item="create-route">Построить маршрут</button>
	</div>
</li>
