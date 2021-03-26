<?php
/**
 * Добавить в $tabs
 * 'Синхронизация'=>['content'=>$this->widget('\ecommerce\ext\sync\widgets\ListnerSettings', compact('model', 'form'), true), 'id'=>'tab-sync']
 */
namespace ecommerce\ext\sync\widgets;

class ServerSettings extends \common\components\base\Widget
{
    public $form;
    public $model;
    
    public $hide=[];
    
    public function run()
    {
        $this->render('ecommerce.ext.sync.widgets.views.server_settings', [
            'form'=>$this->form,
            'model'=>$this->model
        ]);
    }
}
