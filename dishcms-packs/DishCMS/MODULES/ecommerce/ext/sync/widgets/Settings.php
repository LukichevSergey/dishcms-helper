<?php
/**
 * Добавить в $tabs
 * 'Синхронизация'=>['content'=>$this->widget('\ecommerce\ext\sync\widgets\Settings', compact('model', 'form'), true), 'id'=>'tab-sync']
 */
namespace ecommerce\ext\sync\widgets;

class Settings extends \common\components\base\Widget
{
    public $form;
    public $model;
    
    public $view='ecommerce.ext.sync.widgets.views.settings';
    
    public function run()
    {
        $this->render($this->view, [
            'form'=>$this->form,
            'model'=>$this->model
        ]);
    }
}