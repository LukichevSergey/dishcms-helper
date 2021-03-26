<?php
use common\components\helpers\HArray as A;
use common\components\helpers\HAjax;

class NotiSendController extends Controller
{
    public function filters()
    {
        return [
            'ajaxOnly +add'
        ];
    }
    
    public function actionAdd()
    {
        $ajax=HAjax::start();
        
        $email=trim(A::get($_REQUEST, 'email'));
        if($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $result=$this->add($email);
            if(!empty($result['errors'])) {
                foreach($result['errors'] as $error) {
                    if($error['code'] == 422) {
                        $ajax->success=true;
                        $ajax->data['msg']='Подписка Вами уже оформлена!';
                    }
                }
            }
            else {
                $ajax->success=!empty($result['id']);
                $ajax->data['confirmed']=empty($result['confirmed']) ? false : $result['confirmed'];
                $ajax->data['msg']=$ajax->data['confirmed'] ? 'Подписка оформлена!' : "На указанный Вами адрес {$email} выслано письмо для активации Вашей подписки!";
            }
        }
        
        $ajax->end();
    }
    
    private function add($email)
    {
        $result = $this->send('https://api.notisend.ru/v1/email/lists/'.(int)\D::cms('notisend_list_id').'/recipients', [
            'email'=>$email,
            'unconfirmed'=>(bool)\D::cms('notisend_unconfirmed')
        ],'POST');
        
        return $result; 
    }
    
    private function send($url, $data, $httpRequestType='GET')
    {
        $options=[
            CURLOPT_RETURNTRANSFER=>true,
            CURLOPT_CUSTOMREQUEST=>$httpRequestType,
            CURLOPT_HTTPHEADER=>[
                'Content-Type: application/json',
                'Authorization: Bearer ' . \D::cms('notisend_apikey')
            ],
        ];
        
        if($httpRequestType == 'POST') {
            $options[CURLOPT_POSTFIELDS]=json_encode($data);
        }
        else {
            $url .= ((strpos($url, '?') !== false) ? '&' : '?') . http_build_query($data);
        }
        
        $ch=curl_init($url);
        
        curl_setopt_array($ch, $options);
        
        $result=curl_exec($ch);
        
        curl_close($ch);
        
        return @json_decode($result, true);
    }
}