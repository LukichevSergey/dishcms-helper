<?php
/**
 * Настройки модуля "Точки продаж"
 * 
 */
namespace extend\modules\points\models;

use common\components\helpers\HArray as A;

class PointSettings extends \settings\components\base\SettingsModel
{
    public $id=1;
    
    /**
     * API ключ для Яндекс.Карты
     * @var unknown
     */
    public $apikey;
    
    /**
     * Иконка метки на карте
     * @var string
     */
    public $placemark_icon;
    
    /**
     * @var boolean для совместимости со старым виджетом
     * редактора admin.widget.EditWidget.TinyMCE
     */
    public $isNewRecord=false;
    
    /**
     * 
     * {@inheritDoc}
     * @see \settings\components\base\SettingsModel::behaviors()
     */
    public function behaviors()
    {
        return A::m(parent::behaviors(), [
            'placemarkIconBehavior'=>[
                'class'=>'\common\ext\file\behaviors\FileBehavior',
                'attribute'=>'placemark_icon',
                'attributeLabel'=>'Иконка метки на карте',
                'imageMode'=>true
            ]
        ]);
    }
    
    /**
     * Для совместимости со старым виджетом
     * редактора admin.widget.EditWidget.TinyMCE
     */
    public function tableName()
    {
        return 'extend_points_settings';
    }    
    
    /**
     * {@inheritDoc}
     * @see \settings\components\base\SettingsModel::rules()
     */
    public function rules()
    {
        return $this->getRules([
            ['apikey', 'safe']
        ]);
    }
    
    /**
     * {@inheritDoc}
     * @see \settings\components\base\SettingsModel::attributeLabels()
     */
    public function attributeLabels()
    {
       return $this->getAttributeLabels([
           'apikey'=>'API ключ для Яндекс.Карты'
       ]);
    }
}