<?php 
namespace ecommerce\modules\sberbank\components\helpers;

use settings\components\helpers\HSettings;
use ecommerce\modules\sberbank\components\Api;

class HSberbank
{
    public static function settings()
    {
        return HSettings::getById('sberbank');
    }
    
    public static function api()
    {
        return Api::i();
    }
}