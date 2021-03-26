<?php
namespace accounts\controllers;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HRequest as R;
use common\components\helpers\HModel;
use common\components\helpers\HEvent;
use common\components\helpers\HAjax;
use accounts\components\helpers\HAccount;
use crud\models\ar\accounts\models\Account;
use crud\models\ar\accounts\models\Advert;
use common\components\helpers\HFile;

/**
 * Личный кабинет
 *
 */
class AccountController extends \accounts\components\base\BaseController
{
    public $layout='accounts.views.layouts.account';
    
    /**
     * {@inheritDoc}
     * @see \CController::filters()
     */
    public function filters()
    {
        return [
            'accessControl',
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
        $account=HAccount::account();
        
        $this->pageTitle=$this->t('action.index.title');
        
        $this->addPageBreadcrumb();
        
        $this->render('index', compact('account'));
    }
    
    /**
     * Action: Редактирование профиля
     */
    public function actionEdit()
    {
        $account=HAccount::account();
        $account->setScenario('edit_profile');
        
        if(HModel::isFormRequest($account)) {
            $account=HModel::massiveAssignment($account);
            if(HModel::isAjaxValidation(R::post('afid'), true)) {
                echo \CActiveForm::validate($account);
                Y::end();
            }
            else {
                if(!empty($_POST['account_delete_company_logo']) && $account->companyLogoBehavior->exists()) {
                    $account->companyLogoBehavior->delete();
                }
                
                if($account->save()) {
                    Y::setFlash(HAccount::FLASH_SUCCESS, 'Changes saved successfully');
                    $this->redirect(['edit']);
                }
                else {
                    Y::setFlash(HAccount::FLASH_FAIL, 'An error has occurred. Failed to save changes');
                    $this->redirect(['edit']);
                }
            }
        }
        
        $this->render('edit', compact('account'));
    }
    
    /**
     * Action: Редактирование информации о банке
     */
    public function actionEditBankInformation()
    {
        $account=HAccount::account();
        $account->setScenario('edit_bank_information');
        
        if(HModel::isFormRequest($account)) {
            $lastBankInfo=$account->bankInfoBehavior->get();
            $account=HModel::massiveAssignment($account);
            
            if(md5(json_encode($lastBankInfo)) != md5(json_encode($account->bank_info))) {
                $account->save(true, ['bank_info']);
            }
            
            Y::setFlash(HAccount::FLASH_SUCCESS, 'Changes saved successfully');
            $this->redirect(['editBankInformation']);
        }
        
        $this->render('edit_bank_information', compact('account'));
    }
    
    /**
     * Action: Добавить объявление о продаже
     */
    public function actionAddSaleAdvert()
    {
        $advert=HModel::massiveAssignment('\crud\models\ar\accounts\models\Advert', true, true, 'add_sale_advert');
        $advert->setScenario('add_sale_advert');
        $advert->account_id=HAccount::account()->id;
        $advert->type=Advert::TYPE_SALE;
        
        if(HModel::isFormRequest($advert)) {
            if(HModel::isAjaxValidation(R::post('afid'), true)) {
                echo \CActiveForm::validate($advert);
                Y::end();
            }
            else {
                if(!empty($_POST['advert_delete_file']) && $advert->fileBehavior->exists()) {
                    $advert->fileBehavior->delete();
                }
                
                if($advert->save()) {
                    Y::setFlash(HAccount::FLASH_SUCCESS, 'Your ad has been added.<br/>After checking by the moderator, it will be published.<br/>You can find your ad in the "<a href="/accounts/account/adverts">My Adverts</a>" section');
                    
                    HEvent::raise('onAccountNewAdvert', compact('advert'));
                    
                    $this->redirect(['addSaleAdvert']);
                }
                elseif($advert->getError('file_file')) {
                    Y::setFlash(HAccount::FLASH_FAIL, 'Only XLSX document allowed for publication.');
                }
                else {
                    Y::setFlash(HAccount::FLASH_FAIL, 'An error has occurred. Your ad has not been added.');
                    $this->redirect(['addSaleAdvert']);
                }
            }
        }
        
        $this->render('add_sale_advert', compact('advert'));
    }
    
    /**
     * Action: Добавить объявление о покупке
     */
    public function actionAddPartsWantedAdvert()
    {
        $advert=HModel::massiveAssignment('\crud\models\ar\accounts\models\Advert', true, true, 'add_parts_wanted_advert');
        $advert->setScenario('add_parts_wanted_advert');
        $advert->account_id=HAccount::account()->id;
        $advert->type=Advert::TYPE_PARTS_WANTED;
        
        if(HModel::isFormRequest($advert)) {
            if(HModel::isAjaxValidation(R::post('afid'), true)) {
                echo \CActiveForm::validate($advert);
                Y::end();
            }
            else {
                if(!empty($_POST['advert_delete_file']) && $advert->fileBehavior->exists()) {
                    $advert->fileBehavior->delete();
                }
                
                if($advert->save()) {
                    Y::setFlash(HAccount::FLASH_SUCCESS, 'Your ad has been added.<br/>After checking by the moderator, it will be published.<br/>You can find your ad in the "<a href="/accounts/account/adverts">My Adverts</a>" section');
                    
                    HEvent::raise('onAccountNewAdvert', compact('advert'));
                    
                    $this->redirect(['addPartsWantedAdvert']);
                }
                elseif($advert->getError('file_file')) {
                    Y::setFlash(HAccount::FLASH_FAIL, 'Only XLSX document allowed for publication.');
                }
                else {
                    Y::setFlash(HAccount::FLASH_FAIL, 'An error has occurred. Your ad has not been added.');
                    $this->redirect(['addPartsWantedAdvert']);
                }
            }
        }
        
        $this->render('add_parts_wanted_advert', compact('advert'));
    }
    
    /**
     * Action: Список объявлений пользователя
     */
    public function actionAdverts()
    {
        $dataProvider=Advert::model()->byAccountId(HAccount::account()->id)->getDataProvider([
            'pagination'=>['pageSize'=>20],
            'sort'=>['defaultOrder'=>'`id` DESC']
        ]);
        
        $this->render('adverts', compact('dataProvider'));
    }
    
    public function actionDownloadAdvertFile($id)
    {
        if($advert=Advert::model()->byAccountId(HAccount::account()->id)->findByPk($id)) {
            if($advert->fileBehavior->exists()) {
                HFile::download($advert->fileBehavior->getFilename(true), null, true, false, null, $advert->getAdvertLocalFileName());
            }
        }
        
        R::e404();
    }
    
    public function actionAdvertEdit($id)
    {
        if($advert=Advert::model()->byAccountId(HAccount::account()->id)->findByPk($id)) {
            $advert->last_published=$advert->published;
            $advert->last_file=$advert->file;
            if($advert->fileBehavior->exists()) {
                $lastFileHash=hash_file('md5', $advert->fileBehavior->getFilename(true));
            }
            $saveAttributes=['part_number', 'part_type', 'quantity', 'code', 'more_info', 'detail_type', 'detail_type_value', 'category'];
            $hash=md5(json_encode($advert->getAttributes($saveAttributes), JSON_UNESCAPED_UNICODE));
            $advert->setScenario('edit_advert');
            if(HModel::isFormRequest($advert)) {
                $advert->unsetAttributes($saveAttributes);
                $advert=HModel::massiveAssignment($advert, true, true, 'edit_advert');
                $allowDetailTypes=$advert->getAdvertDetailTypeList(true);
                if(empty($allowDetailTypes[$advert->detail_type])) {
                    $advert->detail_type='';
                }
                if(HModel::isAjaxValidation(R::post('afid'), true)) {
                    echo \CActiveForm::validate($advert);
                    Y::end();
                }
                else {
                    if(!empty($_POST['advert_delete_file']) && $advert->fileBehavior->exists()) {
                        $advert->fileBehavior->delete();
                    }
                    
                    $hash2=md5(json_encode($advert->getAttributes($saveAttributes), JSON_UNESCAPED_UNICODE));
                    if($hash !== $hash2) {
                        $advert->published=0;
                    }
                    
                    $advert->ignoreSendPublishedEmail=true;
                    if($advert->save()) {
                        if($advert->fileBehavior->exists()) {
                            $fileHash=hash_file('md5', $advert->fileBehavior->getFilename(true));
                        }
                        
                        if(!$advert->fileBehavior->exists() || ($lastFileHash != $fileHash)) {
                            if($advert->published) {
                                $advert->published=0;
                                $advert->update(['published']);
                            }
                        }
                        
                        Y::setFlash(HAccount::FLASH_SUCCESS, 'Changes saved successfully.<br/>After checking by the moderator, it will be published.<br/>You can see the status of your ad in the "<a href="/accounts/account/adverts">My Adverts</a>" section');
                        
                        if(!$advert->published && ((int)$advert->last_published !== (int)$advert->published)) {
                            HEvent::raise('onAccountAdvertEdit', compact('advert'));
                        }
                        
                        $this->redirect(['advertEdit', 'id'=>$advert->id]);
                    }
                    elseif($advert->getError('file_file')) {
                        Y::setFlash(HAccount::FLASH_FAIL, 'Only XLSX document allowed for publication.');
                    }
                    else {
                        Y::setFlash(HAccount::FLASH_FAIL, 'An error has occurred. Your ad has not been added.');
                        $this->redirect(['advertEdit', 'id'=>$advert->id]);
                    }
                }
            }
            
            $this->render('edit_advert', compact('advert'));
        }
    }
}
