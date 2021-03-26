<?php
/**
 * API Почта.России
 * 
 */
namespace rpochta\components;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HRequest as R;
use rpochta\components\helpers\HRPochta;
use rpochta\components\RPochtaConst;
use rpochta\models\Order;

class RPochtaApi
{
    use \common\traits\Singleton;
    
    /**
     * Вычисление стоимости доставки
     * @param array $params
     *  "index_to" почтовый индекс города-доставки или ОПС
     *  "mass" масса отправления в граммах
     *  "attribute_product_weight" (по умолчанию weight)
     *  "product_weight_coeff" (по умолчанию 1000)
     *  "mail_category"
     *  "mail_type"
     *  "payment_method"
     * @param boolean $safeOriginPrice
     */ 
    public function tariff($params=[], $safeOriginPrice=false)
    {
        $mass=(int)A::get($params, 'mass', R::rget('mass'));
        if(!$mass) {
            $mass=0;
            foreach(\Yii::app()->cart->getData() as $item) {
                $weight=(int)A::get($params, 'product_weight_coeff', 1000) * $weight=A::rget($item, 'attributes.'.A::get($params, 'attribute_product_weight', 'weight'), 0);
                for($i=0; $i<(int)$item['count']; $i++) {
                    $mass+=$weight;
                }
            }
        }
        
        $calcResult=$this->send('/1.0/tariff', true, [
            'index-from'=>HRPochta::indexFrom(),
            'courier'=>(bool)HRPochta::settings()->rpochta_courier,
            'declared-value'=>(int)HRPochta::settings()->rpochta_insr_value, // Объявленная ценность
            /*'dimension'=>[ // Линейные размеры
                'height'=>0,
                'length'=>0,
                'width'=>0
            ],*/
            'fragile'=>(bool)HRPochta::settings()->rpochta_fragile,
            'index-to'=>HRPochta::normalizeIndex(A::get($params, 'index_to', R::rget('index_to'))),
            'mail-category'=>A::get($params, 'mail_category', R::rget('mail_category', 'ORDERED')),
            'mail-type'=>A::get($params, 'mail_type', R::rget('mail_type', 'BANDEROL')),
            'mass'=>$mass, 
            'payment-method'=>A::get($params, 'payment_method', R::rget('payment_method', 'BANDEROL')),
            'with-order-of-notice'=>(bool)HRPochta::settings()->rpochta_with_order_of_notice,
            'with-simple-notice'=>(bool)HRPochta::settings()->rpochta_with_simple_notice
        ]);
        
        // производим рассчет
        $result=[];
        if ($calcResult) {
            $error=false;
            if(isset($calcResult['error']) || isset($calcResult['code']) || !isset($calcResult['total-rate'])) {
                $error='Расчет доставки не может быть произведен.<br/>';
                if(isset($calcResult['desc'])) {
                    $error.='Ошибка: '.$calcResult['desc'].'<br/>';
                }
                //$error.=var_export($calcResult, true).'<br/>';
                $error.='Укажите, пожалуйста, другие параметры.';
            }
            elseif(isset($calcResult['total-rate']) && !(float)$calcResult['total-rate']) {
                $error='Выбранная доставка в данный город не осуществляется.<br/>';
                //$error.=var_export($calcResult, true).'<br/>';
                $error.='Рекомендуется выбрать ближайший крупный населенный пункт.';
            }
            
            if($error) {
                $result=[
                    'errors'=>[$error]
                ];
            }
            else {
                if($safeOriginPrice) {
                    $calcResult['origin-total-rate']=A::get($calcResult, 'total-rate', 0);
                    $calcResult['origin-total-vat']=A::get($calcResult, 'total-vat', 0);
                    $calcResult['origin-total-cost']=$calcResult['origin-total-rate'] + $calcResult['origin-total-vat'];                        
                }
                $calcResult['total-rate']=HRPochta::normalizePrice(A::get($calcResult, 'total-rate', 0));
                $calcResult['total-vat']=HRPochta::normalizePrice(A::get($calcResult, 'total-vat', 0));
                $calcResult['total-cost']=$calcResult['total-rate'] + $calcResult['total-vat'];
                
                foreach(RPochtaConst::i()->calcResultFields() as $name=>$label) {
                    if($safeOriginPrice) {
                       $calcResult[$name]['origin-rate']=A::rget($calcResult, "{$name}.rate", 0);
                       $calcResult[$name]['origin-vat']=A::rget($calcResult, "{$name}.vat", 0);
                    }
                    $rate=HRPochta::normalizePrice(A::rget($calcResult, "{$name}.rate", 0));
                    if($rate) {
                        $calcResult[$name]['rate']=$rate;
                        $vat=HRPochta::normalizePrice(A::rget($calcResult, "{$name}.vat", 0));
                        if($vat) {
                            $calcResult[$name]['vat']=$vat;
                        }
                    }
                }
                
                $result=[
                    'result'=>$calcResult,
                    'mass'=>$mass
                ];
            }
        }
        else {
            $result=[
                'errors'=>['Сервис Почта.России временно недоступен.']
            ];
        }
        
        return $result;        
    }
    
