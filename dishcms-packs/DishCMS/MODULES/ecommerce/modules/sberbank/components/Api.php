<?php
namespace ecommerce\modules\sberbank\components;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HEvent;
use ecommerce\modules\sberbank\components\helpers\HSberbank;

class Api
{
    use \common\traits\Singleton;
    
    /**
     * Базовая ссылка для REST запросов
     * @var string 
     */
    const REST_BASE_URL='https://securepayments.sberbank.ru/payment/rest/';
    
    /**
     * Базовая ссылка для тестовых REST запросов
     * @var string 
     */
    const REST_TEST_BASE_URL='https://3dsec.sberbank.ru/payment/rest/';
    
    /**
     * Регистрация оплаты
     * 
     * @param string $id идентификатор оплаты

     * @return string|false возвращает URL для оплаты, 
     * либо FALSE если запрос не отправлен. 
     */
    public function register($id)
    {
        $data=[
            'returnUrl'=>Y::createAbsoluteUrl(HSberbank::settings()->success_url ?: '/ecommerce/sberbank/pay/success'),
            'failUrl'=>Y::createAbsoluteUrl(HSberbank::settings()->fail_url ?: '/ecommerce/sberbank/pay/fail'),
            'orderNumber'=>A::get(HEvent::raise('onSberbankRegisterGetOrderNumber', ['id'=>$id])->params, 'orderNumber'),
            'amount'=>A::get(HEvent::raise('onSberbankRegisterGetAmount', ['id'=>$id])->params, 'amount')           
        ];        
        
        if(empty($data['orderNumber'])) {
            return false;
        }
        
        $orderBundle=A::get(HEvent::raise('onSberbankRegisterGetOrderBundle', ['id'=>$id])->params, 'orderBundle');
        if(!empty($orderBundle)) {
            $data['orderBundle']=$orderBundle;
        }
        
        $params=A::get(HEvent::raise('onSberbankRegisterGetParams', ['id'=>$id])->params, 'params');
        if(!empty($params)) {
            $data['params']=$params;
        }
        
        if(is_numeric(HSberbank::settings()->tax_system)) {
            $data['taxSystem']=HSberbank::settings()->tax_system;
        }
        
        $result=$this->send('register.do', $data);
        
        if(!empty($result['formUrl'])) {
            HEvent::raise('onSberbankRegisterSuccess', compact('id', 'result'));
            
            return $result['formUrl'];
        }
        else {
            HEvent::raise('onSberbankRegisterFail', compact('id', 'result'));
        }
        
        return false;
    }
    
    /**
     * Проверка статуса оплаты заказа
     * 
     * @param string $id идентификатор оплаты
     */
    public function getOrderStatus($id, $returnResult=false)
    {
        $result=$this->send('getOrderStatus.do', ['orderId'=>$id]);
        
        if(is_array($result) && array_key_exists('OrderStatus', $result)) {
            HEvent::raise('onSberbankGetOrderStatusSuccess', compact('id', 'result'));
            // 0 Заказ зарегистрирован, но не оплачен
            // 1 Предавторизованная сумма захолдирована (для двухстадийных платежей)
            // 2 Проведена полная авторизация суммы заказа
            // 3 Авторизация отменена
            // 4 По транзакции была проведена операция возврата
            // 5 Инициирована авторизация через ACS банка-эмитента
            // 6 Авторизация отклонена
            if($returnResult) {
                return $result;
            }
            return $result['OrderStatus'];
        }
        else {
            HEvent::raise('onSberbankGetOrderStatusFail', compact('id', 'result'));
        }
        
        if($returnResult) {
            return $result;
        }
        
        return false;
    }
        
    
    /**
     * Отправка запроса через file_get_contents()
     *
     * @param string $method имя REST метода
     * @param [] $data массив данных
     * @throws \CHttpException
     */
    protected function send($method, $data)    
    {
        if($this->setAuth($data)) {
        	$ch=curl_init($this->getUrl($method) . '?' . http_build_query($data));
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER=>true,
                CURLOPT_SSL_VERIFYPEER=>false,
                CURLOPT_HEADER=>false
            ]);
            $result=curl_exec($ch);

            curl_close($ch);

            return json_decode($result, true);
            /*
            $result=file_get_contents($this->getUrl($method), false, stream_context_create([
                'http'=>[
                    'method'=>'POST',
                    'header'=>'Content-type: application/x-www-form-urlencoded',
                    'content'=>http_build_query($data),
                    'timeout'=>60,
                ],
                'ssl'=>[
                    'allow_self_signed'=>true,
                    'verify_peer'=>false,
                ]
            ]));
            
            return json_decode($result, true);
            /**/
        }
        
        return false;        
    }
    
    /**
     * Отправка запроса через CURL
     * 
     * @param string $method имя REST метода 
     * @param [] $data массив данных
     * @throws \CHttpException
     */
    protected function curl($method, $data)
    {
        if($this->setAuth($data)) {
            $ch=curl_init($this->getUrl($method));        
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER=>true,
                CURLOPT_FOLLOWLOCATION=>true,
                CURLOPT_VERBOSE=>true,
                CURLOPT_STDERR=>($verbose = fopen('php://temp', 'rw+')),
                CURLOPT_FILETIME=>true,
                CURLOPT_POST=>1,
                CURLOPT_POSTFIELDS=>http_build_query($data),
                CURLOPT_SSL_VERIFYHOST=>0,
                CURLOPT_SSL_VERIFYPEER=>0,
            ]);
            
            $result=curl_exec($ch);
            
            echo "Verbose information:\n", !rewind($verbose), stream_get_contents($verbose), "\n";
            
            curl_close($ch);
            
            echo $result;
            
            die;
        }
        
        throw new \CHttpException(400);
    }
    
    /**
     * Устанавливает параметры авторизации во входной массив данных
     * @param [] $data массив данных
     * @return boolean
     */
    protected function setAuth(&$data)
    {
        if(HSberbank::settings()->token) {
            $data['token']=HSberbank::settings()->token;
        }
        elseif(!HSberbank::settings()->login || !HSberbank::settings()->password) {
            return false;
        }
        else {
            $data['userName']=HSberbank::settings()->login;
            $data['password']=HSberbank::settings()->password;
        }
        
        if(HSberbank::settings()->merchant) {
            $data['merchantLogin']=HSberbank::settings()->merchant;
        }
        
        return true;
    }
    
    /**
     * Получить URL для запроса
     * @param string $method имя REST метода 
     * @return string
     */
    protected function getUrl($method)
    {
        if(HSberbank::settings()->is_test_mode) {
            return self::REST_TEST_BASE_URL . $method;
        }
        else {
            return self::REST_BASE_URL . $method;
        }
    }
}