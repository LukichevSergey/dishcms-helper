<?php
namespace ecommerce\modules\moysklad\modules\admin\controllers;

use ecommerce\modules\moysklad\modules\admin\components\base\Controller;

class ImportController extends Controller
{
    /**
     * {@inheritdoc}
     * @see Controller::$viewPathPrefix
     */
    public $viewPathPrefix='ecommerce.modules.moysklad.modules.admin.views.import.';
    
    /**
     * Action: главная страница импорта из сервиса "Мой Склад"
     */
    public function actionIndex()
    {
        $this->render($this->viewPathPrefix . 'index');
    }
}