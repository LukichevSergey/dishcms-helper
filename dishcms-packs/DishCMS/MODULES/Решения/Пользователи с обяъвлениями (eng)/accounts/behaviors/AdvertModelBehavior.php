<?php
namespace accounts\behaviors;

use common\components\helpers\HDb;
use accounts\components\helpers\HAccount;
use crud\models\ar\accounts\models\Account;
use crud\models\ar\accounts\models\Advert;
use crud\models\ar\accounts\models\AdvertResponse;

class AdvertModelBehavior extends \CBehavior
{
    /**
     * {@inheritDoc}
     * @see \CBehavior::events()
     */
    public function events()
    {
        return [
            //'onBeforeValidate'=>'beforeValidate',
            'onBeforeSave'=>'beforeSave',
            'onAfterSave'=>'afterSave'
        ];
    }

    /**
     * Правила валидации
     * @see \CActiveRecord::rules()
     * @return []
     */
    public function rules()
    {
        return [
            // add_sale_advert
            [['part_number', 'part_type', 'quantity', 'code', 'category', 'detail_type'], 'required', 'on'=>'add_sale_advert'],
            [['detail_type_value'], 'required', 'on'=>'edit_advert', 'message'=>'Field cannot be blank.'],
            ['quantity', 'numerical', 'integerOnly'=>true, 'min'=>1, 'on'=>'add_sale_advert'],
            ['more_info, detail_type_value', 'safe', 'on'=>'add_sale_advert'],
            
            // add_parts_wanted_advert
            [['part_number', 'part_type', 'quantity', 'code', 'category', 'detail_type'], 'required', 'on'=>'add_parts_wanted_advert'],
            [['detail_type_value'], 'required', 'on'=>'edit_advert', 'message'=>'Field cannot be blank.'],
            ['quantity', 'numerical', 'integerOnly'=>true, 'min'=>1, 'on'=>'add_parts_wanted_advert'],
            ['more_info, detail_type_value', 'safe', 'on'=>'add_parts_wanted_advert'],
            
            // crud filter
            ['id, part_number, account_id, published, account_category, account_region_id, advert_filter_info, type', 'safe', 'on'=>'crud_filter'],
            
            // edit_advert
            [['part_number', 'part_type', 'quantity', 'code', 'category', 'detail_type'], 'required', 'on'=>'edit_advert'],
            [['detail_type_value'], 'required', 'on'=>'edit_advert', 'message'=>'Field cannot be blank.'],
            ['quantity', 'numerical', 'integerOnly'=>true, 'min'=>1, 'on'=>'edit_advert'],
            ['more_info, detail_type_value', 'safe', 'on'=>'edit_advert'],
            
            // crud_update
            [['account_id', 'part_number', 'part_type', 'quantity', 'code', 'category'], 'required', 'on'=>'crud_update'],
            [['detail_type_value'], 'required', 'on'=>'crud_update', 'message'=>'Field cannot be blank.'],
            ['quantity', 'numerical', 'integerOnly'=>true, 'min'=>1, 'on'=>'crud_update'],
            ['type, more_info, detail_type, detail_type_value', 'safe', 'on'=>'crud_update'],
        ];
    }
    
    public function relations()
    {
        return [
            'account'=>[\CActiveRecord::BELONGS_TO, '\crud\models\ar\accounts\models\Account', 'account_id'],
            'respondsCount'=>[\CActiveRecord::STAT, '\crud\models\ar\accounts\models\AdvertResponse', 'advert_id'],
            'emailsCount'=>[\CActiveRecord::STAT, '\crud\models\ar\accounts\models\AdvertEmail', 'advert_id'],
        ];
    }
    
    public function byAccountId($accountId)
    {
        $c=HDb::criteria();
        
        $c->addColumnCondition(['account_id'=>(int)$accountId]);
        $this->owner->getDbCriteria()->mergeWith($c);
        
        return $this->owner;
    }
    
    public function byDetailType($detailType)
    {
        $c=HDb::criteria();
        
        $c->addColumnCondition(['detail_type'=>$detailType]);
        $this->owner->getDbCriteria()->mergeWith($c);
        
        return $this->owner;
    }
    
