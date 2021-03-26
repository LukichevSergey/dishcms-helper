<?php
namespace accounts\modules\admin\controllers;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HRequest as R;
use common\components\helpers\HAjax;
use common\components\helpers\HEvent;
use common\ext\email\components\helpers\HEmail;
use accounts\components\helpers\HAccount;
use crud\models\ar\accounts\models\Account;
use crud\models\ar\accounts\models\Advert;
use crud\models\ar\accounts\models\AdvertEmail;
use accounts\components\helpers\HAccountEmail;

class DefaultController extends \accounts\modules\admin\components\base\Controller
{
    public $viewPathPrefix='accounts.modules.admin.views.default.';
    
    /**
     * 
     * {@inheritDoc}
     * @see \AdminController::filters()
     */
    public function filters()
    {
        return A::m(parent::filters(), [
            'ajaxOnly +confirmRegistration, viewEmailTemplate, viewAdvertEmailBody, getAdvertCounts, getAccountCounts'
        ]);
    }
    
    /**
     * Подтверждение регистрации
     */
    public function actionConfirmRegistration()
    {
        $ajax=HAjax::start();
        
        if($id=R::post('id')) {
            if($account=Account::model()->findByPk($id)) {
                $account->moderated=1;
                $account->published=1;
                if($account->save()) {
                    $event=HEvent::raise('onAccountActivatedSuccessed', compact('account'));
                    $ajax->data['sended']=$event->getParam('sended');
                    $ajax->success=true;
                }
            }
        }
        
        $ajax->end();
    }
    
    /**
     * Предпросмотр шаблона письма
     */
    public function actionViewEmailTemplate()
    {
        $ajax=HAjax::start();
        
        $accountId=(int)R::post('id');
        $templateId=R::post('tpl');
        if($accountId && $templateId) {
            if(HAccountEmail::isActiveTemplate($templateId)) {
                if($account=Account::model()->findByPk($accountId)) {
                    $templateParams=compact('account');
                    
                    $html='<strong style="font-family:monospace">' 
                        . HAccountEmail::processShortCodes($templateId, HAccountEmail::getSubject($templateId), $templateParams) 
                        . '</strong><hr/>' 
                        . $this->renderPartial(
                            HAccountEmail::getEmailTemplate($templateId), 
                            ['content'=>HAccountEmail::processShortCodes($templateId, HAccountEmail::getBody($templateId), $templateParams)], 
                            true
                        );
                    
                    $ajax->data['html']=$html; 
                    
                    $ajax->success=true;
                }
            }
        }
        
        $ajax->end();
    }
    
    /**
     * Получение кол-ва объявлений для пользователей
     */
    public function actionGetAccountCounts()
    {
        $ajax=HAjax::start();
        
        $ajax->data['acc']=[];
        
        $ids=R::post('ids');
        if(!empty($ids) && is_array($ids)) {
            $ids=array_map('intval', $ids);
            if(!empty($ids)) {
                $c=new \CDbCriteria();
                $c->select='id';
                $c->addInCondition('id', $ids);
                if($accounts=Account::model()->findAll($c)) {
                    foreach($accounts as $acc) {
                        $ajax->data['acc'][$acc->id]=[
                            'ordersCount'=>(int)$acc->ordersCount,
                        ];
                    }
                    $ajax->success=true;
                }
            }
        }
        
        $ajax->end();
    }
}