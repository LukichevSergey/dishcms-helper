<?php
namespace extend\modules\pages\widgets;

use common\components\helpers\HArray as A;

class ListWidget extends \crud\widgets\ListWidget
{
	/**
	 * @var boolean|NULL только опубликованные.
	 * Может быть передано (FALSE) - только неопубликованные.
	 * Может быть передано (NULL) - отображать все элементы.
	 * По умолчанию (TRUE) - только опубликованные.
	 */
	public $published=true;
	
	/**
	 * (non-PHPdoc)
	 * @see \CWidget::init()
	 */
	public function init()
	{
		if($this->published) {
			A::rset($this->options, 'criteria.scopes', 'published', true, 1, '.', true);
		}
		
		parent::init();	
	}
}