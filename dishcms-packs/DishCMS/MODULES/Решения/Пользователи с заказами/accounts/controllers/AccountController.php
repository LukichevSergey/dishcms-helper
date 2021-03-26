<?php
namespace accounts\controllers;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HRequest as R;
use common\components\helpers\HModel;
use common\components\helpers\HEvent;
use common\components\helpers\HAjax;
use common\components\helpers\HDb;
use common\components\helpers\HFile;
use accounts\components\helpers\HAccount;
use ecommerce\modules\order\models\Order;
use crud\models\ar\accounts\models\Account;
use crud\models\ar\accounts\models\AccountFavoriteProduct;

/**
 * Личный кабинет
 *
 */
class AccountController extends \accounts\components\base\BaseController
{
    public $layout='//layouts/other';
    
    /**
     * {@inheritDoc}
     * @see \CController::filters()
     */
    public function filters()
    {
        return [
            'accessControl',
            'ajaxOnly +setFavoriteProduct, reorder'
        ];
    }
    
    /**
     * {@inheritDoc}
     * @see \CController::accessRules()
     */
    public function accessRules()
    {
        return [
            ['allow', 'users'=>['@'], 'roles'=>[Account::ROLE_RETAIL_BUYER, Account::ROLE_WHOLESALE_BUYER]],
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
        $this->actionEdit();
        /*
        $account=HAccount::account();
        
        $this->pageTitle=$this->t('action.index.title');
        
        $this->addPageBreadcrumb();
        
        $this->render('index', compact('account'));
        */
    }
    
    /**
     * Action: Редактирование профиля
     */
    public function actionEdit()
    {
        $account=HAccount::account();
        $account->setScenario('edit_profile');
        
        $accountChangePassword=HAccount::account(true);
        $accountChangePassword->setScenario('profile_change_password');
        
        if(HModel::isFormRequest($account)) {
            if(R::post('afid') == 'accounts__change-password-form') {
                $accountChangePassword=HModel::massiveAssignment($accountChangePassword);
                if(HModel::isAjaxValidation(R::post('afid'), true)) {
                    echo \CActiveForm::validate($accountChangePassword);
                    Y::end();
                }
                else {
                    if($accountChangePassword->save()) {
                        Y::setFlash(HAccount::FLASH_SUCCESS, 'Новый пароль успешно установлен');
                        $this->redirect(['index']);
                    }
                    else {
                        Y::setFlash(HAccount::FLASH_FAIL, 'Произошла ошибка. Новый пароль установить не удалось');
                        $this->redirect(['index']);
                    }
                }
            }
            else {
                $account=HModel::massiveAssignment($account);
                if(HModel::isAjaxValidation(R::post('afid'), true)) {
                    echo \CActiveForm::validate($account);
                    Y::end();
                }
                else {
                    if($account->save()) {
                        Y::setFlash(HAccount::FLASH_SUCCESS, 'Изменения успешно сохранены');
                        $this->redirect(['index']);
                    }
                    else {
                        Y::setFlash(HAccount::FLASH_FAIL, 'Произошла ошибка. Изменения не сохранены');
                        $this->redirect(['index']);
                    }
                }
            }            
        }
        
        $accountChangePassword->password='';
        $accountChangePassword->repassword='';
        
        $this->pageTitle=$this->t('action.index.title');
        
        $this->addPageBreadcrumb();
        
        $this->render('index', compact('account', 'accountChangePassword'));
    }
    
    /**
     * Action: добавление/удаление избранного товара
     */
    public function actionSetFavoriteProduct()
    {
        $ajax=HAjax::start();
        
        if($productId=R::post('id')) {
            $tableName=HDb::qt(AccountFavoriteProduct::model()->tableName());
            $params=[
                'accountId'=>HAccount::account()->id,
                'productId'=>$productId
            ];
            
            if($existsId=HDb::queryScalar("SELECT `id` FROM {$tableName} WHERE `account_id`=:accountId AND `product_id`=:productId", $params)) {
                HDb::query("DELETE FROM {$tableName} WHERE `account_id`=:accountId AND `product_id`=:productId", $params);
                $ajax->data['removed']=1;
            }
            else {
                HDb::query("REPLACE INTO {$tableName} (`account_id`, `product_id`, `create_time`) VALUES (:accountId, :productId, NOW())", $params);
                $ajax->data['added']=1;
            }
            
            $ajax->success=true;
        }
        
        $ajax->end();
    }
    
    public function actionReorder()
    {
        $ajax=HAjax::start();
        
        $ajax->data['notexists']=0;
        $ajax->data['count']=0;
        
        if($orderId=R::post('id')) {
            if($order=Order::modelById($orderId)) {
                $orderData=$order->getOrderData();
                foreach($orderData as $item) {
                    if($product=\Product::model()->visibled()->findByPk($item['id']['value'], ['condition'=>'notexist!=1'])) {
                        \Yii::app()->cart->add($product, (int)$item['count']['value']);
                        $ajax->data['count']++;
                    }
                    else {
                        $ajax->data['notexists']++;
                    }
                }
                $ajax->data['cart']=\Yii::app()->cart->getTotalCount();
                $ajax->success=($ajax->data['count'] > 0);
            }
        }
        
        
        $ajax->end();
    }
}
