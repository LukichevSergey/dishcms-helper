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
    public $reg_confirm_mode=true;
    
    /**
     * Текст 
     * @var string
     */
    public $reg_done_text='Регистрация успешно завершена';
    
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
        return 'shop_settings';
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
            ['reg_done_text', 'safe']
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
           'reg_done_text'=>'Текст успешного завершения регистрации'
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
        return ((int)$this->reg_confirm_mode > 0);
    }
}