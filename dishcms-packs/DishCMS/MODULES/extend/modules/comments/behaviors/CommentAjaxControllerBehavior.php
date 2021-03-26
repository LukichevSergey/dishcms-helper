<?php
namespace extend\modules\comments\behaviors;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HAjax;
use common\components\helpers\HModel;
use extend\modules\comments\components\helpers\HComment;

class CommentAjaxControllerBehavior extends \CBehavior
{
    public function actionAdd($cid)
    {
        $ajax=HAjax::start();
        
        $hash=$cid=A::get($_POST, 'hash');
        
        if(!empty($hash)) {
            if($config=HComment::getConfigByHash($hash)) {
                $t=HComment::t();
                
                $model=HModel::massiveAssignment('\crud\models\ar\extend\modules\comments\Comment', true);
                $model->model=$config['class'];
                
                if(!$model->validate()) {
                    echo \CActiveForm::validate($model);
                    Y::end();
                }
                
                $ajax->success=$model->save();
                
                if($ajax->success) {
                    $ajax->data['message']=$t('ajax.add.success');
                }
                else {
                    $ajax->data['message']=$t('ajax.add.fail');
                    $ajax->addErrors($model->getErrors());
                }
            }
        }
        
        $ajax->end();
    }
}