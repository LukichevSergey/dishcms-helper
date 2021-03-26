<?php
/**
 * Слушатель на стороне основного сервера
 * Добавить действие в контроллер:
 * 'sync'=>'\ecommerce\ext\sync\actions\Listner'
 */
namespace ecommerce\ext\sync\actions;

use common\components\helpers\HArray as A;
use common\components\helpers\HAjax;
use settings\components\helpers\HSettings;

class Listner extends \CAction
{
    public function run()
    {
        $ajax=HAjax::start();
        
        if(empty($_POST['token'])) {
            $ajax->addError('Не передан токен синхронизации');
        }
        else {
            $settings=HSettings::getById('shop');
            if($_POST['token'] !== $settings->sync_token) {
                $ajax->addError('Не верный токен синхронизации');
            }
            else {
                
            }
        }
        
        $ajax->end();
    }
}
