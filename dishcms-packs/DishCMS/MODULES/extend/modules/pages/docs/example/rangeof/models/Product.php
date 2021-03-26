<?php
use common\components\helpers\HArray as A;
use common\components\helpers\HCache;

class Product extends \common\components\base\ActiveRecord
{

    /**
     * (non-PHPdoc)
     * @see \CModel::behaviors()
     */
    public function behaviors()
    {
        return A::m(parent::behaviors(), array(
        	...
        	'rangeofBehavior'=>[
        		'class'=>'\common\behaviors\ARAttributeListBehavior',
        		'attribute'=>'rangeof',
        		'rel'=>'\Rangeof',
        		'searchAttribute'=>\Rangeof::model()->getSefAttribute(),
        		'cacheTime'=>HCache::YEAR
        	],
        ));
    }
}
