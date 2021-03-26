<?php
/** @var \ext\uploader\widgets\FileList $this */
$files=$this->getFiles();
?>
<? if($this->tag) echo \CHtml::openTag($this->tag, $this->tagOptions); ?>
<strong><?=$this->header?></strong><br/>
<? foreach($files as $fileUrl): ?>
	<div class="uploader__file-item">
		<? if($this->isImageFile($fileUrl)): ?>
			<div class="uploader__file-image"><a href="<?=$fileUrl?>" target="_blank"><?= \CHtml::image($fileUrl); ?></a></div>
		<? else: ?>
			<div class="uploader__file-file"><a href="<?=$fileUrl?>" target="_blank"><?= basename($fileUrl); ?></a></div>
		<? endif; ?>
		<? if($this->deleteUrl): ?>
			<i data-src="<?=$fileUrl?>" class="js-file-delete-<?=$this->hash?> fas fa-times-circle" title="Удалить">X</i>
		<? endif; ?>
	</div>
<? endforeach; ?>
<? if($this->tag) echo \CHtml::closeTag($this->tag); ?>
<style>
.uploader__file-item{margin:5px 2px 2px;vertical-align:top;display:inline-block;position:relative;}.uploader__file-file{display:block;}
.uploader__file-image{display:inline-block;}.uploader__file-image img{max-width:100px;max-height:100px;}
.js-file-delete-<?=$this->hash?>{cursor:pointer;position:absolute;color:#000;font-size:10px;font-style:normal;border:1px solid #000;border-radius:50%;padding:2px 5px;background:rgba(0, 0, 0, 0.2);right:0;top:-10px;}
.js-file-delete-<?=$this->hash?>:hover{color: #fff;background: #000;}	
</style>
<?if($this->deleteUrl):?>
<script>$(document).on("click", ".js-file-delete-<?=$this->hash?>",function(e){e.stopImmediatePropagation();if(confirm("Подтвердите удаление файла")){$.post("<?= $this->deleteUrl; ?>", {filename: $(e.target).data("src")}, 
function(response){if(response.success){$(e.target).parent().remove();}else{alert("Повторите попытку удаления файла позже");}},"json");}return false;});</script>
<?endif?>
