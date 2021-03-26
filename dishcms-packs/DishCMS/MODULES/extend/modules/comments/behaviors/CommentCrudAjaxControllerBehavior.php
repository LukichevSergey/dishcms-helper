<?php
namespace extend\modules\comments\behaviors;

use common\components\helpers\HArray as A;
use common\components\helpers\HAjax;
use extend\modules\comments\components\helpers\HComment;
use crud\models\ar\extend\modules\comments\Comment;

class CommentCrudAjaxControllerBehavior extends \CBehavior
{
    public function actionGetParentList($cid)
    {
        $ajax=HAjax::start();
        
        $hash=$cid=A::get($_POST, 'hash');
        
        if(!empty($hash)) {
            if($config=HComment::getConfigByParentHash($hash)) {
                $model=new Comment;
                $model->model=$config['class'];
                $ajax->data=$model->getParentListData();
                $ajax->success=true;
            }
        }
        
        $ajax->end();
    }
    
    public function actionGetModelList($cid)
    {
        $ajax=HAjax::start();
        
        $id=$cid=A::get($_POST, 'id');
        $hash=$cid=A::get($_POST, 'hash');
        
        if(!empty($hash) && !empty($id)) {
            if($config=HComment::getConfigByParentHash($hash)) {
                $model=new Comment;
                $model->model=$config['class'];
                $ajax->data=$model->getModelListData(['scopes'=>['wcolumns'=>[[$config['parent']['attributeParentId']=>$id]]]]);
                $ajax->success=true;
            }
        }
        
        $ajax->end();
    }
}