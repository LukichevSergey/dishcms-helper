<?php
/**
 * Виджет "Результаты опроса"
 */
namespace extend\modules\polls\widgets;

class PollStat extends \common\components\base\Widget
{
    /**
     * @var integer идентификатор опроса
     */
    public $id;
    
    /**
     * {@inheritDoc}
     * @see \common\components\base\Widget::$view
     */
    public $view='pollstat_default';    
    
}