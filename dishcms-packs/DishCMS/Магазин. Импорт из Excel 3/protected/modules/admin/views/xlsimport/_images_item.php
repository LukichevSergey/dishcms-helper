<div class="xlsimport__image">
	<a href="<?="{$imagesBaseUrl}/{$data}"?>" target="_blank"><div class="xlsimport__img"><?= CHtml::image("{$imagesBaseUrl}/{$data}"); ?></div></a>
	<div class="xlsimport__filename"><?=$data?></div>
	<div class="xlsimport__rm"><a data-js="xlsimport-image-rm" data-filename="<?=$data?>" href="#" class="btn btn-xs btn-danger">удалить</a></div>
</div>