<?php
/**
 * Поведение модели "Опросы и голосования"
 *
 */
namespace extend\modules\polls\behaviors;

use common\components\helpers\HDb;
use crud\components\helpers\HCrud;
use extend\modules\polls\components\helpers\HPoll;
use crud\models\ar\extend\modules\polls\models\Poll;
use crud\models\ar\extend\modules\polls\models\Question;

class ResultModelBehavior extends \CBehavior
{
    /**
     * {@inheritDoc}
     * @see \CBehavior::events()
     */
    public function events()
    {
        return [
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
            ['poll_id, question_id, answer_hash', 'required'],
            ['poll_id, question_id, answer_hash, user_hash', 'numerical', 'integerOnly'=>true]
        ];
    }
    
    /**
     * onBeforeSave
     * @return boolean
     */
    public function beforeSave()
    {
        if($this->owner->isNewRecord) {
            $this->owner->ip=HPoll::getUserIp();
            $this->owner->user_hash=HPoll::getUserHash();
            $this->owner->create_time=new \CDbExpression('NOW()');
        }
        
        return true;
    }
    
    /**
     * onAfterDelete
     * @return boolean
     */
    public function afterDelete()
    {
        $query='DELETE FROM ' . HDb::qt(HCrud::param('extend_polls_results', 'config.tablename')) . ' WHERE `result_hash`='.(int)$this->owner->result_hash;
        HDb::execute($query);
        
        Poll::model()->updateStats((int)$this->owner->poll_id);
        
        return true;
    }
    
    public function getQuestionIdByQuestionHash($hash)
    {
        $query='SELECT `id` FROM ' . HDb::qt(HCrud::param('extend_polls_questions', 'config.tablename')) . ' WHERE ';
        $query.="SHA1(CONCAT('".Question::CLASS_HASH."_', `id`, '_', `poll_id`))";
        if(is_string($hash)) {
            $query.='='.HDb::qv($hash);
            return HDb::queryScalar($query);
        }
        elseif(is_array($hash)) {
            $query.=" IN ('" . implode("','", $hash) . "')";
            return HDb::queryColumn($query);
        }        
        return [];
    }
}