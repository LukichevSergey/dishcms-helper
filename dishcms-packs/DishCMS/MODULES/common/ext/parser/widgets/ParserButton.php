<?php
namespace common\ext\parser\widgets;

use common\ext\parser\components\helpers\HParser;
use common\ext\parser\models\Config as ParserConfig;
use common\ext\iterator\models\Config as IteratorConfig;

class ParserButton extends \common\ext\iterator\widgets\Button
{
    /**
     * Псевдоним пути к конфигурации парсера
     * @var string
     */
    public $config;
    
    /**
     *
     * {@inheritDoc}
     * @see \common\ext\iterator\widgets\Button::$view
     */
    public $view='common.ext.iterator.widgets.views.button';
    
    /**
     * Инициализация конфигурации итератора
     */
    protected function initIterator()
    {
        $parserConfig=ParserConfig::load($this->config);
        
        $iteratorConfig=new IteratorConfig;
        
        if($this->iterator) {
            $iteratorConfig->load($this->iterator);
        }
        else {
            $iteratorConfig->setId('iterator');
            $iteratorConfig->setPath($this->config);
            $iteratorConfig->setConfig($parserConfig->getIteratorConfig());
            $iteratorConfig->init();
        }
        
        if(!$this->url) {
            $this->url=HParser::getRunParserUrl();
        }
        
        $this->data[ParserConfig::CONFIG_HASH_VAR]=$parserConfig->getConfigHash();
        
        $this->hashVar=$iteratorConfig->getHashVar();
    }
}