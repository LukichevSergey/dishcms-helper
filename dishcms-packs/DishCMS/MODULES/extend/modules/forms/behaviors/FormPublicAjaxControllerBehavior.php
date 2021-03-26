<?php
namespace extend\modules\forms\behaviors;

use common\components\helpers\HRequest as R;
use common\components\helpers\HFile;
use common\components\helpers\HAjax;
use crud\components\helpers\HCrud;
use extend\modules\forms\components\helpers\HForm;
use common\components\helpers\HModel;
use common\components\helpers\HEvent;

class FormPublicAjaxControllerBehavior extends \CBehavior
{
    public function actionUploadFile($cid)
    {
        $ajax=HAjax::start();
        
        if($attribute=R::get('attribute')) {
            if($config=HForm::getConfigByCrudConfigId($cid)) {
                if($config->published) {
                    if($form=HForm::getFormByCrudConfigId($cid)) {
                        $action=new \ext\uploader\actions\UploadFileAction($this->getOwner(), 'uploadFile');
                        $action->path=$config->getTypeParamByAttribute($attribute, 'upload_dir');
                        $action->limit=$config->getTypeParamByAttribute($attribute, 'limit');
                        $action->maxsize=$config->getTypeParamByAttribute($attribute, 'maxsize');
                        $action->extensions=$config->getTypeParamByAttribute($attribute, 'types');
                        $action->run();
                    }
                }
            }
        }
        
        $ajax->end();
    }
    
    public function actionDeleteFile($cid)
    {
        if($attribute=R::get('attribute')) {
            if($config=HForm::getConfigByCrudConfigId($cid)) {
                if($config->published) {
                    if($form=HForm::getFormByCrudConfigId($cid)) {
                        $action=new \ext\uploader\actions\DeleteFileAction($this->getOwner(), 'deleteFile');
                        $action->path=$config->getTypeParamByAttribute($attribute, 'upload_dir');
                        $action->run();
                    }
                }
            }
        }
    }
    
    /**
     * Отправка формы
     * @param string $cid идентификатор CRUD конфигурации формы
     */
    public function actionSend($cid)
    {
        $ajax=HAjax::start();
        
        $ajax->data['afid']=R::get('afid');
        
        if($config=HForm::getConfigByCrudConfigId($cid)) {
            if($config->published) {
                if($form=HForm::getFormByCrudConfigId($cid)) {
                    $form=HModel::massiveAssignment($form, true, true);
                    if($form->validate()) { 
                        $saved=true;
                        if($config->is_save_results) {
                            if($saved=$form->save()) {
                                $ajax->success=true;
                            }
                            else {
                                $ajax->addErrors($form->getErrors());
                            }
                        }
                        
                        if($saved && $config->is_send_mail) {
                            $form->send();
                        }
                        HEvent::raise('onExtendFormsFormAfterActionSend');
                    }
                    else {
                        $ajax->addErrors($form->getErrors());
                    }
                }
            }
        }
        
        $ajax->end();
    }
}