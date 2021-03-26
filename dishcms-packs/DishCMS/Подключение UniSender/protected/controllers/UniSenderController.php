<?php
use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HAjax;

require_once dirname(__FILE__) . '/../vendors/php-api-wrapper/UnisenderApi.php';

class UniSenderController extends Controller
{
	/**
	 * (non-PHPdoc)
	 * @see CController::filters()
	 */
	public function filters()
	{
		return A::m(parent::filters(), [
			'ajaxOnly +subscribe',
		]);
	}
	
	public function actionSubscribe()
	{
		$ajax=HAjax::start();
		
		if(empty($_GET['email'])) {
			$ajax->addError('E-Mail обязателен для заполнения', 'email');
		}
		else {
			$uni=new \Unisender\ApiWrapper\UnisenderApi(D::cms('unisender_apikey'));
			$result=json_decode($uni->subscribe([
				'list_ids'=>D::cms('unisender_list_ids'), 
				'fields'=>['email'=>$_GET['email']], 
				'double_optin'=>3
			]), true);
			if(!empty($result['result']['person_id'])) {
				$ajax->success=true;
			}
			else {
				$ajax->addError('Сервис временно недоступен', 'email');
			}
		}
		
		$ajax->end();
	}
}
