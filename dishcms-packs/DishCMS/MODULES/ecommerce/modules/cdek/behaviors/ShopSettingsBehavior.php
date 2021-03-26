<?php
/**
 * Поведение настроек модуля СДЭК
 * 
 */
namespace cdek\behaviors;

class ShopSettingsBehavior extends \CBehavior
{
    public $cdek_tariff_group;
    public $cdek_send_city_id;
	public $cdek_extra_charge;
    public $cdek_seller_name;
    public $cdek_package_item_cost;
    public $cdek_ymap_apikey;
    
	/**
	 * (non-PHPdoc)
	 * @see \settings\components\base\SettingsModel::rules()
	 */
	public function rules()
	{
		return [
            ['cdek_tariff_group, cdek_send_city_id, cdek_extra_charge, cdek_package_item_cost', 'numerical'],
            ['cdek_seller_name, cdek_ymap_apikey', 'safe']
		];
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \settings\components\base\SettingsModel::attributeLabels()
	 */
	public function attributeLabels()
	{
		return [
            'cdek_tariff_group'=>'Группа тарифов по которым происходит рассчет доставки',
			'cdek_send_city_id'=>'Идентификатор города-отправителя в сервисе СДЭК',
			'cdek_extra_charge'=>'Наценка на доставку',
            'cdek_seller_name'=>'Наименование компании',
            'cdek_package_item_cost'=>'Объявленная стоимость товара',
		    'cdek_ymap_apikey'=>'API ключ для Яндекс.Карты при выборе ПВЗ'
		];
	}
}
