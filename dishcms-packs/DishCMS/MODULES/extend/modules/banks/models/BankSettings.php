<?php
/**
 * Настройки модуля Банки.
 * 
 */
namespace extend\modules\banks\models;

use common\components\helpers\HArray as A;

class BankSettings extends \settings\components\base\SettingsModel
{
	/**
	 * SEO
	 */
	public $meta_title;
	public $meta_desc;
	public $meta_key;
	public $meta_h1;
	
	/**
	 * @var string текст на главной странице
	 */
	public $main_text;
	
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
		return 'bank_settings';
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \common\components\base\FormModel::behaviors()
	 */
	public function behaviors()
	{
		return A::m(parent::behaviors(), [
		]);
	}

	/**
	 * (non-PHPdoc)
	 * @see \settings\components\base\SettingsModel::rules()
	 */
	public function rules()
	{
		return $this->getRules([
			['meta_h1,meta_title,meta_key,meta_desc,main_text', 'safe']
		]);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \settings\components\base\SettingsModel::attributeLabels()
	 */
	public function attributeLabels()
	{
		return $this->getAttributeLabels([
			'meta_h1'=>'H1',
			'meta_title' => 'Meta Title',
			'meta_key' => 'Meta Keywords',
			'meta_desc' => 'Meta Description',
			'main_text'=>'Текст на главной странице',
		]);
	}
}