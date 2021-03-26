<?php
/**
 * Модель Заказа модуля Почта.России
 * 
 * @FIXME использует базу городов СДЭК
 */
namespace rpochta\models;

use common\components\helpers\HArray as A;
use rpochta\components\helpers\HRPochta;
use rpochta\components\RPochtaConst;
use rpochta\components\RPochtaApi;
use cdek\models\City;

class Order extends \common\components\base\ActiveRecord
{
    const STATUS_WAIT=1;
    const STATUS_RPOCHTA=10;
    const STATUS_CLOSE=20;
    const STATUS_REJECT=30;

	const STATUS_RPOCHTA_ERROR=110;
    
	/**
	 * (non-PHPdoc)
	 * @see \CActiveRecord::tableName()
	 */
	public function tableName()
	{
		return 'rpochta_orders';
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CModel::behaviors()
	 */
	public function behaviors()
	{
		return A::m(parent::behaviors(), [
            'updateTimeBehavior'=>[
				'class'=>'\common\ext\updateTime\behaviors\UpdateTimeBehavior',
				'attributeLabel'=>'Дата обновления записи',
				'addColumn'=>false
			],
		]);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CActiveRecord::scopes()
	 */
	public function scopes()
	{
		return $this->getScopes([
				
		]);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CActiveRecord::relations()
	 */
	public function relations()
	{
		return $this->getRelations([
			'order'=>[\CActiveRecord::HAS_ONE, '\DOrder\models\DOrder', 'order_id'],
		]);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CModel::rules()
	 */
	public function rules()
	{
		return $this->getRules([
            ['index_from, index_to, rpo_category, payment_type, rpo_type', 'required'],
            ['ops_address, ops_index', 'required', 'on'=>RPochtaConst::MODE_OPS, 'message'=>'Необходимо выбрать почтовое отделение связи'],
            ['address_street, address_house, address_room', 'required', 'on'=>RPochtaConst::MODE_ADDRESS],
            ['address_street', 'validateAddress', 'on'=>RPochtaConst::MODE_ADDRESS],
            ['index_from, index_to, ops_index', 'safe'],
            ['ops_address, ops_longitude, ops_latitude, ops_data', 'safe'],
            ['address_street, address_house, address_room', 'safe'],
            ['city_name_from, city_name_to, given_name, given_midname, given_surname, given_phone', 'safe'],
            ['mass, items, delivery_origin_price, delivery_price, delivery_extra_charge, delivery_price_data, comment, status', 'safe'],
            
            ['delivery_mode', 'safe']
		]);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CModel::attributeLabels()
	 */
	public function attributeLabels()
	{
		return $this->getAttributeLabels([
            'id'=>'pk',
            'order_id'=>'Номер заказа',
            'order_number'=>'Код заказа',
            'payment_type'=>'Способ оплаты',
            'rpo_category'=>'Категория РПО',
            //'rpo_type'=>'Вид РПО',
            'rpo_type'=>'Скорость доставки',
            'result_ids'=>'Идентификатор(ы) заказа в сервисе Почта.России',         
            'index_from'=>'Почтовый индекс города-отправителя',
            'city_name_from'=>'Наименование города-отправителя',
            'index_to'=>'Почтовый индекс города-получателя',
            'city_name_to'=>'Наименование города-отправителя',
            'given_name'=>'Имя получателя',
            'given_midname'=>'Отчество получателя',
            'given_surname'=>'Фамилия получателя',
            'given_phone'=>'Номер телефона получателя',
            'req_data'=>'Данные запроса добавления заказа в сервис Почта.России',
            'items'=>'Товары заказа',
            'mass'=>'Вес посылки в граммах',
            'address_street'=>'Улица',
            'address_house'=>'Дом',
            'address_room'=>'Квартира',
            'address_data'=>'Данные адреса',
            'ops_address'=>'Адрес почтового отделения',
            'ops_index'=>'Почтовый индекс почтового отделения',
            'ops_longitude'=>'Долгота на карте почтового отделения',
            'ops_latitude'=>'Широта на карте почтового отделения',
            'ops_data'=>'Данные почтового отделения',
            'delivery_origin_price'=>'Стоимость доставки без наценки',
            'delivery_price'=>'Стоимость доставки',
            'delivery_price_data'=>'Данные стоимости доставки',
            'delivery_extra_charge'=>'Наценка',
            'comment'=>'Комментарий',
            'status'=>'Статус доставки',
            'create_time'=>'Дата создания записи',
            'update_time'=>'Дата обновления записи',
		]);
	}
    
    public function validateAddress($attribute, $params)
    {
        $address_data=HRPochta::getAddressData($this->getFullAddressTo(), false);
        if($address_data === null) {
            $this->addError($attribute, 'Невозможно проверить корректность адреса доставки. Сервис доставки "Почта.России" временно недоступен.');
        }
        elseif(!$address_data) {
            $this->addError($attribute, 'Указан некорректный адрес доставки.');
        }
    }
    
    public function beforeValidate()
    {
        if(!$this->city_name_from) {
            $this->city_name_from=HRPochta::indexFromName();
        }
        
        if(!$this->city_name_to) {
            if($city=City::model()->wcolumns(['postcode'=>$this->index_to])->find()) {
                $this->city_name_to=$city->fullname;
            }
        }
        return true;
    }
    
    public function getFullName()
    {
        return trim("{$this->given_surname} {$this->given_name} {$this->given_midname}");
    }
    
    public function geOpstFullAddressTo()
    {
        return $this->city_name_to . ',' . $this->ops_address;
    }
    
    public function getFullAddressTo()
    {
        return $this->city_name_to . ',' . $this->address_street . ',' . $this->address_house . ', ' . $this->address_room;
    }
    
    public function getPaymentTypes()
    {
        return [
            RPochtaConst::PAYMENT_TYPE_CASHLESS=>RPochtaConst::i()->paymentTypeLabels(RPochtaConst::PAYMENT_TYPE_CASHLESS)
        ];
    }
    
    public function getRpoCategories()
    {
        // @FIXME поддержка всего двух категорий РПО
        if((int)HRPochta::settings()->rpochta_insr_value > 0) {
            return [
                RPochtaConst::RPO_CATEGORY_WITH_DECLARED_VALUE=>RPochtaConst::i()->rpoCategoryLabels(RPochtaConst::RPO_CATEGORY_WITH_DECLARED_VALUE)
            ];
        }
        else {
            return [
                RPochtaConst::RPO_CATEGORY_ORDINARY=>RPochtaConst::i()->rpoCategoryLabels(RPochtaConst::RPO_CATEGORY_ORDINARY)
            ];
        }
    }
    
    public function getRpoTypes()
    {
        return [
            RPochtaConst::RPO_TYPE_POSTAL_PARCEL=>RPochtaConst::i()->rpoTypeLabels(RPochtaConst::RPO_TYPE_POSTAL_PARCEL),
            RPochtaConst::RPO_TYPE_EMS=>RPochtaConst::i()->rpoTypeLabels(RPochtaConst::RPO_TYPE_EMS),
            //RPochtaConst::RPO_TYPE_EMS_OPTIMAL=>RPochtaConst::i()->rpoTypeLabels(RPochtaConst::RPO_TYPE_EMS_OPTIMAL),
        ];
    }
    
    public function statusLabels($status=false)
    {
        $labels=[
            static::STATUS_WAIT=>'Ожидает отправки в сервис Почта.России',
            static::STATUS_RPOCHTA=>'Отправлено в сервис Почта.России',
            static::STATUS_CLOSE=>'Доставлено',
            static::STATUS_REJECT=>'Отменено',
			static::STATUS_RPOCHTA_ERROR=>'Ошибка при отправке в сервис Почта.России'
        ];
        
        if($status) {
            if(isset($labels[$status])) {
                return $labels[$status];
            }
            return false;
        }
        
        return $labels;
    }
    
    public function getResultIds()
    {
        if($this->result_ids) {
            return json_decode($this->result_ids, true);
        }
        return [];
    }
    
    public function setResultIds($resultIds)
    {
        if(!is_array($resultIds)) {
            $resultIds=[];
        }
        
        $this->result_ids=json_encode($resultIds, JSON_UNESCAPED_UNICODE);
    }
    
    public function getReqData()
    {
        if($this->req_data) {
            return json_decode($this->req_data, true);
        }
        return [];
    }
    
    public function setReqData($data)
    {
        if(!is_array($data)) {
            $data=[];
        }
        
        $this->req_data=json_encode($data, JSON_UNESCAPED_UNICODE);
    }
    
    public function getItems()
    {
        if($this->items) {
            return json_decode($this->items, true);
        }
        return [];
    }
    
    public function getOpsData()
    {
        if($this->ops_data) {
            return json_decode($this->ops_data, true);
        }
        return [];
    }
    
    public function getAddressData()
    {
        if($this->address_data) {
            return json_decode($this->address_data, true);
        }
        return [];
    }
    
    public function getPriceData()
    {
        if($this->delivery_price_data) {
            return json_decode($this->delivery_price_data, true);
        }
        return [];
    }
    
    public function getScenarioByMode($mode=false)
    {
        return $this->isOpsMode($mode) ? RPochtaConst::MODE_OPS : RPochtaConst::MODE_ADDRESS;
    }
    
    public function isOpsMode($mode=false)
    {
        if($mode === false) {
            $mode=$this->delivery_mode;
        }
        
        return ($mode == RPochtaConst::MODE_OPS);
    }
}