    /**
     * Нормализация адреса
     * @link https://otpravka.pochta.ru/specification#/nogroup-normalization_adress
     * 
     * @param array $params входные параметры, может принимать:
     *  "address" проверяемый адрес.
     */
    public function cleanAddress($params=[])
    {
        return $this->send('/1.0/clean/address', true, [
            [
                'id'=>'adr 1',
                'original-address'=>A::get($params, 'address', R::rget('address'))
            ]
        ]);
    }
    
    /**
     * Поиск почтовых индексов в населённом пункте
     * @link https://otpravka.pochta.ru/specification#/services-postoffice-settlement.offices.codes
     */
    public function settlementOfficesCodes()
    {
        return $this->send(
            '/postoffice/1.0/settlement.offices.codes?'
                . 'settlement=' . urlencode(R::rget('settlement'))
                . '&region=' . urlencode(R::rget('region'))
                . '&district=' . urlencode(R::rget('district'))
        );
    }
    
    /**
     * Поиск почтовых отделений по координатам
     * @link https://otpravka.pochta.ru/specification#/services-postoffice-nearby
     * 
     * @param array $params входные параметры, может принимать:
     *  "latitude" широта.
     *  "longitude" долгота.
     *  "top" integer кол-во получаемых ОПС.  По умолчанию 10.
     *  "search-radius" Радиус для поиска (в километрах). По умолчанию 2км.
     */
    public function opsNearby($params=[])
    {
        return $this->send(
            '/postoffice/1.0/nearby'
                . '?filter=ALL'
                . '&search-radius=' . urlencode(A::get($params, 'search-radius', R::rget('search-radius', 2)))
                . '&latitude=' . urlencode(A::get($params, 'latitude', R::rget('latitude')))
                . '&longitude=' . urlencode(A::get($params, 'longitude', R::rget('longitude')))
                . '&top=' . urlencode(A::get($params, 'top', R::rget('top', 10)))
        );
    }
    
    /**
     * Поиск почтовых сервисов ОПС
     * @link https://otpravka.pochta.ru/specification#/services-postoffice-service
     */
    public function opsServices()
    {
        return $this->send(
            '/postoffice/1.0/'.R::rget('postal-code').'/services'
        );
    }
    
    /**
     * Поиск обслуживающего ОПС по адресу
     * @link https://otpravka.pochta.ru/specification#/services-postoffice-by-address
     * 
     * @param array $params входные параметры, может принимать:
     * "address" string адрес для которого ищутся ОПС
     * "top" integer кол-во получаемых ОПС
     *
     */
    public function opsByAddress($params=[])
    {
        return $this->send(
            '/postoffice/1.0/by-address'
                . '?address=' . urlencode(A::get($params, 'address', R::rget('address')))
                . '&top=' . urlencode(A::get($params, 'top', R::rget('top', 10)))
        );
    }
    
    /**
     * @access protected 
     */
    protected function send($url, $post=false, $data=false, $json=true, $returnArray=true)
    {
         // var_dump($data);
        if(($data !== false) && $json) {
            $data=json_encode($data , JSON_UNESCAPED_UNICODE);
        }
        $type=is_string($post) ? $post : ($post ? "POST" : "GET");
        
        $ch=curl_init();
        curl_setopt($ch, CURLOPT_URL, Y::param('rpochta.url') . $url);        
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $headers=[
            'Authorization:	AccessToken ' . Y::param('rpochta.token'),
            'X-User-Authorization: Basic ' . Y::param('rpochta.auth_key'),
            'Content-Type: application/json;charset=UTF-8',
            //'Accept: application/json;charset=UTF-8',
        ];
        if($post) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            //$headers[]='Content-Length: ' . mb_strlen($data);
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        $result = curl_exec($ch);
        // print_r($headers);
        // print_r($data);
        // print_r($result);
        // print_r(curl_getinfo($ch));
        
        curl_close($ch);
        
        if($returnArray) {
            return json_decode($result, true);
        }
        
        return $result;
    }
    
