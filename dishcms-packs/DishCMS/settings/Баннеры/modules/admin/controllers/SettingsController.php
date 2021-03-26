<?php
use common\components\helpers\HYii as Y;
use settings\components\HSettings;

Yii::setPathOfAlias('settings', Yii::getPathOfAlias('application.modules.settings'));
Yii::import('settings.SettingsModule');

class SettingsController extends \settings\modules\admin\controllers\DefaultController
{	
}