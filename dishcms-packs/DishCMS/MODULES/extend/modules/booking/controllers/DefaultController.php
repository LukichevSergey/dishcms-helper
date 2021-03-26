<?php
namespace extend\modules\booking\controllers;

use common\components\helpers\HArray as A;
use common\components\helpers\HRequest as R;
use common\components\helpers\HAjax;
use common\components\helpers\HDb;
use common\components\helpers\HHash;
use common\ext\email\components\helpers\HEmail;
use extend\modules\booking\components\helpers\HBooking;
use crud\models\ar\extend\modules\booking\models\Schedule;
use crud\models\ar\extend\modules\booking\models\Request;

class DefaultController extends \Controller
{
    /**
     * 
     * {@inheritDoc}
     * @see \CController::filters()
     */
    public function filters()
    {
        return A::m(parent::filters(), [
            'ajaxOnly +booking, reject'
        ]);
    }
    
    /**
     * Action: Получить форму бронирования
     */
    public function actionGetBookingForm()
    {
        $this->layout=false;
        
        if(preg_match('/^(\d{2})\.(\d{2})\.(\d{4})$/', R::get('date'), $m)) {
            $date=new \DateTimeImmutable();
            
            $date=$date->setDate($m[3], $m[2], $m[1]);
            
            $schedule=HBooking::getScheduleData($date);
            
            $this->render('extend.modules.booking.views.default.booking_form', compact('schedule', 'date'));
        }
        else {
            echo 'Неверная дата бронирования';
        }
    }
    
    /**
     * Action: Бронирование
     */
    public function actionBooking()
    {
        $ajax=HAjax::start();
        
        $name=R::post('name');
        $phone=R::post('phone');
        $comment=R::post('comment');
        $count=R::post('count', 1);
        $items=R::post('items');
        
        if(empty($name)) {
            $ajax->addError('Имя обязательно для заполнения');
        }
        
        if(empty($phone)) {
            $ajax->addError('Номер телефона обязателен для заполнения');
        }
        
        if(empty($items)) {
            $ajax->addError('Не выбрано время бронирования');
        }
        
        if(!$ajax->hasErrors) {
            $requests=[];
            foreach($items as $item) {
                if(preg_match('/^(\d{2})\.(\d{2})\.(\d{4})$/', $item['date'], $m)) {
                    if(preg_match('/^(\d{2}):(\d{2})$/', $item['time'], $t) && ((int)$t[1]<24) && ((int)$t[2]<60)) {
                        if($schedule=Schedule::model()->resetScope()->published()->byHash($item['hash'])->find()) {
                            if($count > $schedule->session_ticket_count) {
                                $count=$schedule->session_ticket_count;
                            }
                            
                            $date=date_create_immutable_from_format('Y-m-d H:i:s', "{$m[3]}-{$m[2]}-{$m[1]} {$t[1]}:{$t[2]}:00");
                            $freeTicketCount=HBooking::getFreeTicketCount($date, $schedule->session_ticket_count);
                            
                            $request=new Request;
                            $request->date=$date->format('Y-m-d H:i:s');
                            if($freeTicketCount >= $count) {
                                $request->name=$name;
                                $request->phone=$phone;
                                $request->comment=$comment;
                                $request->count=$count;
                                $request->price=$schedule->session_ticket_price;
                                if($request->validate()) {
                                    $requests[]=$request;
                                }
                            }
                            else {
                                $ajax->addError('Бронирование на ' . $request->getFormattedDate() . ' не выполнено, по причине отсутствия доступных мест');
                            }
                        }
                    }
                    else {
                        $ajax->addError('Некорректное время бронирования');
                    }
                }
                else {
                    $ajax->addError('Некорректная дата бронирования');
                }
            }
            
            if(!empty($requests)) {
                $ajax->data['name']=$name;
                $ajax->data['messages']=[];
                foreach($requests as $request) {
                    if(!$request->save()) {
                        $ajax->addError('Бронирование на ' . $request->getFormattedDate() . ' не выполнено');
                    }
                    else {
                        $ajax->data['messages'][$request->id]='Оформлено бронирование на ' . $request->getFormattedDate();
                    }
                }
                
                $ajax->data['reject']=HHash::srEcrypt(array_keys($ajax->data['messages']));
                
                if(!empty($ajax->data['messages'])) {
                    HEmail::cmsAdminSend(
                        'Новая заявка бронирования на сайте ' . \Yii::app()->name, 
                        compact('ajax', 'requests'),
                        'extend.modules.booking.views._email.new_request'
                    );
                    $ajax->success=true;
                }
            }
            elseif(!$ajax->hasErrors) {
                $ajax->addError('Бронирование не выполнено');
            }
        }
        
        $ajax->end();
    }
    
    /**
     * Action: отмена бронирования
     */
    public function actionReject()
    {
        $ajax=HAjax::start();
        
        $rejects=HHash::srDecrypt(R::post('reject'));
        if(is_array($rejects) && !empty($rejects)) {
            $criteria=HDb::criteria();
            $criteria->addInCondition('id', $rejects);
            if($requests=Request::model()->resetScope()->findAll($criteria)) {
                foreach($requests as $request) {
                    if(!$request->reject) {
                        $request->reject=1;
                        $request->update(['reject']);
                    }
                    $ajax->data['messages'][$request->id]='Бронирование на ' . $request->getFormattedDate() . ' отменено';
                }
                
                HEmail::cmsAdminSend(
                    'Отмена бронирования на сайте ' . \Yii::app()->name,
                    compact('ajax', 'requests'),
                    'extend.modules.booking.views._email.reject_request'
                );
                $ajax->success=true;
            }
        }
        
        if(!$ajax->success) {
            $ajax->addError('Отменить бронирование не удалось');
        }
        
        $ajax->end();
    }
}