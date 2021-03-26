<?php
namespace ecommerce\modules\exchange\modules\admin\controllers;

use common\components\helpers\HRequest as R;
use common\components\helpers\HFile;

class ExchangeController extends \ecommerce\modules\exchange\modules\admin\components\base\Controller
{
    public $viewPathPrefix='ecommerce.modules.exchange.modules.admin.views.exchange.';
    
    /**
     * Action: Импорт
     */
    public function actionImport()
    {
        if($profile=trim(R::get('profile')) && ($config=trim(R::get('config')))) {
            $iterator="ecommerce.modules.exchange.config.iterators.{$profile}_import.main";
            
            $this->render($this->viewPathPrefix.'import', compact('iterator', 'config'));
            
            return;
        }
        
        R::e404();
    }
    
    /**
     * Action: Экспорт
     */
    public function actionExport()
    {
        if($profile=trim(R::get('profile')) && ($config=trim(R::get('config')))) {
            $iterator="ecommerce.modules.exchange.config.iterators.{$profile}_export.main";
            
            $this->render($this->viewPathPrefix.'export', compact('iterator', 'config'));
            
            return;
        }
        
        R::e404();
    }
    
    /**
     * Action: Загрузка файла экспорта
     */
    public function actionDownloadExportFile()
    {
        if($filename=R::get('filename')) {
            $filename=\Yii::getPathOfAlias('webroot.upload') . "/export_{$filename}";
            if(is_file($filename)) {
                HFile::download($filename, null, false, false, function($filename) {
                    @unlink($filename);
                });
            }
        }
        
        R::e404();
    }
}