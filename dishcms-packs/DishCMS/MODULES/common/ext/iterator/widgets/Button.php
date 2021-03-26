<?php
/**
 * Кнопка запуска пошагового процесса c индикатором процесса.
 */
namespace common\ext\iterator\widgets;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HHash;
use common\ext\iterator\models\Config;

class Button extends \common\ext\iterator\components\base\Widget
{
    /**
     * Подпись кнопки
     * @var string
     */
    public $label;
    
    /**
     * Ajax URL выполнения процесса.
     * Запрос на данный URL должен возвращать данные в JSON формате
     * {
     *     success: boolean, 
     *     data: {
     *         hash: (string) хэш процесса при создании нового процесса, 
     *         percent: (float) процент выполненности процесса, при последующих итерациях. 
     *         params: (mixed) дополнительные параметры, которые будут переданы в следующий запрос
     *     },
     *     при возникновении ошибок
     *     errors: { 
     *         code: message
     *     }
     * }
     * 
     * Данные параметр можно не указывать, если задан параметр $iterator.
     * @var string
     */
    public $url;
    
    /**
     * Идентификатор конфигурации итератора. 
     * @var string
     */
    public $iterator;
    
    /**
     * Дополнительные параметры для запроса
     * @var array
     */
    public $data=[];
    
    /**
     * Имя переменной хэша.
     * По умолчанию "h".
     * @var string
     */
    public $hashVar='h';
    
    /**
     * Имя переменной в которой передаются дополнительные параметры.
     * По умолчанию "ipm".
     * @var string
     */
    public $paramsVar='ipm';
    
    /**
     * Дополнительный javascript код в тело функции обработчика шага итерации.
     * Внутри функции будет доступна переменная "response" в которой будет 
     * содержаться результат запроса.
     * @var array
     */
    public $jsProcess='';
    
    /**
     * Дополнительный javascript код в тело функции обработчика завершения процесса.
     * Внутри функции будет доступна переменная "response" в которой будет 
     * содержаться результат запроса.
     * @var array
     */
    public $jsDone='';
    
    /**
     * Дополнительный javascript код в тело функции обработчика ошибки итерации.
     * Внутри функции будет доступна переменная "response" в которой будет 
     * содержаться результат запроса.
     * @var array
     */
    public $jsError='';
    
    /**
     * Задержка перед следующей итерацией в милисекундах.
     * По умолчанию 100.
     * @var integer
     */
    public $delay=100;
    
    /**
     * Текст кнопки в момент выполнения процесса
     * @var string
     */
    public $loadingText='Процесс выполняется...';
    
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
     * 
     * {@inheritDoc}
     * @see \common\components\base\Widget::$view
     */
    public $view='button';
    
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
        
        $this->htmlOptions['class']=A::get($this->htmlOptions, 'class', '') . ' ' . $this->getJsId();
        
        if(empty($this->htmlOptions['data-loading-text'])) {
            $this->htmlOptions['data-loading-text']=$this->loadingText;
        }
        
        
        $this->initIterator();
        
        $this->publishMainAssets();
        
        $this->registerScripts();
    }
        
    /**
     * Получить javascript идентификатор
     * @return string
     */
    public function getJsId()
    {
        return $this->jsId;
    }
    
    /**
     * Инициализация конфигурации итератора
     */
    protected function initIterator()
    {
        if($this->iterator) {
            $config=new Config;
            
            if($config->load($this->iterator)) {
                if(!$this->url) {
                    $this->url='/common/crud/admin/default/ajax?cid=common_ext_iterator_iterator&action=run';
                }
                
                $this->data[Config::CONFIG_HASH_VAR]=$config->getConfigHash();
                
                $this->hashVar=$config->getHashVar();
            }
        }
    }
    
    /**
     * Регистрация дополнительных скриптов виджета
     */
    protected function registerScripts()
    {
        $jsId=$this->getJsId();
        
        // запуск процесса по клику на кнопку
        $jsCode="$(document).on('click','.{$jsId}',function(e){e.preventDefault();$('.{$jsId}').button('loading');"
        . "window.cmsCommonExtIterator.next('{$this->url}',".json_encode($this->data).",{"
            . "hashVar:'{$this->hashVar}',"
            . "paramsVar:'{$this->paramsVar}',"
            . "progress:'.{$jsId}-progress',"
            . "progressbar:'.{$jsId}-progress-bar',"
            . "process:function(response){{$this->jsProcess}},"
            . "done:function(response){{$this->jsDone};$('.{$jsId}').button('reset');},"
            . "error:function(response){{$this->jsError};$('.{$jsId}').button('reset');},"
            . "delay:{$this->delay}"
            . "});"
            . "return false;});";
            
        Y::js($jsId, $jsCode, \CClientScript::POS_READY);
    }
}