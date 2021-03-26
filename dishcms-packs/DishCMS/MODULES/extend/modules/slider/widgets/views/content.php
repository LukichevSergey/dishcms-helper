<?php
/** @var \extend\modules\slider\widgets\Slick $this */

if($this->tag) echo CHtml::openTag($this->tag, $this->tagOptions);
echo CHtml::openTag($this->itemsTagName, $this->itemsOptions);
foreach($this->getSlides() as $slide) {
	if($this->contentTag) echo CHtml::openTag($this->contentTag, $this->contentOptions);
       	if(is_string($this->content)) echo $this->content;
       	elseif(is_callable($this->content)) echo call_user_func_array($this->content, [$slide, $this]);
	if($this->contentTag) echo CHtml::closeTag($this->contentTag);
}
echo CHtml::closeTag($this->itemsTagName);
if($this->tag) echo CHtml::closeTag($this->tag);
?>
