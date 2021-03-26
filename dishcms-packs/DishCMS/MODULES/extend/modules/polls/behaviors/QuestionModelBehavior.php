<?php
/**
 * Поведение модели "Опросы и голосования"
 *
 */
namespace extend\modules\polls\behaviors;

use common\components\helpers\HDb;
use common\components\helpers\HHash;
use crud\models\ar\extend\modules\polls\models\Question;
use crud\models\ar\extend\modules\polls\models\Result;

class QuestionModelBehavior extends \CBehavior
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
            ['can_other, multiple, required', 'boolean'],
            ['text', 'safe'],
        ];
    }
    
    /**
     * onBeforeValidate
     * @return boolean
     */
    public function beforeValidate()
    {
        return true;
    }
    
    /**
     * onBeforeSave
     * @return boolean
     */
    public function beforeSave()
    {
        if($this->owner->isNewRecord) {
            if(!$this->owner->sort) {
                $query='SELECT MAX(`sort`) + 5 FROM ' . HDb::qt($this->owner->tableName()) . ' WHERE 1=1';
                $this->owner->sort=(int)HDb::queryScalar($query);
            }
        }
        
        $data=$this->owner->answersBehavior->get();
        foreach($data as $key=>$item) {
            if(empty($data[$key]['votes'])) $data[$key]['votes']=(string)0;
            if(empty($data[$key]['hash'])) $data[$key]['hash']=HHash::u();
        }
        $this->owner->answers=$data;
        
        return true;
    }
    
    /**
     * onAfterDelete
     * @return boolean
     */
    public function afterDelete()
    {
        Result::model()->deleteAllByAttributes(['question_id'=>$this->owner->id]);
        
        return true;
    }
    
    /**
     * Получить хэш вопроса
     * @return string
     */
    public function getHash()
    {
        return sha1(Question::CLASS_HASH . '_' . $this->owner->id . '_' . $this->owner->poll_id);
    }
}