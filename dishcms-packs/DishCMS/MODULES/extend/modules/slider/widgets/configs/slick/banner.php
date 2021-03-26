<?php
/**
 * Конфигурация для Slick слайдера.
 * "Баннер"
 *
 * @var \extend\modules\slider\widgets\Slick $widget
 */

return [
    'less'=>'css/banner.less',
    'tag'=>false,
    'itemsOptions'=>'banner__slider slider-dots',
    'itemOptions'=>'banner__slide',
    'linkOptions'=>'banner__image-link',
    'imageOptions'=>'banner__image',
    'options'=>[
        'autoplay'=>true,
        'autoplaySpeed'=>3000,
        'arrows'=>false,
        'dots'=>true
    ]
];