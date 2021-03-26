<?php
/**
 * Виджет сообщения "Только для региона"
 */
namespace extend\modules\regions\widgets;

use extend\modules\regions\components\helpers\HRegion;

class OnlyForRegion extends \common\components\base\Widget
{
	/**
	 * @var integer|false идентификатор региона. 
	 * По умолчанию (false) текущий.
	 */
	public $regionId = false;
	
	/**
	 * @var string категория сообщения для \Yii::t().
	 */
	public $messageCategory = '\extend\modules\regions\RegionsModule.widgets/onlyForRegion';
	
	/**
	 * @var string текст сообщения для \Yii::t(). 
	 * В тексте может использоваться переменная "{title}" - название региона.
	 * По умолчанию (false) заданный по умолчанию в данном виджете.
	 */
	public $messageText = 'default_message';
	
	/**
	 * {@inheritDoc}
	 * @see \common\components\base\Widget::$tagOptions
	 */
	public $tagOptions=['class'=>'alert alert-info'];
	
	/**
	 * {@inheritDoc}
	 * @see \common\components\base\Widget::$view
	 */
	public $view = 'only_for_region';
	
	/**
	 * Получить название региона.
	 * @return string
	 */
	public function getRegionTitle()
	{
		return HRegion::i()->getTitle($this->regionId);		
	}
}