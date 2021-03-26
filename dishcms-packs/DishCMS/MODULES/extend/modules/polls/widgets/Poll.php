<?php
/**
 * Виджет "Опрос"
 */
namespace extend\modules\polls\widgets;

use crud\models\ar\extend\modules\polls\models;

class Poll extends \common\components\base\Widget
{
    /**
     * @var integer идентификатор опроса
     */
    public $id;
    
    /**
     * {@inheritDoc}
     * @see \common\components\base\Widget::$view
     */
    public $view='poll_default';
    
    /**
     * Текущая модель опроса
     * @var \crud\models\ar\extend\modules\polls\models\Poll|NULL
     */
    private $poll=null;
    
    /**
     * Модели вопросов текущей модели опроса
     * @var \crud\models\ar\extend\modules\polls\models\Question[]|NULL
     */
    private $questions=null;
    
    /**
     * 
     * {@inheritDoc}
     * @see \common\components\base\Widget::run()
     */
    public function run()
    {
        if($this->getPoll()) {
            $this->publish(true, 'styles.less');
            parent::run();
        }
    }
    
    /**
     * Получить модель опроса
     * @return \crud\models\ar\extend\modules\polls\models\Poll
     */
    public function getPoll()
    {
        if($this->poll === null) {
            $this->poll=models\Poll::modelById($this->id, ['scopes'=>'published']);
        }
        
        return $this->poll;
    }
    
    /**
     * Получить модели вопросов текущей модели опроса
     * @return \crud\models\ar\extend\modules\polls\models\Question[]|NULL
     */
    public function getQuestions()
    {
        if($this->questions === null) {
            $this->questions=$this->getPoll()->getRelated('questions',['scope'=>'published']);
        }
        
        return $this->questions;
    }
}