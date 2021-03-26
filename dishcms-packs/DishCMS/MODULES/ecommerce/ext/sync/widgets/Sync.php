<?php
namespace ecommerce\ext\sync\widgets;

use common\components\helpers\HYii as Y;

class Sync extends \common\components\base\Widget
{
    public $syncUrl='/admin/shop/sync';
    
    /**
     * {@inheritDoc}
     * @see \common\components\base\Widget::$view
     */
    public $view='sync_button';
    
    /**
     * {@inheritDoc}
     * @see \CWidget::init()
     */
    public function init()
    {
        parent::init();
        
        $this->publish(true, false);
        
        Y::js(
            false, 
            ';ecommerce_ext_sync_widgets_Sync.init(' . \CJavaScript::jsonEncode(['url'=>$this->syncUrl]) . ');', 
            \CClientScript::POS_READY
        );
    }
    
    /**
     * {@inheritDoc}
     * @see \common\components\base\Widget::run()
     */
    public function run()
    {
        $this->render($this->view, $this->params);
    }
}