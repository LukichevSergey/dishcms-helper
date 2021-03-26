<?php
namespace extend\modules\forms\behaviors;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HEvent;
use common\ext\email\components\helpers\HEmail;
use extend\modules\forms\components\types;
use extend\modules\forms\components\helpers\HForm;
use ext\uploader\components\helpers\HUploader;
use common\components\helpers\HFile;

class FormModelBehavior extends \CBehavior
{
    /**
     * 
     * {@inheritDoc}
     * @see \CBehavior::events()
     */
    public function events()
    {
        return [
            'onBeforeSave'=>'beforeSave'
        ];
    }
    
    /**
     * Связи
     * @return []
     */
    public function relations()
    {
        return [
            
        ];
    }
    
    /**
     * Получить конфигурацию формы
     * @return \crud\models\ar\extend\modules\forms\models\Config|NULL
     */
    public function getConfig()
    {
        return HForm::getConfigByCrudConfigId($this->owner->getCrudId());
    }
    
    /**
     * Event: onBeforeSave
     */
    public function beforeSave()
    {
        if($config=$this->getConfig()) {
            $fields=$config->getFields();
            
            foreach($fields as $field) {
                $attribute=$this->owner->getAttributeName($field['name']);
                if($this->owner->$attribute === null) {
                    $this->owner->$attribute=$config->getFieldOption($field, 'default');
                }
            }
            
            return true;
        }
        
        return false;
    }
    
    public function getAttributeValueFormatted($attribute)
    {
        $value=null;
        
        $config=$this->getConfig();
        if($field=$config->getFieldByAttribute($attribute)) {
            switch(A::rget($field, 'type.id')) {
                case 'rel':
                    if($this->owner->$attribute) {
                        $relModel=$config->getTypeParam($field, 'model');
                        $relModelAttributeId=$config->getTypeParam($field, 'model_id');
                        $relModelAttributeValue=$config->getTypeParam($field, 'model_value');
                        if(!empty($relModel) && !empty($relModelAttributeId) && !empty($relModelAttributeValue) && class_exists($relModel)) {
                            if($rel=$relModel::model()->findByAttributes([$relModelAttributeId=>$this->owner->$attribute])) {
                                $value=$rel->$relModelAttributeValue;
                            }
                        }
                    }
                    break;
                    
                case 'datalist':
                    $items=types\Datalist::getTypeData($config->getTypeParams($field));
                    $value=A::get($items, $this->owner->$attribute);
                    break;
                    
                case 'file':
                    $value='';
                    if($this->owner->$attribute) {
                        $path=\Yii::getPathOfAlias($config->getTypeParam($field,'upload_dir','webroot.files.uploader'));
                        $files=HUploader::getFiles($path, true, $this->owner->$attribute);
                        if(!empty($files)) {
                            foreach($files as $filename) {
                                $value.=\CHtml::link(basename($filename), Y::createAbsoluteUrl(HFile::pathToUrl($filename))) 
                                    . '<br/>';
                            }
                        }
                    }
                    break;
                    
                case 'date':
                    $value=date_create_from_format("Y-m-d", $this->owner->$attribute)->format("d.m.Y");
                    break;                    
            }
        }
        
        if($value === null) {
            $value=$this->owner->getAttribute($attribute);
        }
        
        return $value;
    }
    
    /**
     * Отправка почтового уведомеления
     */
    public function send()
    {
        $config=$this->getConfig();
        if($config->is_send_mail) {
            $emailConfig=HEmail::config();
            $emailConfig['ishtml']=true;
            
            $to=preg_replace('/\s+/', '', $config->email_to ?: \D::cms('email'));
            $toAddresses=explode(',', $to);
            $emailConfig['address']['to']=[HEmail::normalizeAddress(array_shift($toAddresses))];
            if(!empty($toAddresses)) {
                $emailConfig['address']['cc']=$toAddresses;
            }
            
            $subject='Новое сообщение с сайта ' . \D::cms('sitename', $_SERVER['SERVER_NAME']);
            $emailConfig['subject']=$config->email_subject ?: $subject;
            
            $bodyView=$config->email_view ?: 'extend.modules.forms.views._email.admin_form_send';
            
            if(HEmail::send($emailConfig, ['model'=>$this->owner, 'subject'=>$subject], $bodyView)) {
                HEvent::raise('onExtendFormsFormAfterSendMail', ['form'=>$this->owner]);
            }
        }
    }
}