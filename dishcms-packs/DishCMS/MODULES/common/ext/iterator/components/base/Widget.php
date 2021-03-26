<?php
namespace common\ext\iterator\components\base;

use common\components\helpers\HYii as Y;

class Widget extends \common\components\base\Widget
{
    /**
     * Публикация основных ресурсов расширения
     */
    protected function publishMainAssets()
    {
        Y::publish([
            'path'=>\Yii::getPathOfAlias('common.ext.iterator.assets'),
            'js'=>'js/iterator.js'
        ]);
    }
}