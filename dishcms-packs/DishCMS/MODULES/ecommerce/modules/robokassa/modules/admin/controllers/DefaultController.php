<?php
namespace ecommerce\modules\robokassa\modules\admin\controllers;

use common\components\helpers\HRequest as R;
use ecommerce\modules\robokassa\modules\admin\components\base\Controller;
use ecommerce\modules\robokassa\components\helpers\HRobokassa;

class DefaultController extends Controller
{
    public $layout='column2';
    public $viewPathPrefix='ecommerce.modules.robokassa.modules.admin.views.default.';

    public function actionIndex()
    {
        if(!HRobokassa::isInstalled()) {
            return $this->actionInstall();
        }
        
        $this->render($this->viewPathPrefix . 'index');
    }

    public function actionInstall()
    {
        if(HRobokassa::isInstalled() || !\D::isDevMode()) {
            R::e404();
        }

        $this->render($this->viewPathPrefix . 'install');
    }
}