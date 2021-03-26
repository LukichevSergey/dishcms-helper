<?php
if($banner=\crud\models\ar\HeaderBanner::getBanner()) {
    echo \CHtml::link(
        \CHtml::image($banner->imageBehavior->getSrc(), $banner->imageBehavior->getAlt(), ['class'=>'product-commercial__image']), 
        $banner->url?:'#!',
        ['class'=>'product-commercial__image-link']
    ); 
}
else {
    echo '';
}
?>
