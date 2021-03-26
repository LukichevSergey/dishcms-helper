<?php
/**
 * Модель. Области применения.
 */
use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;

class Rangeof extends \extend\modules\pages\models\Page
{
	/**
	 * (non-PHPdoc)
	 * @see \extend\modules\pages\models\Page::tableName()
	 */
	public function tableName()
	{
		return 'rangeof';
	}
	
	public function behaviors()
	{
		$t=Y::ct('models/rangeof');
		return A::m(parent::behaviors(), [
			'displayIndexPageBehavior'=>[
				'class'=>'\common\ext\active\behaviors\ActiveBehavior',
				'attribute'=>'display_index_page',
				'attributeLabel'=>$t('label.display_index_page'),
				'scopeActivlyName'=>'displayOnIndexPage',
				'scopeActivedName'=>false,
				'scopeNotActivlyName'=>false,
				'addColumn'=>false
			],
		]);
	}
}