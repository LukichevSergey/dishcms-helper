<?php
/**
 * Модуль "Аккаунты"
 *
 */
class AccountsModule extends \common\components\base\WebModule
{
    /**
     * Переопределение страницы авторизации
     * @var string
     */
    public $loginUrl='/signin';
    
    /**
     * {@inheritDoc}
     * @see \CModule::init()
     */
    public function init()
    {
        parent::init();
        
        if($this->loginUrl && (\Yii::app()->user instanceof \CWebUser)) {
            \Yii::app()->user->loginUrl=$this->loginUrl;
        }
    }
}