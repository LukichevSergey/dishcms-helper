<?php
namespace rpochta\controllers;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HRequest as R;
use common\components\helpers\HAjax;

use rpochta\components\RPochtaApi;

class ApiController extends \Controller
{
    public function actionIndex()
    {
        $ajax=HAjax::start();
        /*
        $method=R::rget('m');
        if(!$method || !method_exists(RPochtaApi::i(), $method)) {
            $ajax->addError('Не задан метод');
            $ajax->end();
        }
        
        $ajax->data=RPochtaApi::i()->$method();
        $ajax->success=true;
          var_dump($ajax->data);
        /**/
        $ajax->end();
    }
}