    /**
     * Новый заказ.
     * @link https://otpravka.pochta.ru/specification#/orders-creating_order
     */
    public function newOrder($order, $update=false)
    {
        if(is_numeric($order)) {
            $order=Order::model()->wcolumns(['order_id'=>$order])->find();
        }
        
        if(!($order instanceof Order)) {
            return false;
        }
        
        // формирование данных для запроса в сервис Почта.России
        $data=[];
        
        $data['order-num']=$order->order_number; // Номер заказа. Внешний идентификатор заказа, который формируется отправителем
        
        if(HRPochta::settings()->rpochta_brand_name) {
            $data['brand-name']=HRPochta::settings()->rpochta_brand_name;
        }
        
        $data['insr-value']=(int)HRPochta::settings()->rpochta_insr_value; // Сумма объявленной ценности (копейки)
        
        $data['payment']=0; // Cумма наложенного платежа (копейки)
        
        $data['recipient-name']=$order->getFullName(); // Наименование получателя одной строкой (ФИО, наименование организации)
        $data['given-name']=$order->given_name; // Имя получателя
        $data['middle-name']=$order->given_midname; // Отчество получателя
        $data['surname']=$order->given_surname; // Фамилия получателя
        $data['tel-address']=(int)$order->given_phone; // Телефон получателя (может быть обязательным для некоторых типов отправлений)
        
        $data['mail-category']=$order->rpo_category; // Категория РПО
        $data['mail-direct']=643; // Код страны Россия: 643
        $data['mail-type']=$order->rpo_type; // Категория РПО
        $data['payment-method']=$order->payment_type; // Способ оплаты
        //$data['envelope-type']='C4'; // Тип конверта - ГОСТ Р 51506-99. 
        
        $data['manual-address-input']=false; // Отметка 'Ручной ввод адреса'
        $data['sms-notice-recipient']=(int)HRPochta::settings()->rpochta_sms_notice_recipient; // Признак услуги SMS уведомления
        $data['with-order-of-notice']=(bool)HRPochta::settings()->rpochta_with_order_of_notice; // Отметка 'С заказным уведомлением'
        $data['with-simple-notice']=(bool)HRPochta::settings()->rpochta_with_simple_notice; // Отметка 'Ручной ввод адреса'
        $data['wo-mail-rank']=(bool)HRPochta::settings()->rpochta_wo_mail_rank; // Отметка 'Без разряда'
        $data['fragile']=(bool)HRPochta::settings()->rpochta_fragile; // Установлена ли отметка 'Осторожно/Хрупкое'
        $data['courier']=(bool)HRPochta::settings()->rpochta_courier; // Отметка 'Курьер'
        
        $data['mass']=(int)$order->mass; // Вес РПО (в граммах)        
        $data['dimension']=['height'=>0, 'length'=>0, 'width'=>0]; // Линейные размеры
        
        if($order->isOpsMode()) {
            //$ops=$order->getOpsData();
            $address=HRPochta::getAddressData($order->geOpstFullAddressTo(), false, true);
            //$data['postoffice-code']=(int)$order->ops_index; // Индекс места приема
        }
        else {
            $address=$order->getAddressData();
        }
        $addressFields=[
            'address-type', 
            'area', // Район
            'building', // Часть здания: Строение
            'corpus', // Часть здания: Корпус
            'hotel', // Название гостиницы
            'house', // Часть адреса: Номер здания
            'index', // Почтовый индекс
            'letter', // Часть здания: Литера
            'location', // Микрорайон
            'num-address-type', // Номер для а/я, войсковая часть, войсковая часть ЮЯ, полевая почта
            'place', // Населенный пункт
            'region', // Область, регион
            'room', // Часть здания: Номер помещения
            'slash', // Часть здания: Дробь
            'street' // Часть адреса: Улица
        ];
        foreach($addressFields as $field) {
            $fieldTo=$field.'-to';
            if(isset($address[$field])) {
                $data[$fieldTo]=$address[$field];
            }
            else {
                if($field=='index') $data[$fieldTo]=0;
                else $data[$fieldTo]='';
            }
            if($field=='index') {
                $data[$fieldTo]=(int)$data[$fieldTo];
                // $data['postoffice-code']=(string)$data[$fieldTo];
            }
        }
        
        $data['comment']=$order->order_number; // Комментарий:Номер заказа. Внешний идентификатор заказа, который формируется отправителем
        
        //print_r($data);
        
        $jsonData='[' . json_encode($data , JSON_UNESCAPED_UNICODE) . ']';
        
        $result=$this->send('/1.0/user/backlog', 'PUT', $jsonData, false);
        
        
        if($update && $result) {
            try {
                $order->status=Order::STATUS_RPOCHTA_ERROR;
                $order->setReqData($result);
                if(!isset($result['errors']) && isset($result['result-ids'])) {
                    $order->setResultIds($result['result-ids']);
                    if(!empty($order->getResultIds())) {
                        $order->status=Order::STATUS_RPOCHTA;
                    }
                }
                $order->update(['status', 'result_ids', 'req_data']);
            }
            catch(\Exception $e) {}
        }
        
        return $result;
    }
}
