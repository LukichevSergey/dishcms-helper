<?php
/**
 * Настройки модуля "Аккаунты"
 * 
 */
namespace accounts\models;

class AccountSettings extends \settings\components\base\SettingsModel
{
    public $id=1;
    
    /**
     * Секретный ключ для хэширования
     * @var string
     */
    public $secret_key='';
    
    /**
     * Подтверждать регистрацию
     * @var string
     */
    public $reg_confirm_mode=0;
    
    /**
     * Текст 
     * @var string
     */
    public $reg_done_text='Регистрация успешно завершена';
    
    /**
     * Дополнительный текст в форме регистрации
     * @var string
     */
    public $reg_form_text='';
    
    public $privacy_link;
    public $terms_link;
    
    public $signin_form_text;
    public $restore_form_text;
    public $restore_change_form_text;
    
    public $reg_moderate_mode=0;
    
    /**
     * @var boolean для совместимости со старым виджетом
     * редактора admin.widget.EditWidget.TinyMCE
     */
    public $isNewRecord=false;
    
    /**
     * Для совместимости со старым виджетом
     * редактора admin.widget.EditWidget.TinyMCE
     */
    public function tableName()
    {
        return 'account_settings';
    }    
    
    /**
     * {@inheritDoc}
     * @see \settings\components\base\SettingsModel::rules()
     */
    public function rules()
    {
        return [
            ['secret_key', 'required'],
            ['secret_key', 'length', 'min'=>8],
            ['reg_confirm_mode', 'boolean'],
            ['reg_done_text, reg_form_text', 'safe'],
            ['reg_email_before, reg_email_after', 'safe'],
            ['privacy_link, terms_link', 'safe'],
            ['signin_form_text', 'safe'],
            ['reg_moderate_mode', 'safe'],
            ['restore_form_text, restore_change_form_text', 'safe']
        ];
    }
    
    /**
     * {@inheritDoc}
     * @see \settings\components\base\SettingsModel::attributeLabels()
     */
    public function attributeLabels()
    {
       return [
           'secret_key'=>'Секретный ключ',
           'reg_confirm_mode'=>'Пользователю необходимо подтверждать регистрацию',
           'reg_moderate_mode'=>'Регистрация пользователя подтверждается администратором сайта (режим модерирования)',           
           'reg_done_text'=>'Текст успешного завершения регистрации',
           'reg_form_text'=>'Дополнительный текст в форме регистрации',
           'signin_form_text'=>'Дополнительный текст в форме авторизации',
           'terms_link'=>'URL страницы Terms of Service',
           'privacy_link'=>'URL страницы Privacy Policy',
           'restore_form_text'=>'Дополнительный текст в форме восстановления пароля',
           'restore_change_form_text'=>'Дополнительный текст в форме смены пароля'
       ];
    }
    
    /**
     * {@inheritDoc}
     * @see \CModel::beforeValidate()
     */
    public function beforeValidate()
    {
        $this->secret_key=trim($this->secret_key);
        
        return true;
    }
    
    /**
     * Подтверждение регистрации активировано
     * @return boolean
     */
    public function isRegConfirmMode()
    {
        return false; // ((int)$this->reg_confirm_mode > 0);
    }
    
    /**
     * Модерация регистрации активирована
     * @return boolean
     */
    public function isModerateMode()
    {
        return ((int)$this->reg_moderate_mode > 0);
    }
}