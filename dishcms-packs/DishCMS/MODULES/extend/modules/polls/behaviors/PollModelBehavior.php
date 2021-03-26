<?php
/**
 * Поведение модели "Опросы и голосования"
 *
 */
namespace extend\modules\polls\behaviors;

use common\components\helpers\HDb;
use crud\models\ar\extend\modules\polls\models\Question;
use crud\models\ar\extend\modules\polls\models\Result;

class PollModelBehavior extends \CBehavior
{
    /**
     * {@inheritDoc}
     * @see \CBehavior::events()
     */
    public function events()
    {
        return [
            'onBeforeValidate'=>'beforeValidate',
            'onBeforeSave'=>'beforeSave',
            'onAfterDelete'=>'afterDelete',
        ];
    }
    
    /**
     * Правила валидации модели
     * @return array
     */
    public function rules()
    {
        return [
            ['title', 'required'],
            ['sort', 'numerical', 'integerOnly'=>true],
            ['finish_time, text', 'safe']
        ];
    }
    
    public function beforeValidate()
    {
        return true;
    }
    
    public function beforeSave()
    {
        if($this->owner->isNewRecord) {
            if(!$this->owner->sort) {
                $query='SELECT MAX(`sort`) + 10 FROM ' . HDb::qt($this->owner->tableName()) . ' WHERE 1=1';
                $this->owner->sort=(int)HDb::queryScalar($query);
            }
        }
        
        return true;
    }
    
    public function afterDelete()
    {
        if($models=Question::model()->findAllByAttributes(['poll_id'=>$this->owner->id])) {
            foreach($models as $model) {
                $model->delete();
            }
        }
        
        return true;
    }
    
    public function updateStats($pollId=null) 
    {
	if($pollId === null) {
	    $pollId=$this->owner->id;
	}
	
	$stats=[];
        if($results=Result::model()->findAllByAttributes(['poll_id'=>$pollId])) {
            foreach($results as $result) {
                if(!isset($stats[$result->question_id][$result->answer_hash])) {
                    $stats[$result->question_id][$result->answer_hash]=1;
                }
                else {
                    $stats[$result->question_id][$result->answer_hash]++;
                }
            }
        }
            
        if($questions=Question::model()->findAllByAttributes(['poll_id'=>$pollId])) {
            foreach($questions as $question) {
                $answers=$question->answersBehavior->get();
                foreach($answers as $idx=>$answer) {
                    if(isset($stats[$question->id][$answer['hash']])) {
                        $answer['votes']=$stats[$question->id][$answer['hash']];
                    }
                    else {
                        $answer['votes']=0;
                    }
                    $answers[$idx]=$answer;
                }
                $question->answers=$answers;
                $question->save();
            }
        }
        
        return true;
    }
}