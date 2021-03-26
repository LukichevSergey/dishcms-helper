<?php
/**
 * Модель
 */
namespace cdek\models;

use common\components\helpers\HArray as A;
use cdek\models\Tariff;

class Order extends \common\components\base\ActiveRecord
{
    const STATUS_WAIT=1;
    const STATUS_CDEK=10;
    const STATUS_CLOSE=20;
    const STATUS_REJECT=30;

	const STATUS_CDEK_ERROR=110;
    
    /**
     * @var integer режим доставки
     */
    public $delivery_mode;
    
	/**
	 * (non-PHPdoc)
	 * @see \CActiveRecord::tableName()
	 */
	public function tableName()
	{
		return 'cdek_orders';
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
            'sendCity'=>[\CActiveRecord::HAS_ONE, '\cdek\models\City', ['send_city_id'=>'cdek_id']],
            'recCity'=>[\CActiveRecord::HAS_ONE, '\cdek\models\City', ['rec_city_id'=>'cdek_id']],
		]);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CModel::rules()
	 */
	public function rules()
	{
		return $this->getRules([
            ['send_city_id, rec_city_id', 'required'],
            ['pvz_code', 'required', 'on'=>'pvz', 'message'=>'Необходимо выбрать ПВЗ'],
            ['address_street, address_house, address_flat', 'required', 'on'=>'address'],
            ['send_city_id, rec_city_id', 'safe'],
            ['pvz_code, pvz_data', 'safe'],
            ['address_street, address_house, address_flat', 'safe'],
            ['send_city_name, send_city_postcode, rec_city_name, rec_city_postcode, rec_name, rec_email, rec_phone, tariff_id', 'safe'],
            ['package_number, package_barcode, package_weight, items, delivery_price, delivery_extra_charge, info, rec_phone, comment, status', 'safe'],
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
            'dispatch_number'=>'Номер накладной',            
            'send_city_id'=>'Идентификатор города-отправителя в базе СДЭК',
            'send_city_name'=>'Имя города-отправителя',
            'send_city_postcode'=>'Почтовый индекс города-отправителя в базе СДЭК',            
            'rec_city_id'=>'Идентификатор города-получателя в базе СДЭК',
            'rec_city_name'=>'Имя города-получателя',
            'rec_city_postcode'=>'Почтовый индекс города-получателя в базе СДЭК',
            'rec_name'=>'ФИО получателя',
            'rec_email'=>'EMail получателя',
            'rec_phone'=>'Телефон получателя',            
            'tariff_id'=>'Идентфикатор тарифа',
            'pvz_code'=>'Код ПВЗ',
            'pvz_data'=>'Дополнительные данные о ПВЗ',
            'address_street'=>'Улица',
            'address_house'=>'Номер дома',
            'address_flat'=>'Номер квартиры',            
            'package_number'=>'Номер упаковки',
            'package_barcode'=>'Код упаковки',
            'package_weight'=>'Общий вес в граммах',            
            'items'=>'Товары',            
            'delivery_price'=>'Стоимость доставки',
            'delivery_extra_charge'=>'Наценка',
            'info'=>'Дополнительная информация о доставке',
            'comment'=>'Комментарий',
            'status'=>'Статус доставки',
            'create_time'=>'Дата создания записи',
            'update_time'=>'Дата обновления записи',
		]);
	}
    
    public function statusLabels($status=false)
    {
        $labels=[
            self::STATUS_WAIT=>'Ожидает отправки в СДЭК',
            self::STATUS_CDEK=>'Отправлено в СДЭК',
            self::STATUS_CLOSE=>'Доставлено',
            self::STATUS_REJECT=>'Отменено',
			self::STATUS_CDEK_ERROR=>'Ошибка при отправке в СДЭК'
        ];
        
        if($status) {
            if(isset($labels[$status])) {
                return $labels[$status];
            }
            return false;
        }
        
        return $labels;
    }
    
    public function getItems()
    {
        if($this->items) {
            return json_decode($this->items, true);
        }
        return [];
    }
    
    public function getPvzData()
    {
        if($this->pvz_data) {
            return json_decode($this->pvz_data, true);
        }
        return [];
    }
    
    public function getInfo()
    {
        if($this->info) {
            return json_decode($this->info, true);
        }
        return [];
    }
    
    public function getScenarioByMode($mode=false)
    {
        return $this->isPvzMode($mode) ? 'pvz' : 'address';
    }
    
    public function isPvzMode($mode=false)
    {
        if($mode === false) {
            $mode=$this->delivery_mode;
        }
        $mode=(int)$mode;
        
        return in_array($mode, [Tariff::MODE_SS, Tariff::MODE_DS]);
    }
}