    public function byType($type)
    {
        $c=HDb::criteria();
        
        $c->addColumnCondition(['type'=>(int)$type]);
        $this->owner->getDbCriteria()->mergeWith($c);
        
        return $this->owner;
    }
    
    public function byActiveAccount()
    {
        $c=HDb::criteria();
        
        $query='SELECT `id` FROM ' . HDb::qt(Account::model()->tableName()) . ' WHERE `published`=1 AND `moderated`=1';
        $ids=HDb::queryColumn($query);
        
        if(!empty($ids)) {
            $c->addInCondition('account_id', $ids);            
        }
        else {
            $c->addCondition('1<>1');
        }
        
        $this->owner->getDbCriteria()->mergeWith($c);
        
        return $this->owner;
    }
    
    public function can($action)
    {
        switch($action) {
            case 'download':
                return ($this->owner->account_id == HAccount::account()->id) || in_array(HAccount::account()->role, [Account::ROLE_AIRLINE_MRO, Account::ROLE_AIRPORT]);
                break;
        }
        
        return false;
    }
    
    public function canById($id, $action)
    {
        if($advert=Advert::model()->resetScope()->published()->findByPk($id)) {
            return $advert->can($action);
        }
        
        return false;
    }
    
    public function hasResponse()
    {
        return (AdvertResponse::model()->countByAttributes(['advert_id'=>$this->owner->id, 'account_id'=>HAccount::account()->id]) > 0);
    }
    
    public function getAdvertLocalFileName($full=false)
    {
        
        if($this->owner->fileBehavior->exists()) {
            $local='';
            if($full) {
                $local.=str_replace('.', '_', $_SERVER['SERVER_NAME']) . '_ad_';
            }
            $local.=$this->owner->id . '_document.' . pathinfo($this->owner->fileBehavior->getFilename(), PATHINFO_EXTENSION);
            
            return $local;
        }
        
        return false;
    }
    
    public function getAdvertFileNameDownloadUrl()
    {
        return '/accounts/account/downloadAdvertFile/' . $this->owner->id;
    }
    
    public function getAdvertFileDownloadLink($htmlOptions=[])
    {
        if($localFileName=$this->getAdvertLocalFileName()) {
            return \CHtml::link($localFileName, $this->getAdvertFileNameDownloadUrl(), $htmlOptions);
        }
        
        return null;
    }
    
    public function getAdvertTitle()
    {
        return 'Advert #' . $this->owner->id;
    }

    public function beforeSave()
    {
    	if(!$this->owner->file) {
    	    $this->owner->file='';
    	}
    }
    
    public function afterSave()
    {
        if(!$this->owner->ignoreSendPublishedEmail) {
            if($this->owner->last_published !== null) {
                if((int)$this->owner->last_published !== (int)$this->owner->published) {
                    if(!$this->owner->published) {
                        \common\components\helpers\HEvent::raise("onAccountAdvertUnpublished", ["advert"=>$this->owner, "account"=>$this->owner->account]);
                    }
                    else {
                        \common\components\helpers\HEvent::raise("onAccountAdvertPublished", ["advert"=>$this->owner, "account"=>$this->owner->account]);
                    }
                }
            }
        }
    }
    
    public function getAdvertDetailTypeList($checkAccountCategory=false)
    {
        if($checkAccountCategory) {
            $category=HAccount::account()->category;
        }
        else {
            $category=$this->owner->account->category;
        }
        $types=[];
        if(in_array($category, [Account::CATEGORY_AIRLINE_MRO, Account::CATEGORY_RESELLER])) {
            $types[Advert::DETAIL_TYPE_AIRCRAFT]='Aircraft';
        }
        
        if(in_array($category, [Account::CATEGORY_AIRPORT, Account::CATEGORY_RESELLER])) {
            $types[Advert::DETAIL_TYPE_EQUIPMENT]='Equipment';
        }
        
        return $types;
    }
    
    public function getDetailTypeLabel()
    {
        switch($this->owner->detail_type) {
            case Advert::DETAIL_TYPE_AIRCRAFT:
                return 'Aircraft';
                break;
            
                
            case Advert::DETAIL_TYPE_EQUIPMENT:
                return 'Equipment';
                break;    
        }
    }
}