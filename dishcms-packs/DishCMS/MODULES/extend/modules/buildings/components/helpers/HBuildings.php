<?php
namespace extend\modules\buildings\components\helpers;

use \settings\components\helpers\HSettings;

class HBuildings
{
    public static function settings()
    {
        return HSettings::getById('buildings');
    }
}