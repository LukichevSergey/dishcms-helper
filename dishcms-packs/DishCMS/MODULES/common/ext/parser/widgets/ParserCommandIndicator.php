<?php
namespace common\ext\parser\widgets;

use common\components\helpers\HHash;
use common\ext\parser\models\Config;
use crud\models\ar\common\ext\parser\models\Parser;

/**
 * Виджет индикатора завершенности процесса, запущенного из консоли.
 *
 */
class ParserCommandIndicator extends \common\components\base\Widget
{
    /**
     * Процесс является периодическим
     * @var string
     */
    public $isPeriodic=false;
    
    /**
     * Подпись 
     * @var string
     */
    public $label;
    
    /**
     * Интервал в милисекундах проверки значения процента завершенности процесса парсинга.
     * По умолчанию 10000 (10 сек)
     * @var integer
     */
    public $delay=10000;
    
    /**
     * Дополнительные HTML атрибуты подписи кнопки
     * @var array
     */
    public $labelOptions=[];
    
    /**
     * Псевдоним пути к конфигурации парсера
     * @var string
     */
    public $config;
    
    /**
     * Дополнительные атрибуты для элемента обертки индикатора прогресса.
     * @var array
     */
    public $progressOptions=[];
    
    /**
     * Дополнительные атрибуты для элемента индикатора прогресса.
     * @var array
     */
    public $progressBarOptions=[];
    
    /**
     * Тэг тестового сообщения прогресса
     * @var string
     */
    public $textTag='span';
    
    /**
     * Дополнительные атрибуты для элемента тестового сообщения прогресса
     * @var array
     */
    public $textOptions=[];
    
    /**
     * Дополнительный текст
     * @var string
     */
    public $note='';
    
    /**
     * Тэг для обертки дополнительного текста
     * @var string
     */
    public $noteTag='div';
    
    /**
     * Дополнительные атрибуты для элемента обертки дополнительного текста
     * @var array
     */
    public $noteOptions=[];
    
    /**
     * {@inheritDoc}
     * @see \common\components\base\Widget::$view
     */
    public $view='parse_indicator';
    
    /**
     * Javascript идентификатор
     * @access protected
     * @var string
     */
    protected $jsId;
    
    /**
     *
     * {@inheritDoc}
     * @see \common\components\base\Widget::init()
     */
    public function init()
    {
        parent::init();
        
        $this->jsId=HHash::ujs();
        $this->isPeriodic=(int)$this->isPeriodic;
    }
    
    /**
     * Получить хэш конфигурации парсера
     * @return string
     */
    public function getConfigHash()
    {
        if($config=Config::load($this->config)) {
            return $config->getConfigHash();
        }
        
        return '';
    }
    
    /**
     * Получить javascript идентификатор
     * @return string
     */
    public function getJsId()
    {
        return $this->jsId;
    }
}
