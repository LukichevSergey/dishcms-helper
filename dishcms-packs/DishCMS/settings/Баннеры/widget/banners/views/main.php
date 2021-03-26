<?php
/** @var BannerWidget $this */
/** @var BannerSettings $banner */
use settings\components\helpers\HSettings;

$banner=HSettings::getById('banners');

if($banner->main_active && $banner->mainImageBehavior->isEnabled() && $banner->mainImageBehavior->exists()) { 
	$content=CHtml::tag('div', ['class'=>'banner__main-image'], $banner->mainImageBehavior->img(750,170,true,['class'=>'img-responsive']));
	if($banner->main_url) {
		$content=CHtml::link($content, $banner->main_url);
	}
	echo CHtml::tag('div', ['class'=>'banner__main'], $content);
} 
?>