<?php
/** @var \extend\modules\slider\widgets\BxSlider $this */ 
/** @var boolean $enableInfo отображать блок информации. По умолчанию FALSE. */
/** @var string $infoLinkLabel название ссылки в блоке информации. По умолчанию $t('link.detail'). */
/** @var string $infoLinkOptions атрибуты для тэга ссылки в блоке информации. По умолчанию array('class'=>'slide-link-button'). */
use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;

$t=Y::ct('\extend\modules\slider\SliderModule.widgets/bxslider', 'extend.slider');

echo CHtml::openTag($this->tag, $this->tagOptions);
	echo CHtml::openTag($this->itemsTagName, $this->itemsOptions);
		foreach($this->getSlides() as $slide) {
		    if($img=$this->getSlideImage($slide)) {
				echo CHtml::openTag($this->itemTagName, $this->itemOptions);
					if($this->isLink() && $slide->url) echo CHtml::link($img, $slide->url, $this->linkOptions);
					else echo $img;
					if(isset($enableInfo) && $enableInfo && ($slide->description || $slide->url)):
					?><div class="slide-info"><?
						?><div class="slide-info-head"><p><?=$slide->title?></p></div><?
						?><div class="slide-info-content"><p><?=$slide->description?></p></div><?
 						if($slide->url) {
 							echo CHtml::link(
 								(isset($infoLinkLabel) ? $infoLinkLabel : $t('link.detail')), 
 								$slide->url, 
 								(isset($infoLinkOptions) ? $infoLinkOptions : ['class'=>'slide-link-button'])
 							);
 						}
 					?></div><?
					endif;
				echo CHtml::closeTag($this->itemTagName);
			}
		}
	echo CHtml::closeTag($this->itemsTagName);
echo CHtml::closeTag($this->tag);

?>
