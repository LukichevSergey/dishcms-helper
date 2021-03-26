<?php
/**
 * СДЭК АПИ
 */
namespace cdek\components;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HRequest as R;
use common\components\helpers\HDb;
use cdek\components\helpers\HCdek;
use cdek\models\Tariff;
use cdek\models\Order;

class CdekApi
{
    use \common\traits\Singleton;
    
    /**
     * Установка геокоординат для модели города СДЭК
     * @param \cdek\models\City &$city модель города СДЭК
     */
    public function setGeodata(&$city)
    {
        if($city instanceof \cdek\models\City) {
            $geocode=urlencode(/*$city->postcode . ',' . */$city->fullname);
        
            $ch=curl_init();        
            curl_setopt($ch, CURLOPT_URL, 'https://geocode-maps.yandex.ru/1.x/?geocode='.$geocode);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
            $result=curl_exec($ch);
            $info=curl_getinfo($ch);
            
            if(isset($info['http_code']) && ($info['http_code'] == 200)) {
                $xml=simplexml_load_string($result);
                
                if($geoObject=$xml->GeoObjectCollection->featureMember->GeoObject) {
                    $columns=[];
                    if($pos=(string)$geoObject->Point->pos) {
                        $coords=explode(' ', $pos);
                        $columns['ym_point_x']=$coords[0];
                        $columns['ym_point_y']=$coords[1];
                    }
                    if($boundedBy=$geoObject->boundedBy) {
                        if($lc=(string)$boundedBy->Envelope->lowerCorner) {
                            $coords=explode(' ', $lc);
                            $columns['ym_bounds_lx']=$coords[0];
                            $columns['ym_bounds_ly']=$coords[1];
                        }
                        if($uc=(string)$boundedBy->Envelope->upperCorner) {
                            $coords=explode(' ', $uc);
                            $columns['ym_bounds_ux']=$coords[0];
                            $columns['ym_bounds_uy']=$coords[1];
                        }
                    }
                    
                    if(!empty($columns)) {
                        $criteria=HDb::criteria();
                        if($city->id) {
                            $criteria->addColumnCondition(['id'=>$city->id]);
                        }
                        elseif($city->cdek_id) {
                            $criteria->addColumnCondition(['cdek_id'=>$city->cdek_id]);
                        }
                        elseif($city->fullname) {
                            $criteria->addColumnCondition(['fullname'=>$city->fullname]);
                        }
        
                        if(!empty($criteria->params)) {
                            $updated=$city->updateAll($columns, $criteria);
                            $city->attributes=$columns;
                        }
                    }
                }
                
            }
        
            curl_close($ch);        
        }
    }
    
    /**
     * @var array $params параметры для калькуляции
     * Обязательные параметры:
     * "rec_city_id"
     * "mode"
     * "attribute_product_weight" (по умолчанию weight)
     * "attribute_product_volume" (по умолчанию volume)
     */
    public function calc($params=[])
    {
        if(isset($params['rec_city_id']) && isset($params['mode'])) {
            // подключаем класс калькулятора СДЭК
            \Yii::import('cdek.vendors.cdek.CalculatePriceDeliveryCdek');
            //создаём экземпляр объекта CalculatePriceDeliveryCdek
            $calc = new \CalculatePriceDeliveryCdek();            
            //Авторизация. Для получения логина/пароля (в т.ч. тестового) обратитесь к разработчикам СДЭК -->
            $calc->setAuth(Y::param('cdek.account'), Y::param('cdek.secure_password'));
            //устанавливаем город-отправитель
            $calc->setSenderCityId(HCdek::settings()->cdek_send_city_id); 
            //устанавливаем город-получатель
            $calc->setReceiverCityId($params['rec_city_id']);
            //устанавливаем дату планируемой отправки
            $calc->setDateExecute(date('Y-m-d'));        
            // добавляем тарифы, по которым будет производится рассчет
            if($tariffs=Tariff::i()->tariffCodes(HCdek::settings()->cdek_tariff_group, $params['mode'])) {
                
                // добавляем товары заказа
                $totalWeight=0;
                $totalVolume=0;
                foreach(\Yii::app()->cart->getData() as $item) {
                    $weight=A::rget($item, 'attributes.'.A::get($params, 'attribute_product_weight', 'weight'), 0);
                    $volume=A::rget($item, 'attributes.'.A::get($params, 'attribute_product_volume', 'volume'), 0);
                    HCdek::normalizeSize($weight, $volume);
                    
                    for($i=0; $i<(int)$item['count']; $i++) {
                        $calc->addGoodsItemByVolume($weight, $volume);
                        //$calc->addGoodsItemBySize($weight, $length, $width, $height);
                        $totalWeight+=$weight;
                        $totalVolume+=$volume;
                    }
                }
                
                $tariffsCount=0;
                foreach($tariffs as $tariffId=>$tariffConfig) {
                    if($totalWeight > A::get($tariffConfig, 'maxWeight', $totalWeight)) continue;
                    if($totalWeight < A::get($tariffConfig, 'minWeight', $totalWeight)) continue;
                    $calc->addTariffPriority($tariffId, $tariffId);
                    $tariffsCount++;
                }
                if(!$tariffsCount) {
                    return [
                        'errors'=>[
                            'error'=>['text'=>'Нет подоходящих тарифов для рассчета стоимости доставки']
                        ]
                    ];
                }                
                
                // производим рассчет
                if ($calc->calculate() === true) {
                    $calcResult=$calc->getResult();
                    
                    if(isset($calcResult['result']['price'])) {
                        $calcResult['result']['price']=HCdek::normalizePrice($calcResult['result']['price']);
                    }
                    if(isset($calcResult['result']['priceByCurrency'])) {
                        $calcResult['result']['priceByCurrency']=HCdek::normalizePrice($calcResult['result']['priceByCurrency']);
                    }
                    
                    return [
                        'result'=>$calcResult,
                        'totalWeight'=>$totalWeight,
                        'totalVolume'=>$totalVolume
                    ];
                }
                else {
                    return [
                        'errors'=>$calc->getError()
                    ];
                }
            }
        }
        
        return false;
    }
    
