<?php
namespace ecommerce\ext\sync\actions;

use common\components\helpers\HArray as A;
use common\components\helpers\HAjax;

class Sync extends \CAction 
{
    /**
     * Атрибуты обновления товара
     * @var array
     */
    public $attributes=[
        'title',
        'alias',
        'brand_id',
        'price',
        'old_price',
        'description',
        'hidden',
        'new',
        'hit',
        'sale',
        'notexist',
        'in_carousel',
        'on_shop_index',
        'link_title'
    ];
    
    /**
     * 
     * {@inheritDoc}
     * @see \CAction::run()
     */
    public function run()
    {
        $ajax=HAjax::start();
        
        $sync=new \ecommerce\ext\sync\models\Sync();
        $sync->setImportAttributes($this->attributes);
        
        switch($mode=A::get($_POST, 'mode')) {
            case 'init':
            case 'get':
                if($data=$sync->execute($mode, ['token'=>A::get($_POST, 'token')])) {
                    $ajax->data=$data;
                    $ajax->success=true;
                }
                else {
                    $ajax->errors=$sync->getSyncErrors();
                }
                break;
        }
        
        $ajax->end();
    }
}