<?php
use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HFile;

$imagesPath=Yii::getPathOfAlias($this->imagesAlias);
$images=HFile::getFiles($imagesPath);
if(count($images) > 0):

Y::js(false,
'$(document).on("click", "#xlsimport_rm_all", function(e) {
    e.preventDefault();
    if(confirm("Подтвердите удаление ВСЕХ изображений из папки изображений импорта ('.$this->imagesUrl.')")) {
        $.post("/cp/xlsimport/removeAllImages", function() {
            window.location.reload()
        });
    }
});', \CClientScript::POS_READY);

Y::js(false,
'$(document).on("click", "[data-js=\'xlsimport-image-rm\']", function(e) {
    e.preventDefault();
    if(confirm("Подтвердите удаление изображения")) {
        $.post("/cp/xlsimport/removeImage", {image: $(e.target).data("filename")}, function() {
			$(e.target).parents(".xlsimport__image:first").remove();
        });
    }
});', \CClientScript::POS_READY)
?>
<h2>Ранее загруженные фотографии</h2>
<div style="margin: 10px">
	<a id="xlsimport_rm_all" class="btn btn-danger" href="javascript:;">Удалить все фотографии</a>
</div>

<div class="xlsimport__images">
<?
foreach($images as $filename):
?><div class="xlsimport__image">
	<a href="<?="{$this->imagesUrl}/{$filename}"?>" target="_blank"><div class="xlsimport__img"><?= CHtml::image("{$this->imagesUrl}/{$filename}"); ?></div></a>
	<div class="xlsimport__filename"><?=$filename?></div>
	<div class="xlsimport__rm"><a data-js="xlsimport-image-rm" data-filename="<?=$filename?>" href="#" class="btn btn-xs btn-danger">удалить</a></div>
</div>
<?
endforeach;
?>
</div>

<style>
.xlsimport__images {
	width: 100%;
}
.xlsimport__image {
    width: 20%;
    height: 250px;
    border: 1px solid #ccc;
    padding: 5px;
	margin: 5px;
	display: inline-block;
}
.xlsimport__img {
    height: 190px;
    border: 1px solid #dfdfdf;
    text-align: center;
}
.xlsimport__img img {
    max-height: 180px;
    max-width: 100%;
}
.xlsimport__filename {
    text-align: center;
    text-transform: uppercase;
    font-size: 0.8em;
    height: 20px;
}
.xlsimport__rm {
    text-align: center;
    font-size: 0.8em;
    height: 20px;
}
</style>
</div>
<?
endif;
?>
