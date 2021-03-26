<?php

namespace subscribe\controllers;

use subscribe\models\Subscribe;
use subscribe\models\Messages;

class AdminController extends \AdminController
{
	public $viewPath = 'subscribe.views.admin.';

    public function actionIndex()
    {
		$dataProvider=new \CActiveDataProvider('\subscribe\models\Subscribe');
		
		$this->breadcrumbs=$this->getModuleBreadcrumb();
		$this->breadcrumbs['Подписчики']=['subscribe/index'];
		
		$this->render($this->viewPath.'index',array(
		    'dataProvider'=>$dataProvider,
		));
    }

    public function actionDelete($id, $class = null)
    {
        $model = $this->loadModel($id, $class);
        if($model->delete()) \Yii::app()->user->setFlash("deleted", 'true');
		else \Yii::app()->user->setFlash("not_deleted", 'false');
		$this->redirect(['subscribe/index']);
    }

	public function actionEdit()
	{	
		$model = new Messages();
		
		if (isset($_POST['subscribe_models_Messages'])) {
            $model->attributes = $_POST['subscribe_models_Messages'];
            if ($model->save()) {
            	$this->redirect(['subscribe/list']);
            	\Yii::app()->end();
            }
        }
        
        $this->breadcrumbs=$this->getModuleBreadcrumb();
        $this->breadcrumbs['Сообщения']=['subscribe/list'];
        $this->breadcrumbs['Новое сообщение']=['subscribe/edit'];
        
		$this->render($this->viewPath.'edit', array('model'=>$model));
	}

	public function actionList()
	{
		$dataProvider=new \CActiveDataProvider('\subscribe\models\Messages');
		
		$this->breadcrumbs=$this->getModuleBreadcrumb();
		$this->breadcrumbs['Сообщения']=['subscribe/list'];
		
		$this->render($this->viewPath.'list', array(
		    'dataProvider'=>$dataProvider,
		));
	}
	//очищает данные сессии
	public function clearSession()
	{
		unset(\Yii::app()->session['send_info']);
		unset(\Yii::app()->session['result']);
	}
	
	public function actionClear()
	{
		unset(\Yii::app()->session['send_info']);
		unset(\Yii::app()->session['result']);
		$this->redirect(['subscribe/list']);
	}

	public function actionActive()
	{
		$target = Subscribe::model()->findByPk($_GET['id']);
		if($target->active)  $target->active = 0;
		else $target->active = 1;
		
		if($target->save()) $this->redirect(['subscribe/index']);
	}

	public function actionSend($id)
	{
		$message = Messages::model()->findByPk($id);

		if (isset(\Yii::app()->session['result']) ) {
			$send_info = \Yii::app()->session['send_info'];
			$result = \Yii::app()->session['result'];
			$this->render($this->viewPath.'send_info', compact('id', 'result', 'send_info'));
		}
		else{
			$result = $this->send( $message );
			\Yii::app()->session['send_info'] = $message->attributes;
			\Yii::app()->session['result'] = $result;
			$this->refresh();
		}
	}

	public function Send( $message )
	{
		$result = array();
		//заводим PHPMailer
		$mail = new \PHPMailer;

		$mail->XMailer = "why you see this text?";
		$message = $message->attributes;		

		if ($message['theme'] == null) return $result = array('r_message' => 'Не задана тема сообщения');
		//С какого адреса и от кого отправлено
		if ($message['from'] == null) $message['from'] = 'noreply@'.$_SERVER['SERVER_NAME'];
		if ($message['from_name'] == null) $message['from'] = 'noreply';
		$mail->From = $message['from'];
		$mail->FromName = $message['from_name'];
		$criteria = new \CDbCriteria;
		$criteria->condition='active=1';
		$target=Subscribe::model()->findAll($criteria);
		$emails = array();
		if ($target == null) return $result['status'] = false;
		//Тема письма, содержимое, и содержимое strip_tags если у клиента нет HTML поддержки
		$mail->Subject = $message['theme'];
		$mail->Body    = $message['message'];
		$mail->AltBody = strip_tags($message['message']);		
		
		$isFile=false;
				
		foreach ($target as $key => $targ) {
		$result['email_count'] = $key;
		$emails[$key] = $targ->email;
			
			if ($isFile) $mail->addAttachment('files/gost/'.$message['file']);
			$mail->addAddress( $targ->email );
			
			if(!$mail->send()) {
				$result['email'][] = $targ->email.'- Ошибка: '.$mail->ErrorInfo;
			} else {
			    $result['email'][] = $targ->email.' - отправлено';
			    $result['status'] = true;
			}
			$mail->ClearAddresses();
			$mail->ClearAttachments();
			
		}

		return $result;
	}


	public function actionUpdate($id)
	{	
        $model = $this->loadModel($id);
		if (isset($_POST['subscribe_models_Messages']))
		{
			$model->attributes = $_POST['subscribe_models_Messages'];
			if ($model->save()) {
				$this->redirect(['subscribe/list']);
            }
		}
		
		$this->breadcrumbs=$this->getModuleBreadcrumb();
		$this->breadcrumbs['Сообщения']=['subscribe/list'];
		$this->breadcrumbs['Редактирование сообщения']=['subscribe/update', 'id'=>$id];
		
		$this->render($this->viewPath.'edit', compact('model'));
	}

	public function loadModel($id, $class=null)
	{
		if ($class=='s') {
			$model = Subscribe::model()->findByPk((int)$id);
		}
		else
			$model = Messages::model()->findByPk((int)$id);

		if ($model === null)
			throw new \CHttpException(404, 'Страница не найдена');
		return $model;
	}
	
	protected function getModuleBreadcrumb()
	{
		return [
			'Рассылка' => ['subscribe/index'],
		];
	}	
}