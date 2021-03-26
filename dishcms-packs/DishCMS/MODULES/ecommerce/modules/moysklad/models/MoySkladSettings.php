<?php
/**
 * Настройки модуля "Аккаунты"
 * 
 */
namespace ecommerce\modules\moysklad\models;

class MoySkladSettings extends \settings\components\base\SettingsModel
{
    public $id=1;
    
    /**
     * Логин для подключения к сервису "Мой Склад"
     * @var string
     */
    public $login='';
    
    /**
     * Пароль для подключения к сервису "Мой Склад"
     * @var string
     */
    public $password='';
    
    /**
     * Дополнительный ключ шифрования для обмена с сервисом "Мой Склад" 
     * @var string
     */
    public $secure='';
    
    /**
     * Количество обрабатываемых записей за один шаг 
     * при обмене с сервисом "Мой Склад".
     * По умолчанию 100
     * @var integer
     */
    public $limit=100;
    
    /**
     * Нормализовывать структуру категорий при обновлении 
     * @var integer
     */
    public $normalize_categories=0;
    
    /**
     * Тип цены товара
     * @var string
     */
    public $price_type='';
    
    /**
     * Организация
     * @var string
     */
    public $organization='';
    
    public $store;
    
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
        return 'moysklad_settings';
    }    
    
    /**
     * {@inheritDoc}
     * @see \settings\components\base\SettingsModel::rules()
     */
    public function rules()
    {
        return [
            ['normalize_categories', 'boolean'],
            ['limit', 'numerical', 'integerOnly'=>true],
            ['login, password, secure, price_type, store, organization', 'safe']
        ];
    }
    
    /**
     * {@inheritDoc}
     * @see \settings\components\base\SettingsModel::attributeLabels()
     */
    public function attributeLabels()
    {
       return [
           'login'=>'Логин для подключения к сервису "Мой Склад"',
           'password'=>'Пароль для подключения к сервису "Мой Склад"',
           'secure'=>'Дополнительный ключ шифрования для обмена с сервисом "Мой Склад"',
           'limit'=>'Количество обрабатываемых записей за один шаг при обмене с сервисом "Мой Склад"',
           'normalize_categories'=>'Перемещать категории на сайте в соответствии со структурой в сервисе "Мой Склад" при обмене',
           'price_type'=>'Тип цены товара',
           'store'=>'Склад',
           'organization'=>'Организация в которую будут выгружаться заказы'
       ];
    }
    
    /**
     * {@inheritDoc}
     * @see \CModel::beforeValidate()
     */
    public function beforeValidate()
    {
        $this->login=trim($this->login);
        $this->password=trim($this->password);
        
        return true;
    }
}