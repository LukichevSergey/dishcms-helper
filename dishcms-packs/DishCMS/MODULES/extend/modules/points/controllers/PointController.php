<?php
namespace extend\modules\points\controllers;

use common\components\helpers\HArray as A;
use common\components\helpers\HRequest as R;
use common\components\helpers\HAjax;
use crud\models\ar\extend\points\models\Point;
use ext\uploader\components\helpers\HUploader;

class PointController extends \extend\modules\points\components\base\Controller
{
    /**
     * 
     * {@inheritDoc}
     * @see \CController::filters()
     */
    public function filters()
    {
        return [
            'ajaxOnly +info'
        ];
    }
    
    /**
     * Action: главная страница "Точки продаж"
     * 
     */
    public function actionIndex()
    {
        if(R::post('page')) {
            $_GET['p']=R::post('page');
        }
        
        $searchVar=R::get('_sv', 'q');
        $searchQuery=R::get($searchVar);
        
        $dataProvider=Point::model()->search($searchQuery, 'OR')->published()->getDataProvider([
            'pagination'=>[
                'pageVar'=>'p',
                'pageSize'=>5
            ],
            'sort'=>[
                'defaultOrder'=>'`sort` DESC, `create_time` DESC, `id` DESC'
            ]
        ]);
        
        $dataProvider->getTotalItemCount(true);
        
        if(R::isAjaxRequest()) {
            $this->renderPartial('_point_listview', compact('dataProvider'), false, true);            
        }
        else {
            $this->pageTitle=$this->t('action.index.title');
            
            $this->addPageBreadcrumb();          
            
            $this->render('index', compact('dataProvider'));
        }
    }
    
    /**
     * Action: Получение информации адреса точки продаж
     */
    public function actionInfo()
    {
        $ajax=HAjax::start();
        
        if($id=R::post('id')) {
            if($point=Point::modelById($id)) {
                $ajax->data['title']=$point->title;
                $ajax->data['info']=$point->info;                
                $ajax->data['photos']=[];
                
                if($point->photohash) {
                    $ajax->data['photos']=HUploader::getFiles(\Yii::getPathOfAlias('webroot.images.uploader.extend_points'), false, $point->photohash);
                }
                
                $ajax->success=true;
            }
        }
        
        $ajax->end();
    }
}
