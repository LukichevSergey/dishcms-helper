<?php
/**
 * Поведение настроек модуля Почта.России
 * 
 */
namespace pecom\behaviors;

class ShopSettingsBehavior extends \CBehavior
{
    public $pecom_take_town;
	
	/**
	 * (non-PHPdoc)
	 * @see \settings\components\base\SettingsModel::rules()
	 */
	public function rules()
	{
		return [
            ['pecom_take_town', 'numerical'],
		];
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \settings\components\base\SettingsModel::attributeLabels()
	 */
	public function attributeLabels()
	{
		return [
			'pecom_take_town'=>'Город отправитель',
		];
	}
}
