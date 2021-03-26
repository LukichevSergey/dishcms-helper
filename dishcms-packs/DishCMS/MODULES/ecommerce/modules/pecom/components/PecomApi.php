<?php
/**
 * ПЭК API
 */
namespace pecom\components;

use common\components\helpers\HYii as Y;

class PecomApi
{
    use \common\traits\Singleton;
    
    protected $certificate='/../data/cacert-kabinet_pecom_ru.pem';
    
    /**
     * @link https://pecom.ru/business/developers/api_public/
     * @param boolean $main возвратить только основные города, в которых есть терминалы.
     */
    public function towns($main=true)
    {
        $towns=Y::cache()->get('pecom_towns'.($main?'_main':''));
        if(!is_array($towns)) {
            $_towns=$this->get('https://new.pecom.ru/ru/calc/towns.php');
            if($main) {
                $towns=[];
                foreach($_towns as $group=>$list) {
                    foreach($list as $code=>$title) {
                        if($title===$group) {
                            $towns[$code]=$title;
                            break;
                        }
                    }
                }
            }
            else {
                $towns=$_towns;
            }
            Y::cache()->set('pecom_towns'.($main?'_main':''), $towns);
        }
        return $towns;
    }
    
    /**
     * @link https://pecom.ru/business/developers/api_public/
     */
    public function calc($params)
    {
        if(!is_array($params)) {
            return false;
        }
        
        return $this->get('https://calc.pecom.ru/bitrix/components/pecom/calc/ajax.php?'.http_build_query($params));
    }
    
    public function branchesAll()
    {
        return $this->call('branches', 'all');
    }
    
    /**
     * @link https://kabinet.pecom.ru/api/v1/help/calculator
     */
    public function calculatorCalculatePrice()
    {
        /*{
           "senderCityId": 446, // Код города отправителя [Number]
           "receiverCityId": 463, // Код города получателя [Number]
            "isOpenCarSender": false, // Растентовка отправителя [Boolean]
           "senderDistanceType": 0, // Тип доп. услуг отправителя [Number]
                                    // 0 - доп. услуги не нужны
                                    // 1 - СК
                                    // 2 - МОЖД
                                    // 3 - ТТК
           "isDayByDay": false, // Необходим забор день в день [Boolean]
           "isOpenCarReceiver": false, // Растентовка получателя [Boolean]
           "receiverDistanceType": 0, // Тип доп. услуг отправителя [Number]
                                      // кодируется аналогично senderDistanceType
           "isHyperMarket": false, // признак гипермаркета [Boolean]
           "calcDate": "2014-01-21", // расчетная дата [Date]
           "isInsurance": true, // Страхование [Boolean]
           "isInsurancePrice": 234.15, // Оценочная стоимость, руб [Number]
           "isPickUp": false, // Нужен забор [Boolean]
           "isDelivery": false, // Нужна доставка [Boolean]
           "Cargos": [{ // Данные о грузах [Array]
              "length": 2.1, // Длина груза, м [Number]
              "width": 3.2, // Ширина груза, м [Number]
              "height": 2.3, // Высота груза, м [Number]
              "volume": 4.4, // Объем груза, м3 [Number]
              "maxSize": 3.2, // Максимальный габарит, м [Number]
              "isHP": false, // Жесткая упаковка [Boolean]
              "sealingPositionsCount": 0, // Количество мест для пломбировки [Number]
              "weight": 10, // Вес, кг [Number]
              "overSize": false // Негабаритный груз [Boolean]
           }]
        }*/
        return $this->call('calculator', 'calculateprice');
    }
    
    /**
     * @access protected 
     */
    protected function get($url)
    {
        $ch=curl_init();
        //print_r($url);
        curl_setopt_array($ch, array(
            CURLOPT_URL=>$url,
            CURLOPT_RETURNTRANSFER=>true,
            CURLOPT_HTTPHEADER=>[
                'Content-Type: application/json; charset=utf-8',
                'Accept: application/json',
            ],
			CURLOPT_SSL_VERIFYPEER=>false
        ));
        
        $result=curl_exec($ch);
         
        curl_close($ch);

        if($result) {
            return json_decode($result, true);
        }
        
        return false;
    }
    
    /**
     * @access protected 
     */
    protected function call($controller, $action, $data=[], $json=true, $returnArray=true)
    {
        if(($data !== false) && $json) {
            $data=json_encode($data);
        }

        $ch=curl_init();
        
        curl_setopt_array($ch, array(
            CURLOPT_URL=>Y::param('pecom.url') . "{$controller}/{$action}/",
            CURLOPT_RETURNTRANSFER=>true,
            CURLOPT_POST=>true,
            CURLOPT_SSL_VERIFYPEER=>true,
            CURLOPT_SSL_VERIFYHOST=>2,
            CURLOPT_CAINFO=>Y::param('pecom.certificate', dirname(__FILE__) . $this->certificate),
            CURLOPT_HTTPAUTH=>CURLAUTH_BASIC,
            CURLOPT_USERPWD=>'user:'.Y::param('pecom.key'),
            CURLOPT_HTTPHEADER=>[
                'Content-Type: application/json; charset=utf-8',
                'Accept: application/json',
                'Content-Length: ' . mb_strlen($data)
            ]
        ));
        
        $result=curl_exec($ch);
//          print_r($data);
//          print_r($result);
//          print_r(curl_getinfo($ch));

        curl_close($ch);

        if($returnArray) {
            return json_decode($result, true);
        }

        return $result;
    }

}
