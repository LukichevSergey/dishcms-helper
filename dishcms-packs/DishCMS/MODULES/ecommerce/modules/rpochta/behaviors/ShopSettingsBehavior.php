<?php
/**
 * Поведение настроек модуля Почта.России
 * 
 */
namespace rpochta\behaviors;

class ShopSettingsBehavior extends \CBehavior
{
    public $rpochta_index_from;
    public $rpochta_index_from_name;
    public $rpochta_extra_charge;
    public $rpochta_brand_name;
    public $rpochta_insr_value;
    public $rpochta_with_order_of_notice;
    public $rpochta_with_simple_notice;
    public $rpochta_wo_mail_rank;
    public $rpochta_fragile;
    public $rpochta_courier;
    public $rpochta_sms_notice_recipient;
	
	/**
	 * (non-PHPdoc)
	 * @see \settings\components\base\SettingsModel::rules()
	 */
	public function rules()
	{
		return [
            ['rpochta_index_from, rpochta_extra_charge, rpochta_insr_value', 'numerical'],
            ['rpochta_index_from_name, rpochta_brand_name', 'safe'],
            ['rpochta_with_order_of_notice, rpochta_with_simple_notice, rpochta_wo_mail_rank, rpochta_fragile, rpochta_courier', 'boolean'],
            ['rpochta_sms_notice_recipient', 'boolean']
		];
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \settings\components\base\SettingsModel::attributeLabels()
	 */
	public function attributeLabels()
	{
		return [
			'rpochta_index_from'=>'Почтовый индекс города-отправителя',
            'rpochta_index_from_name'=>'Имя города-отправителя',
            'rpochta_extra_charge'=>'Наценка на доставку',
            'rpochta_brand_name'=>'Отправитель на посылке/название брэнда',
            'rpochta_insr_value'=>'Сумма объявленной ценности (копейки)',
            'rpochta_with_order_of_notice'=>'Отметка "С заказным уведомлением"',
            'rpochta_with_simple_notice'=>'Отметка "С простым уведомлением"',
            'rpochta_wo_mail_rank'=>'Отметка "Без разряда"',
            'rpochta_fragile'=>'Отметка "Осторожно/Хрупкое"',
            'rpochta_courier'=>'Отметка "Курьер"',
            'rpochta_sms_notice_recipient'=>'Услуга SMS уведомления'
		];
	}
}
