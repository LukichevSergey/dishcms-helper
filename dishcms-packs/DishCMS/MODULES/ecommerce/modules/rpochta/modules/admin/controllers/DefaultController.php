<?php
/**
 * Основной контроллер раздела администрирования модуля
 *
 */
namespace rpochta\modules\admin\controllers;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HAjax;
use rpochta\modules\admin\components\BaseController;
use rpochta\components\RPochtaApi;
use rpochta\components\helpers\HRPochta;
use rpochta\models\Order;

class DefaultController extends BaseController
{
	/**
	 * (non-PHPDoc)
	 * @see BaseController::$viewPathPrefix;
	 */
	public $viewPathPrefix='rpochta.modules.admin.views.default.';
	
    public function filters()
    {
        return A::m(parent::filters(), [
            'ajaxOnly +newOrder, orderView, deleteOrder'
        ]);
    }
    
	/**
	 * Action: Главная страница.
	 */
	public function actionIndex()
	{	
		$t=Y::ct('\rpochta\modules\admin\AdminModule.controllers/default');
        
        $this->setPageTitle($t('page.title'));
		$this->breadcrumbs=[$t('page.title')];
		
		$this->render($this->viewPathPrefix.'index');
	}
    
    public function actionOrderView($id)
    {
        if($order=Order::model()->wcolumns(['order_id'=>$id])->find()) {
            $this->layout=false;
            $this->render('order_view', compact('order'));
        }
        else {
            echo 'Данные не найдены';
        }
    }
    
    public function actionNewOrder($id)
    {
        $ajax=HAjax::start();
        
        if($order=Order::model()->wcolumns(['order_id'=>$id])->find()) {
            $ajax->data=RPochtaApi::i()->newOrder($order, true);
            $order=Order::model()->wcolumns(['order_id'=>$id])->find();
            $ajax->data['order_status']=$order->status;
            $ajax->data['html_status_css_class']=HRPochta::getStatusCssClass($order->status);
            $ajax->data['html_status_label']=$order->statusLabels($order->status);            
            $ajax->success=true;
        }
        else {
            echo 'Данные не найдены';
        }
        
        $ajax->end();
    }
    
    public function actionDeleteOrder($id)
    {
        $ajax=HAjax::start();
        
        if(Order::model()->deleteByPk($id) > 0) {
            $ajax->success=true;
        }
        else {
            echo 'Заказ не найден';
        }
        
        $ajax->end();
    }    
}