    /**
     * Получить список ПВЗ
     */
    public function getPvzList($cityId, $returnAssoc=false)
    {
        $pvzData=$this->isend('/pvzlist.php?cityid='.$cityId);
        
        if($returnAssoc) {
            return $this->pvzDataToArray($pvzData);
        }
        
        return $pvzData;
    }
    
    /**
     * Создание новой заявки
     * @param \cdek\models\Order $order
     * @param boolean $update обновить статус заявки
     */
    public function newOrder($order, $update=false)
    {
        if(is_numeric($order)) {
            $order=Order::model()->wcolumns(['order_id'=>$order])->find();
        }
        
        if(!($order instanceof Order)) {
            return false;
        }
        
        // формирование XML для запроса в сервис СДЭК
        $xml='<?xml version="1.0" encoding="UTF-8"?>';
        $xml.='<DeliveryRequest OrderCount="1" Number="'.$order->id.'" ' . $this->getXMLHeaders(true) . '></DeliveryRequest>';        
        
        $xml=new \SimpleXMLElement($xml);
        $orderNode=$xml->addChild('Order');
        $orderNode->addAttribute('Number', $order->order_number);
        $orderNode->addAttribute('Comment', 'Заказ #'.$order->order_id);
        //$orderNode->addAttribute('DeliveryRecipientCost', '');
        $orderNode->addAttribute('SendCityCode', $order->send_city_id);
        $orderNode->addAttribute('RecCityCode', $order->rec_city_id);
        $orderNode->addAttribute('RecipientName', $order->rec_name);
        if($order->rec_email) {
            $orderNode->addAttribute('RecipientEmail', $order->rec_email);
        }
        $orderNode->addAttribute('Phone', $order->rec_phone);
        $orderNode->addAttribute('TariffTypeCode', $order->tariff_id);
        $orderNode->addAttribute('SellerName', HCdek::settings()->cdek_seller_name);
        $orderNode->addAttribute('RecientCurrency', 'RUB');
        $orderNode->addAttribute('ItemsCurrency', 'RUB');
        
        $addressNode=$orderNode->addChild('Address');
        if($order->isPvzMode(Tariff::i()->getTariffMode($order->tariff_id))) { 
            $addressNode->addAttribute('PvzCode', $order->pvz_code);
        }
        else {
            $addressNode->addAttribute('Street', $order->address_street);
            $addressNode->addAttribute('House', $order->address_house);
            $addressNode->addAttribute('Flat', $order->address_flat);
        }
        
        $packageNode=$orderNode->addChild('Package');
        $packageNode->addAttribute('Number', $order->package_number);
        $packageNode->addAttribute('BarCode', $order->package_barcode);
        
        if(!$order->package_weight) $order->package_weight=1000;
        $packageNode->addAttribute('Weight', $order->package_weight);
        
        foreach($order->getItems() as $item) {
            $itemNode=$packageNode->addChild('Item');
            foreach(['WareKey', 'Cost', 'Payment', 'PaymentVATRate', 'PaymentVATSum', 'Weight', 'Amount', 'Comment'] as $attributeName) {
            	if(($attributeName == 'Weight') && (!isset($item[$attributeName]) || !$item[$attributeName])) {
            		$item[$attributeName]=1;
            	}
                if(isset($item[$attributeName])) {
                    $itemNode->addAttribute($attributeName, $item[$attributeName]);
                }
            }
        }

//      https://integration.cdek.ru/new_orders.php
  		/*<response>
		 <Order Number="Номер заказа" DispatchNumber ="Номер накладной СДЭК"/>
		<Order Msg="Добавлено заказов CntOrder"/>
		</response>
        
        <?xml version="1.0" encoding="UTF-8"?>
		<response>< Order Number="Номер заказа" ErrorCode=”Код ошибки» Msg="Error: описание ошибки"/></response>
        **/

        $result=$this->isend('/new_orders.php', ['xml_request'=>$xml->asXML()]);
        
        if($update && $result) {
            try {
                if($dispatchNumber=(string)$result->Order[0]['DispatchNumber']) {
	                $order->dispatch_number=$dispatchNumber;
					$order->status=Order::STATUS_CDEK;
				}
				else {
					 $order->comment=$result->asXML();
					 $order->status=Order::STATUS_CDEK_ERROR;
				}
                $order->save();
            }
            catch(\Exception $e) {}
        }
        
        return $result;
    }
    
