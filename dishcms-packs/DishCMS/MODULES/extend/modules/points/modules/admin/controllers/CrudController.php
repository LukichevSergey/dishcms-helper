<?php
namespace extend\modules\points\modules\admin\controllers;

use common\components\helpers\HArray as A;

class CrudController extends \extend\modules\points\modules\admin\components\base\Controller
{
    /**
     * 
     * {@inheritDoc}
     * @see \CController::actions()
     */
    public function actions()
    {
        return A::m(parent::actions(), [
            'uploadFile'=>[
                'class'=>'\ext\uploader\actions\UploadFileAction', 
                'extensions'=>'jpg,png,jpeg',
                'path'=>'webroot.images.uploader.extend_points'
            ],
            'deleteFile'=>[
                'class'=>'\ext\uploader\actions\DeleteFileAction', 
                'maxtime'=>315360000,
                'path'=>'webroot.images.uploader.extend_points'
            ],
            'clearFiles'=>[
                'class'=>'\ext\uploader\actions\ClearAction',
                'models'=>['\crud\models\ar\extend\points\models\Point'=>'photohash'],
                'path'=>'webroot.images.uploader.extend_points'
            ]            
        ]);
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \AdminController::filters()
     */
    public function filters()
    {
        return A::m(parent::actions(), [
            'ajaxOnly +uploadFile, deleteFile'
        ]);
    }    
}