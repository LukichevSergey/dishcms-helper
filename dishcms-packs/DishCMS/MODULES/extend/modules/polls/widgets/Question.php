<?php
/**
 * Виджет "Опрос"
 */
namespace extend\modules\polls\widgets;

use common\components\helpers\HArray as A;
use crud\models\ar\extend\modules\polls\models;

class Question extends \common\components\base\Widget
{
    /**
     * @var \crud\models\ar\extend\modules\polls\models\Question модель вопроса
     */
    public $question;    
    
    /**
     * @var string шаблон вопроса "один из"
     */
    public $radioView='question_radio';
    
    /**
     * @var string шаблон вопроса "несколько из"
     */
    public $checkboxView='question_checkbox';
    
    /**
     * @var boolean подключить inline-шаблоны 
     */
    public $inline=true;
    
    /**
     * Текущая модель опроса
     * @var \crud\models\ar\extend\modules\polls\models\Poll|NULL
     */
    private $poll=null;
    
    /**
     * Варинаты ответа для текущего вопроса
     * @var array|NULL
     */
    private $answers=null;
    
    /**
     *
     * {@inheritDoc}
     * @see \common\components\base\Widget::run()
     */
    public function run()
    {
        if($this->getQuestion()) {
            $this->render($this->getView());
        }
    }
    
    /**
     * Получить модель опроса
     * @return \crud\models\ar\extend\modules\polls\models\Poll
     */
    public function getPoll()
    {
        if($this->poll === null) {
            $this->poll=$this->getQuestion()->getRelated('poll', ['scopes'=>'published']);
        }
        
        return $this->poll;
    }
    
    /**
     * Получить модуль текущего вопроса
     * @return \crud\models\ar\extend\modules\polls\models\Question
     */
    public function getQuestion()
    {
        return $this->question;
    }
    
    /**
     * Текущий вопрос является обязательным
     * @return boolean
     */
    public function isRequired()
    {
        return (bool)$this->question->required;
    }
    
    /**
     * Получить текст вопроса
     * @param array $answer элемент ответа
     * @return string
     */
    public function getQuestionTitle()
    {
        return $this->getQuestion()->title;
    }    
    
    /**
     * Получить текст ответа
     * @param array $answer элемент ответа
     * @return string
     */
    public function getAnswerTitle($answer)
    {
        return A::get($answer, 'title');
    }    
    
    /**
     * Получить имя шаблона отображения
     * @return string
     */
    public function getView()
    {
        $view=$this->radioView;
        if($this->getQuestion()->multiple) {
            $view=$this->checkboxView;
        }
        
        if($this->inline) {
            $inline=true;
            foreach($this->getAnswers() as $answer) {
                $inline=$inline && is_numeric($answer->label);
            }
            if($inline) {
                $view.='_inline';
            }
        }
        
        return $view;
    }    
    
    /**
     * Получить ответы текущего вопроса
     * @return array
     */
    public function getAnswers()
    {
        if($this->answers === null) {
            $answers=$this->getQuestion()->answersBehavior->get(true);
            $this->answers=[];
            foreach($answers as $answer) {
                $answer['label']=$this->getAnswerTitle($answer);
                $answer['fieldName']=$this->getAnswerFieldName($answer);
                $answer['fieldId']=$this->getAnswerFieldId($answer);
                $this->answers[]=(object)$answer;
            }
        }
        
        return $this->answers;
    }
    
    /**
     * Получить имя поля ответа
     * @param array $answer элемент ответа
     * @return string
     */
    public function getAnswerFieldName($answer)
    {
        return $this->question->getHash() .  ($this->question->multiple ? '[]' : '');
    }
    
    /**
     * Получить идентификатор поля ответа
     * @param array $answer элемент ответа
     * @return string
     */
    public function getAnswerFieldId($answer)
    {
        return $this->question->getHash() . '_' . A::get($answer, 'hash', md5($this->getAnswerTitle($answer)));
    }
}