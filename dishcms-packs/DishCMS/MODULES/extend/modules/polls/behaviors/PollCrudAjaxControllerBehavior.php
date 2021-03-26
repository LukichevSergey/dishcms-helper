<?php
namespace extend\modules\polls\behaviors;

use common\components\helpers\HArray as A;
use common\components\helpers\HAjax;
use crud\components\helpers\HCrud;
use crud\components\helpers\HCrudForm;
use crud\models\ar\extend\modules\polls\models\Poll;

class PollCrudAjaxControllerBehavior extends \CBehavior
{
    public function actionGetQuestionForm($cid)
    {
        $ajax=HAjax::start();
        
        $idx=A::get($_REQUEST, 'idx', 0);
        
        $qcid='extend_polls_questions';
        $form=new \CActiveForm;
        $model=new \crud\models\ar\extend\modules\polls\models\Question;
        $html=HCrudForm::getHtmlFields(
            $qcid, 
            HCrud::param($qcid, 'crud.ajax.form.attributes', []),
            $model, 
            $form, 
            $this->owner
        );
        
        $html=preg_replace('#(name="crud_models_ar_extend_modules_polls_models_Question)#', "\\1[{$idx}]", $html);
        
        $ajax->data['html']=$html;
        $ajax->success=true;
        
        $ajax->end();
    }
    
    public function actionUpdateStats($cid)
    {
        $ajax=HAjax::start();
        
        if($pollId=A::get($_REQUEST, 'id')) {
            if($poll=Poll::modelById($pollId)) {
                $ajax->success=$poll->updateStats();
            }
        }
        
        $ajax->end();
    }
}
