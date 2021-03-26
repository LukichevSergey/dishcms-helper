<?php
/**
 * Настройки магазина
 * 
 */
namespace ecommerce\modules\sberbank\models;

use common\components\helpers\HArray as A;

class SberbankSettings extends \settings\components\base\SettingsModel
{
	/**
	 * Значение токена эквайринга Сбербанка
	 * @var string 
	 */
	public $token;
    
    /**
     * Логин для эквайринга Сбербанка
	 * @var string 
	 */
	public $login;
    
    /**
     * Пароль для эквайринга Сбербанка
	 * @var string 
	 */
	public $password;
    
    /**
     * Значение Мерачанта для эквайринга Сбербанка
	 * @var string 
	 */
	public $merchant;
    
	/**
	 * Система налогообложения
	 * @var integer 
	 */
	public $tax_system;
	
    /**
     * Тестовый режим
	 * @var integer|boolean
	 */
	public $is_test_mode=0;
	
	/**
	 * URL возврата, в случае успешной оплаты
	 * @var string
	 */
	public $success_url;
	
	/**
	 * URL возврата, в случае неуспешной оплаты
	 * @var string
	 */
	public $fail_url;
	
	
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
		return 'sberbank_settings';
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \common\components\base\FormModel::behaviors()
	 */
	public function behaviors()
	{
		return A::m(parent::behaviors(), [
		]);
	}

	/**
	 * (non-PHPdoc)
	 * @see \settings\components\base\SettingsModel::rules()
	 */
	public function rules()
	{
		return $this->getRules([
			['token, login, password, merchant, is_test_mode', 'safe'],
		    ['success_url, fail_url', 'safe'],
		    ['tax_system', 'numerical', 'integerOnly'=>true]
		]);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \settings\components\base\SettingsModel::attributeLabels()
	 */
	public function attributeLabels()
	{
		return $this->getAttributeLabels([
			'token'=>'Токен',
			'login' => 'Логин',
			'password' => 'Пароль',
			'merchant' => 'Мерчант',
		    'tax_system'=>'Система налогообложения',
			'is_test_mode'=>'Тестовый режим',
		    'success_url'=>'URL возврата, в случае успешной оплаты',
		    'fail_url'=>'URL возврата, в случае неуспешной оплаты',
		]);
	}
	
	public function taxSystems()
	{
	    return [
	        // ''=>'-- не указано --',
	        0=>'общая',
	        1=>'упрощённая, доход',
	        2=>'упрощённая, доход минус расход',
	        3=>'единый налог на вменённый доход',
	        4=>'единый сельскохозяйственный налог',
	        5=>'патентная система налогообложения',
	    ];
	}
}
