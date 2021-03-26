<?php
/** @var \ext\uploader\widgets\FileList $this */
$files=$this->getFiles();
?>
<? if($this->tag) echo \CHtml::openTag($this->tag, $this->tagOptions); ?>
<? foreach($files as $fileUrl): if($this->isImageFile($fileUrl)): ?>
	<div class="question__files-item"><a class="image-full" rel="group-<?=$this->hash?>" href="<?=$fileUrl?>"><?= \CHtml::image($fileUrl); ?></a></div>
<? endif; endforeach; ?>
<? if($this->tag) echo \CHtml::closeTag($this->tag); ?>
<style>.question__files-item{display:inline-block;}.question__files-item img{max-height:100px;max-width:100px;}</style>
