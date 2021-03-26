<?php
/**
 * Виджет поля выбора ОПС для доставки через сервис Почта России
 */
namespace rpochta\widgets;

use common\components\helpers\HYii as Y;

class OpsField extends \common\components\widgets\form\BaseField
{
    /**
     * @var string имя атрибута почтового индекса ОПС
     */
    public $attributeIndex='ops_index';
    
    public $attributeLongitude='ops_longitude';
    public $attributeLatitude='ops_latitude';
    
    /**
     * @var string значение атрибута "data-js" для выбора элемента открытия окна выбора ОПС
     */
    public $jOpsButton=false;
    
    /**
     * @var string значение атрибута "data-js" для выбора элемента открытия окна выбора ОПС по умолчанию.
     */
    public $defaultJOpsButton='default-rpochta-ops-button';
    
    
    /**
     * @var string $jPvzContent выражение выборки элемента выбора ОПС
     */
    public $jOpsContent='[data-js=\'rpochta-ops\']';
    
    /**
     * @var string $jOpsMap выражение выборки элемента в котором будет отображена карта ОПС
     */
    public $jOpsMap='#js-rpochta-ops-map';
    
    /**
     * @var string $urlGetOpsList URL получения списка ОПС
     */
    public $urlGetOpsList='/ecommerce/rpochta/ops/list';    
    
    /**
     * @var string $view шаблон отображения
     */
    public $view='ops_field';
    
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
        if(!$this->jOpsButton) {
            $this->jOpsButton=$this->defaultJOpsButton;
        }
        
        $options=[
            'model_name'=>'rpochta_models_Order',
            'urlGetOpsList'=>$this->urlGetOpsList,
            'jOpsButton'=>$this->jOpsButton,
            'jOpsContent'=>$this->jOpsContent,
            'jOpsMap'=>$this->jOpsMap,
        ];
        
        Y::jsFile('https://api-maps.yandex.ru/2.1/?lang=ru_RU', \CClientScript::POS_HEAD);
        Y::js(false, ';window.rpochta_widgets_OpsField.init('.json_encode($options).');', \CClientScript::POS_READY);
        
        parent::run();
    }
}
