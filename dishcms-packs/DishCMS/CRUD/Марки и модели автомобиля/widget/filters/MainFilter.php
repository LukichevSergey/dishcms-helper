<?php
namespace widget\filters;

class MainFilter extends \common\components\base\Widget
{
    public $view='main_filter';
    
    public function run()
    {
        $this->render($this->view, $this->params);
    }
}