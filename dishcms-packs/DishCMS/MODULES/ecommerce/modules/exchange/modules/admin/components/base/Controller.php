<?php
namespace ecommerce\modules\exchange\modules\admin\components\base;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;

\Yii::import('admin.components.AdminController');

class Controller extends \AdminController
{
    /**
     * @var string путь к шаблонам контроллера.
     */
    public $viewPathPrefix='ecommerce.modules.exchange.modules.admin.views.';
    
    /**
     * (non-PHPdoc)
     * @see \CController::behaviors()
     */
    public function behaviors()
    {
        return A::m(parent::behaviors(), [
            'arControllerBehavior'=>['class'=>'\common\behaviors\ARControllerBehavior']
        ]);
    }
    
    /**
     * (non-PHPDoc)
     * @see \CController::__construct()
     */
    public function __construct($id, $module=null)
    {
        Y::module('ecommerce.exchange.admin');
        
        parent::__construct($id, $module);
    }    
}