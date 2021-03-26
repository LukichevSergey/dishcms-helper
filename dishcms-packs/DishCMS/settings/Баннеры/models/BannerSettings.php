<?php
/**
 * Настройки баннеров. 
 *
 */
use common\components\helpers\HArray as A;

class BannerSettings extends \settings\components\base\SettingsModel 
{
	/**
	 * @var boolean отображать на сайте
	 */
	public $main_active=false;
	
	/**
	 * @var string текст баннера на главной. 
	 */ 
	public $main_text;
	
	/**
	 * @var string ссылка баннера на главной. 
	 */ 
	public $main_url;
	
	/**
	 * @var string подпись ссылки баннера на главной. 
	 */ 
	public $main_url_label;
	
	/**
	 * @var string имя файла изображения баннера.
	 */
	public $main_image;
	
	/**
	 * @var boolean активировать изображение.
	 */
	public $main_image_enable=1;

	/**
	 * @var boolean для совместимости со старым виджетом 
	 * редактора admin.widget.EditWidget.TinyMCE
	 */
	public $isNewRecord=false;
	
	/**
	 * Для совместимости со старым виджетом 
	 * редактора admin.widget.EditWidget.TinyMCE
	 */
	public function tableName()
	{
		return 'banner_settings';
	}
		
	/**
	 * (non-PHPdoc)
	 * @see \settings\components\base\SettingsModel::behaviors()
	 */
	public function behaviors()
	{
		return A::m(parent::behaviors(), [
			'mainImageBehavior'=>[
    			'class'=>'\common\ext\file\behaviors\FileBehavior',
    			'attribute'=>'main_image',
    			'attributeLabel'=>'Изображение',
    			'attributeEnable'=>'main_image_enable',
    			'attributeEnableLabel'=>'Отображать на сайте',
				'enableValue'=>true,
    			'imageMode'=>true 
    		],	
		]);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \settings\components\base\SettingsModel::rules()
	 */
	public function rules()
	{
		return $this->getRules([
			['main_active, main_text, main_url, main_url_label', 'safe']
		]);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \settings\components\base\SettingsModel::attributeLabels()
	 */
	public function attributeLabels()
	{
		return $this->getAttributeLabels([
			'main_active'=>'Отображать на сайте',	
			'main_text'=>'Текст',	
			'main_url'=>'Ссылка',	
			'main_url_label'=>'Подпись ссылки',	
		]);
	}
}