    protected function getXMLHeaders($returnAsAttributes=false)
    {
        $date=date('Y-m-d');
        
        if($returnAsAttributes) {
            return 'Date="'.$date.'"' 
                . ' Account="'.Y::param('cdek.account').'"'
                . ' Secure="'.md5($date.'&'.Y::param('cdek.secure_password')).'"';
        }
        
        return [
            'Date'=>$date,
            'Account'=>Y::param('cdek.account'),
            'Secure'=>md5($date.'&'.Y::param('cdek.secure_password'))
        ];
    }
    
    /**
     * Запрос для интеграции
     * @access protected 
     */
    protected function isend($url, $data=false, $returnSimpleXml=true)
    {
        $ch=curl_init();
        curl_setopt($ch, CURLOPT_URL, Y::param('cdek.integration.url') . $url);        
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, ($data===false) ? "GET" : "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        if($data !== false) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        
        $result = curl_exec($ch);
        
        //var_dump($data, $headers, $result);
        //var_dump(curl_getinfo($ch));
        
        curl_close($ch);
        
        if($returnSimpleXml) {
            $xml=simplexml_load_string($result);
            return $xml;
        }
        
        return $result;
    }
    
    protected function pvzDataToArray($xmlPvzData)
    {   
        $pvzItems=[];
        
        if($xmlPvzData) {
            foreach($xmlPvzData->Pvz as $pvz) {
                $pvzCode=(string)$pvz['Code'];
                $pvzItems[$pvzCode]=[
                    'Code' => $pvzCode,
                    'Name' => (string)$pvz['Name'],
                    'CountryCode' => (string)$pvz['CountryCode'],
                    'CountryName' => (string)$pvz['CountryName'],
                    'RegionCode' => (string)$pvz['RegionCode'],
                    'RegionName' => (string)$pvz['RegionName'],
                    'CityCode' => (string)$pvz['CityCode'],
                    'City' => (string)$pvz['City'],
                    'WorkTime' => (string)$pvz['WorkTime'],
                    'Address' => (string)$pvz['Address'],
                    'FullAddress' => (string)$pvz['FullAddress'],
                    'AddressComment' => (string)$pvz['AddressComment'],
                    'Phone' => (string)$pvz['Phone'],
                    'Email' => (string)$pvz['Email'],
                    'Note' => (string)$pvz['Note'],
                    'coordX' => (string)$pvz['coordX'],
                    'coordY' => (string)$pvz['coordY'],
                    'Type' => (string)$pvz['Type'],
                    'ownerCode' => (string)$pvz['ownerCode'],
                    'IsDressingRoom' => (string)$pvz['IsDressingRoom'],
                    'HaveCashless' => (string)$pvz['HaveCashless'],
                    'AllowedCod' => (string)$pvz['AllowedCod'],
                    'NearestStation' => (string)$pvz['NearestStation'],
                    'MetroStation' => (string)$pvz['MetroStation'],
                    'Site' => (string)$pvz['Site']
                ];
            }
        }
        
        return $pvzItems;
    }
}
