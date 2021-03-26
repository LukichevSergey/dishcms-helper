<?php
namespace admin\actions;

use common\components\helpers\HYii as Y;
use common\components\helpers\HModel;

class ChangePasswordAction extends \CAction
{
	public function run()
	{
    	$model=HModel::massiveAssignment('\admin\models\ChangePassword', true, true);
    	
    	if(HModel::isFormRequest($model)) {
	    	if($model->validate()) {
	    		if($model->savePassword()) {
	    			Y::setFlash(Y::FLASH_SYSTEM_SUCCESS, 'Пароль был успешно изменен');
	    		}
	    		else {
	    			Y::setFlash(Y::FLASH_SYSTEM_ERROR, 'Произошла ошибка. Пароль не был изменен!');
	    		}
	    		$this->controller->refresh();
	    	}
    	}
    	
    	$this->controller->pageTitle = 'Изменение пароля - '. $this->controller->appName;
    	
        $this->controller->render('change_password',compact('model'));

	}
}