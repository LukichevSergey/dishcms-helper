<?php
namespace extend\modules\points\widgets;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;

/**
 * Виджет формы поиска для Яндекс.Карты
 * 
 * Для интеграции с виджетом \common\ext\ymap\widgets\YMap
 * необходимо передать в параметрах YMap
 *  'onAfterInit'=>'js:window.extendPointsWidgetsSearchForm.onAfterInitYMap' 
 */
class SearchForm extends \common\components\base\Widget
{
    /**
     * Идентификатор Яндекс.Карты
     * @var string
     */
    public $map;
    
    /**
     * Дополнительные HTML атрибуты для тэга формы
     * @var array
     */
    public $formOptions=[];
    
    /**
     * Подпись кнопки поиска.
     * Если передано FALSE, кнопка отображена не будет.
     * @var string
     */
    public $submitLabel='Найти';
    
    /**
     * Дополнительные HTML атрибуты кнопки поиска  
     * @var string
     */
    public $submitOptions=[];
    
    /**
     * Дополнительные HTML атрибуты контейнера результатов
     * @var array
     */
    public $resultOptions=[
        'class'=>'points__search-results'
    ];
    
    /**
     * Подпись кнопки перехода к точке указанного адреса
     * Если передано FALSE, кнопка отображена не будет.    
     * @var string|false
     */
    public $gotoLabel='Перейти';
    
    /**
     * Дополнительные HTML атрибуты кнопки перехода 
     * к точке указанного адреса.
     * @var array
     */
    public $gotoOptions=[];
    
    /**
     * Опубликовать CSS стили.  
     * @var boolean
     */
    public $css=true;
    
    /**
     * 
     * {@inheritDoc}
     * @see \common\components\base\Widget::$view
     */
    public $view='search_form';
    
    /**
     * 
     * {@inheritDoc}
     * @see \common\components\base\Widget::run()
     */
    public function run()
    {
        $this->publish(true, (bool)$this->css);
        
        $this->render($this->view, $this->params);
    }
    
    /**
     * Получить дополнительные HTML атрибуты с дополнительным
     * js-{mapId}-{name} значением в атрибуте "class". 
     * @param string $name имя "js-" css класса
     * @param array $htmlOptions
     * @return array
     */
    public function getHtmlOptionsWithJs($name, $htmlOptions=[])
    {
        $cssClass=A::get($htmlOptions, 'class', '');
        $cssClass.=" js-{$this->map}-{$name}";
        
        $htmlOptions['class']=trim($cssClass);
        
        return  $htmlOptions;
    }
    
    /**
     * Получить дополнительные HTML атрибуты поля ввода
     * @return array
     */
    public function getInputHtmlOptions()
    {
        return $this->getHtmlOptionsWithJs('points-search-input', $this->htmlOptions);
    }
    
    /**
     * Получить дополнительные HTML атрибуты кнопки отправки формы
     * @return array
     */
    public function getSubmitHtmlOptions()
    {
        $this->submitOptions['type']='submit';
        
        return $this->getHtmlOptionsWithJs('points-search-submit', $this->submitOptions);
    }
    
    /**
     * Получить дополнительные HTML атрибуты контейнера результатов
     * @return array
     */
    public function getResultHtmlOptions()
    {
        return $this->getHtmlOptionsWithJs('points-search-result', $this->resultOptions);
    }
    
    /**
     * Получить дополнительные HTML атрибуты кнопки перехода 
     * к точке указанного адреса.
     * @return array
     */
    public function getGotoHtmlOptions()
    {
        return $this->getHtmlOptionsWithJs('points-search-goto', $this->gotoOptions);
    }
}