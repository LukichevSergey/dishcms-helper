<?php
/**
 * Основной контроллер раздела администрирования модуля
 *
 */
namespace cdek\modules\admin\controllers;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HAjax;
use cdek\modules\admin\components\BaseController;
use cdek\components\CdekApi;
use cdek\components\helpers\HCdek;
use cdek\models\City;
use cdek\models\Order;
use cdek\models\CityImportForm;

class DefaultController extends BaseController
{
	/**
	 * (non-PHPDoc)
	 * @see BaseController::$viewPathPrefix;
	 */
	public $viewPathPrefix='cdek.modules.admin.views.default.';
    
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
		$t=Y::ct('\cdek\modules\admin\AdminModule.controllers/default');
        
        $cityModel=new City;
        $cdekCityImportFormModel=new CityImportForm;
        if(isset($_POST['cdek_models_CityImportForm'])) {
            $cdekCityImportFormModel->attributes=$_POST['cdek_models_CityImportForm'];
            if($cdekCityImportFormModel->validate()) {
                set_time_limit(0);
                $cdekCityImportFormModel->import();
                Y::setFlash(Y::FLASH_SYSTEM_SUCCESS, 'Импорт городов завершен');
                $this->redirect('index');
            }
        }
        if(isset($_REQUEST['cdek_models_City'])) {
            $cityModel->attributes=$_REQUEST['cdek_models_City'];
            $cityDataProvider=$cityModel->search();
        }
        else {
            $cityDataProvider=$cityModel->getDataProvider();
        }
		
		$this->setPageTitle($t('page.title'));
		$this->breadcrumbs=[$t('page.title')];
		
		$this->render($this->viewPathPrefix.'index', compact('cdekCityImportFormModel', 'cityDataProvider'));
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
            $result=CdekApi::i()->newOrder($order, true);
            $order=Order::model()->wcolumns(['order_id'=>$id])->find();
            if($order->status == Order::STATUS_CDEK) {
                $ajax->data['dispatch_number']=(string)$result->Order["DispatchNumber"];
            }
            $ajax->data['order_status']=$order->status;
            $ajax->data['html_status_css_class']=HCdek::getStatusCssClass($order->status);
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
    
    public function actionUpdateGeoCodes()
    {
        $ajax=HAjax::start();
        
        if($cities=City::model()->findAll(['condition'=>'`ym_point_x` IS NULL', 'limit'=>1000])) {
            foreach($cities as $city) {
                $city->updateGeoCode();
            }
        }
        
        $ajax->end(true);
    }
}
