<?php

namespace subscribe\controllers;

use \subscribe\models\Subscribe;

class DefaultController extends \Controller
{


    public function filters() {
        return array(
            'ajaxOnly + index',
        );
    }

    protected function performAjaxValidation($model)
    {
            if(isset($_POST['ajax']) && $_POST['ajax']==='user-form')
            {
                    echo \CActiveForm::validate($model);
                    \Yii::app()->end();
            }
    }

    public function actionIndex()
    {
        $model = new Subscribe;
        
        if(isset($_POST['subscribe_models_Subscribe']) && \Yii::app()->request->isAjaxRequest)
        {
            $model->attributes=$_POST['subscribe_models_Subscribe'];
            
            if(!$model->validate()){
            	$this->performAjaxValidation($model);
            }


			if ($this->check($model->email)) {
				return true;
			}
			else {
				
		    	$model->hash = hash("crc32", hash("md5", $model->email));
		    	$model->save();
		        echo \CJSON::encode(array('email'=>$model->email, 'message'=>'<span>Подписка оформлена</span>', 'is_ok'=>'1'));

		        $message = $this->renderPartial('success', array('current_hash'=>$model->hash), true);
		        $mail = new \PHPMailer;
		        $mail->XMailer = "why you see this text?";
				$mail->Subject = "Подписка на новости";
				$mail->Body    = $message;
				$mail->AltBody = strip_tags($message);
				$mail->addAddress( $model->email );
				$mail->setFrom('subscribe@'.$_SERVER['SERVER_NAME'], \Yii::app()->name);
				$mail->send();
		        \Yii::app()->end();
			}

        }
    }
    //функция проверки есть ли подписка
	public function check( $email ){
		$current_hash = hash("crc32", hash("md5", $email ));
		$main_check = Subscribe::model()->find('hash=:hash', array(':hash'=>$current_hash));

		if ($main_check==null) {
				return false;
		}
		else{
			echo \CJSON::encode(array('email'=>$email, 'message'=>'Подверждение отмены подписки отправлно.'));

	        $mail = new \PHPMailer;
	        
	        $message = $this->renderPartial('outscribe', array('hash'=>$current_hash), true);

	        $mail->XMailer = "why you see this text?";
			$mail->Subject = "Подписка на новости";
			$mail->Body    = $message;
			$mail->AltBody = strip_tags($message);
			$mail->addAddress( $email );
			$mail->setFrom('subscribe@'.$_SERVER['SERVER_NAME'], \Yii::app()->name);
			$mail->send();
	        \Yii::app()->end();
			return true;
		}


	}
	//Функция отписки
	public function actionOutscribe( $hash ){
		//Отмена подписки
		$check = Subscribe::model()->find('hash=:hash', array(':hash'=>$hash));
		if ($check == null) $this->redirect('/');
		else {
			$check->delete();
			$this->redirect('/');
		}
	
	}

}