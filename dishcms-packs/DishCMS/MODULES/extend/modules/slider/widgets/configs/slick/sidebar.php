<?php
/**
 * Конфигурация для Slick слайдера.
 * "SideBar"
 *
 * @var \extend\modules\slider\widgets\Slick $widget
 */

return [
    'less'=>'css/sidebar.less',
    'tag'=>false,
    'itemsOptions'=>'sidebar-slider slider-dots news-inner__slider',
    'itemOptions'=>'sidebar-slider__slide',
    'linkOptions'=>'sidebar-slider__link',
    'imageOptions'=>'sidebar-slider__image',
    'options'=>[
        'autoplay'=>true,
        'autoplaySpeed'=>1500,
        'arrows'=>false,
        'dots'=>true
    ]
];
