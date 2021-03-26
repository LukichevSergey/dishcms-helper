<?php
/**
 * Виджет поля выбора ПВЗ для доставки СДЭК
 */
namespace cdek\widgets;

use common\components\helpers\HYii as Y;

class PvzField extends \common\components\widgets\form\BaseField
{
    /**
     * @var string значение атрибута "data-js" для выбора элемента открытия окна выбора ПВЗ
     */
    public $jPvzButton=false;
    
    /**
     * @var string значение атрибута "data-js" для выбора элемента открытия окна выбора ПВЗ по умолчанию.
     */
    public $defaultJPvzButton='default-cdek-pvz-button';
    
    
    /**
     * @var string $jPvzContent выражение выборки элемента выбора ПВЗ
     */
    public $jPvzContent='[data-js=\'cdek-pvz\']';
    
    /**
     * @var string $jPvzMap выражение выборки элемента в котором будет отображена карта ПВЗ
     */
    public $jPvzMap='#js-cdek-pvz-map';
    
    /**
     * @var string $urlGetPvzList URL получения списка ПВЗ
     */
    public $urlGetPvzList='/ecommerce/cdek/pvz/list';    
    
    /**
     * @var string $view шаблон отображения
     */
    public $view='pvz_field';
    
    /**
	 * {@inheritDoc}
	 * @see CWidget::init()
	 */
    public function init()
    {
        parent::init();
        
        $this->publish();
    }
    
    /**
	 * {@inheritDoc}
	 * @see CWidget::run()
	 */
    public function run()
    {
        if(!$this->jPvzButton) {
            $this->jPvzButton=$this->defaultJPvzButton;
        }
        
        $options=[
            'model_name'=>'cdek_models_Order',
            'urlGetPvzList'=>$this->urlGetPvzList,
            'jPvzButton'=>$this->jPvzButton,
            'jPvzContent'=>$this->jPvzContent,
            'jPvzMap'=>$this->jPvzMap,
        ];
        
        Y::jsFile('https://api-maps.yandex.ru/2.1/?lang=ru_RU', \CClientScript::POS_HEAD);
        Y::js(false, ';window.cdek_widgets_PvzField.init('.json_encode($options).');', \CClientScript::POS_READY);
        
        parent::run();
    }
}
