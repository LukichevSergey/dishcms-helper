<?php
namespace admin\models;

use common\components\helpers\HArray as A;

class ChangePassword extends \common\components\base\FormModel
{
	public $password;
	public $repassword;
	
	public function rules()
	{
		return [
			['password, repassword', 'required'],
			['password, repassword', 'length', 'min'=>6],
			['password', 'match', 'pattern'=>'/^[a-z0-9]+$/i', 'message'=>'Разрешены только латинские буквы и цифры'],
			['repassword', 'compare', 'compareAttribute'=>'password', 'message'=>'Пароли не совпадают']
		];
	}
	
	public function attributeLabels()
	{
		return [
			'password'=>'Пароль',
			'repassword'=>'Повторите пароль',
		];
	}
	
	public function savePassword()
	{
		$filename=\Yii::getPathOfAlias('application.config') . '/users.php';
		$content='<?php ' . "\nreturn " . A::toPHPString(['admin'=>\CPasswordHelper::hashPassword($this->password)]) . ';';
		
		return file_put_contents($filename, $content);
	}
}