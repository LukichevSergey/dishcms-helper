<?php
/**
 * 
 * 
 */
namespace extend\modules\buildings\models;

use common\components\helpers\HArray as A;

class Settings extends \settings\components\base\SettingsModel
{
    public $id = 1;
    public $disabled = 1;
    public $facade_image;
    public $facade_svg;
    public $text;
    public $text_bottom;
    public $isNewRecord = false;
    
    /**
     * Для совместимости со старым виджетом
     * редактора admin.widget.EditWidget.TinyMCE
     */
    public function tableName()
    {
        return 'buildings_settings';
    }
    
    /**
	 * (non-PHPdoc)
	 * @see \common\components\base\FormModel::behaviors()
	 */
	public function behaviors()
	{
		return A::m(parent::behaviors(), [
		    'imageBehavior'=>[
		        'class'=>'\common\ext\file\behaviors\FileBehavior',
		        'attribute'=>'facade_image',
		        'attributeLabel'=>'Карта фасада',
		        'imageMode'=>true
		    ],
		    'svgBehavior'=>[
		        'class'=>'\common\ext\file\behaviors\FileBehavior',
		        'attribute'=>'facade_svg',
		        'attributeLabel'=>'SVG для карты фасада',
		        'types'=>'svg'
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
		    ['disabled, text, text_bottom', 'safe']
		]);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \settings\components\base\SettingsModel::attributeLabels()
	 */
	public function attributeLabels()
	{
		return $this->getAttributeLabels([
		    'disabled' => 'Отключить модуль "Планировки" для посетителей сайта.',
		    'text' => 'Tекст на странице "Планировки"',
		    'text_bottom' => 'Нижний текст на странице "Планировки"',
		]);
	}
}
