<?php
/**
 * Основной класс для контроллеров модуля администрирования модуля
 *
 */
namespace ecommerce\modules\admin\components;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;

\Yii::import('admin.components.AdminController');

abstract class BaseController extends \AdminController
{
	/**
	 * @var string путь к шаблонам контроллера.
	 */
	public $viewPathPrefix='ecommerce.modules.admin.views.';
	
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
		Y::module('ecommerce')->getModule('admin');
		
		parent::__construct($id, $module);
	}
}