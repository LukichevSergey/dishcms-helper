<?php
namespace accounts\controllers;

use common\components\helpers\HArray as A;
use common\components\helpers\HRequest as R;
use common\components\helpers\HModel;
use common\components\helpers\HEvent;
use accounts\components\helpers\HAccount;
use crud\models\ar\accounts\models\Account;

/**
 * Контроллер регистрация
 *
 */
class RegController extends \accounts\components\base\BaseController
{
    /**
     * (non-PHPdoc)
     * @see \CController::behaviors()
     */
    public function behaviors()
    {
        return A::m(parent::behaviors(), [
            'arControllerBehavior'=>['class'=>'\common\behaviors\ARControllerBehavior']
        ]);
    }
    
    /**
     * Action: Регистрация нового пользователя
     *
     */
    public function actionIndex()
    {
        $account=HModel::massiveAssignment('\crud\models\ar\accounts\models\Account', true, true, 'registration');
        $account->role=Account::ROLE_DEFAULT;
        
        $this->save($account, [], R::post('afid'), [
            'afterSave'=>function() use ($account) {
                HEvent::raise('onAccountRegSuccessed', compact('account'));
                
                if(HAccount::settings()->isRegConfirmMode()) {
                    $this->redirect(['/accounts/reg/confirm', 'c'=>HAccount::getCryptCode($account)]);
                }
                else {
                    $this->redirect('/accounts/reg/done');
                }
            }
        ]);
        
        $this->pageTitle=$this->t('action.index.title');
        
        $this->addHomeBreadcrumb(false);
        $this->addPageBreadcrumb();
        
        $this->render('index', compact('account'));
    }
    
    /**
     * Action: Регистрация успешно завершена
     *
     */
    public function actionDone()
    {
        $this->pageTitle=$this->t('action.done.title');
        
        $this->addHomeBreadcrumb();
        $this->addPageBreadcrumb();
        
        $this->render('done');
    }
    
    /**
     * Action: Подтверждение регистрации
     *
     */
    public function actionConfirm()
    {        
        $account=HAccount::getAccountByCryptCode(R::get('c'), true);
        $account->clearCheckCode(true);
        
        $event=HEvent::raise('onAccountRegConfirm', [
            'account'=>$account,
            'email.subject'=>$this->t('action.confirm.email.subject', ['{sitename}'=>\Yii::app()->name])
        ]);        
        $view=A::get($event->params, 'sended', false) ? 'confirm' : 'confirm_failed';
        
        $this->pageTitle=$this->t('action.confirm.title');
    
        $this->addHomeBreadcrumb(false);
        $this->addPageBreadcrumb();
            
        $this->render($view, compact('account'));
    }
    
    /**
     * Action: Активация аккаунта
     */
    public function actionActivation()
    {
        if($code=R::get('code')) {
            $view='activation_failed';
            if($account=Account::model()->wcolumns(['confirm_code'=>$code, 'last_confirm_code'=>$code], 'OR')->find()) {
                if($account->isActivated()) {
                    $view='activation_activated';
                }
                elseif($account->activate($code)) {
                    $view='activation';    
                }
            }
            
            $this->pageTitle=$this->t('action.activation.title');
            
            $this->addHomeBreadcrumb();
            $this->addPageBreadcrumb();
            
            $this->render($view, compact('account'));
        }
        else {
            R::e404();
        }
    }
}