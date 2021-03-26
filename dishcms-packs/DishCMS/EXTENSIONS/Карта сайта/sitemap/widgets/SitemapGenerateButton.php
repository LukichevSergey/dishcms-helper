<?php

namespace seo\ext\sitemap\widgets;

use common\components\helpers\HHash;

class SitemapGenerateButton extends \common\components\base\Widget
{
    public $view='generate_button';
    public $config='cms';
    public $label='Генерация XML карты сайта';
    public $loadingText='Идет генерация карты сайта...';
    public $htmlOptions=['class'=>'btn btn-warning'];
    public $tagOptions=['class'=>'', 'style'=>'width:80%'];
    public $doneMessage='Генерация карты сайта успешно завершена!';
    public $iterator='seo.ext.sitemap.config.iterators.sitemap.generator';

    /**
     * Получить параметры для итератора.
     *
     * @return void
     */
    public function getData()
    {
        return [
            'config'=>HHash::srEcrypt($this->config, md5($this->iterator))
        ];
    }
}