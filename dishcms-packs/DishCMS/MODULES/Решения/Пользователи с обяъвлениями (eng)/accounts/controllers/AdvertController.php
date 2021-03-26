<?php
namespace accounts\controllers;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HRequest as R;
use common\components\helpers\HModel;
use common\components\helpers\HEvent;
use common\components\helpers\HAjax;
use common\components\helpers\HFile;
use accounts\components\helpers\HAccount;
use accounts\components\helpers\HAccountEmail;
use crud\models\ar\accounts\models\Account;
use crud\models\ar\accounts\models\Advert;
use crud\models\ar\accounts\models\AdvertResponse;

/**
 * Личный кабинет
 *
 */
class AdvertController extends \accounts\components\base\BaseController
{
    /**
     * {@inheritDoc}
     * @see \CController::filters()
     */
    public function filters()
    {
        return [
            'accessControl',
            'ajaxOnly +checkDownload, respond'
        ];
    }
    
    /**
     * {@inheritDoc}
     * @see \CController::accessRules()
     */
    public function accessRules()
    {
        return [
            ['allow', 'users'=>['@'], 'roles'=>[Account::ROLE_AIRLINE_MRO, Account::ROLE_AIRPORT, Account::ROLE_RESELLER]],
            ['deny', 'users'=>['*']]
        ];
    }
    
    /**
     * (non-PHPdoc)
     * @see \CController::behaviors()
     */
    public function behaviors()
    {
        return A::m(parent::behaviors(), [
            
        ]);
    }
    
    /**
     * Action: Главная страница личного кабинета
     */
    public function actionIndex()
    {
        $this->actionAirlineMROPartsWanted();
    }
    
    /**
     * Action: Airline/MRO, Parts Wanted
     */
    public function actionAirlineMROPartsWanted()
    {
        $this->pageTitle='Airline/MRO, Parts Wanted';
        
        $this->addPageBreadcrumb();
        
        $this->render('index', [
            'type'=>Advert::TYPE_AIRLINE_PARTS_WANTED,
            'dataProvider'=>$this->getAdvertDataProvider(Advert::TYPE_PARTS_WANTED, Advert::DETAIL_TYPE_AIRCRAFT)
        ]);
    }
    /**
     * Action: Airline/MRO, For Sale
     */
    public function actionAirlineMROForSale()
    {
        $this->pageTitle='Airline/MRO, For Sale';
        
        $this->addPageBreadcrumb();
        
        $this->render('index', [
            'type'=>Advert::TYPE_AIRLINE_FOR_SALE,
            'dataProvider'=>$this->getAdvertDataProvider(Advert::TYPE_SALE, Advert::DETAIL_TYPE_AIRCRAFT)
        ]);
    }
    /**
     * Action: Airports, Equipment Wanted
     */
    public function actionAirportsEquipmentWanted()
    {
        $this->pageTitle='Airports, Equipment Wanted';
        
        $this->addPageBreadcrumb();
        
        $this->render('index', [
            'type'=>Advert::TYPE_AIRPORT_EQUIPMENT_WANTED,
            'dataProvider'=>$this->getAdvertDataProvider(Advert::TYPE_PARTS_WANTED, Advert::DETAIL_TYPE_EQUIPMENT)
        ]);
    }
    /**
     * Action: Airports, For Sale
     */
    public function actionAirportsForSale()
    {
        $this->pageTitle='Airports, For Sale';
        
        $this->addPageBreadcrumb();
        
        $this->render('index', [
            'type'=>Advert::TYPE_AIRPORT_FOR_SALE,
            'dataProvider'=>$this->getAdvertDataProvider(Advert::TYPE_SALE, Advert::DETAIL_TYPE_EQUIPMENT)
        ]);
    }
    
    public function actionCheckDownload($id)
    {
        $ajax=HAjax::start();
        
        $ajax->success=Advert::model()->canById($id, 'download');
        
        $ajax->end();
    }
    
    public function actionRespond($id)
    {
        $ajax=HAjax::start();
        
        $ajax->data['msg']=HAccountEmail::settings()->admin_advert_response_notify_not_responded;
        
        if($advert=Advert::model()->findByPk($id)) {
            $respond=new AdvertResponse;
            $respond->account_id=HAccount::account()->id;
            $respond->advert_id=$advert->id;
            if($respond->save()) {
                HEvent::raise('onAccountAdvertResponse', ['advert'=>$advert, 'account'=>HAccount::account()]);
                $ajax->data['msg']=HAccountEmail::settings()->admin_advert_response_notify_responded;
                $ajax->success=true;
            }
        }
        
        $ajax->end();
    }
    
    public function actionDownload($id)
    {
        if($advert=Advert::model()->published()->findByPk($id)) {
            if($advert->can('download')) {
                if($advert->fileBehavior->exists()) {
                    HFile::download($advert->fileBehavior->getFilename(true), null, true, false, null, $advert->getAdvertLocalFileName(true));
                }
            }
        }
        
        R::e404();
    }
    
    protected function getAdvertDataProvider($advertType, $detailType)
    {
        return Advert::model()
            ->published()
            ->byActiveAccount()
            ->byDetailType($detailType)
            ->byType($advertType)
            ->getDataProvider([
                'pagination'=>[
                    'pageSize'=>20
                ],
                'sort'=>[
                    'defaultOrder'=>'IF(ISNULL(`update_time`), `create_time`, `update_time`) DESC, `create_time` DESC, `id` DESC'                    
                ]
            ]);
    }
}
