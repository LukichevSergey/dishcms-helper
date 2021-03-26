<?php
namespace extend\modules\polls\behaviors;

use common\components\helpers\HArray as A;
use common\components\helpers\HAjax;
use common\components\helpers\HHash;
use extend\modules\polls\components\helpers\HPoll;
use crud\models\ar\extend\modules\polls\models\Poll;
use crud\models\ar\extend\modules\polls\models\Question;
use crud\models\ar\extend\modules\polls\models\Result;

class PollAjaxControllerBehavior extends \CBehavior
{
    public function actionAdd($cid)
    {
        $ajax=HAjax::start();
        
        $hashes=array_keys($_POST);
        $questionIds=Result::model()->getQuestionIdByQuestionHash($hashes);
        if(!empty($questionIds)) {
            $questions=Question::model()->findAllByPk($questionIds);
            if(!empty($questions)) {
        	$pollId=$questions[0]->poll_id;
                if(HPoll::isPassed($pollId)) {
                    $ajax->addError('Опрос уже пройден');
                }
                else {
                    $results=[];
                    $resultHash=HHash::random();
                    foreach($questions as $question) {
                        $questionHash=$question->getHash();
                        if($answerHash=A::get($_POST, $questionHash)) {
                            $answers=$question->answersBehavior->get(true);
                            $exists=false;
                            foreach($answers as $answer) {
                                if(is_array($answerHash)) {
                                    $answerExists=in_array($answer['hash'], $answerHash);
                                }
                                else {
                                    $answerExists=($answer['hash'] == $answerHash);
                                }
                                
                                $exists=$exists || $answerExists;
                                
                                if($answerExists) {
                                    $result=new Result;
                                    $result->result_hash=$resultHash;
                                    $result->poll_id=$question->poll_id;
                                    $result->question_id=$question->id;
                                    $result->answer_hash=$answer['hash'];
                                    $results[]=$result;                                
                                }
                            }
                            
                            if(!$exists && $question->required) {
                                $ajax->addError('Вопрос "' . $question->title . '" требует обязательного ответа');
                            }
                        }
                        elseif($question->required) {
                            $ajax->addError('Вопрос "' . $question->title . '" требует обязательного ответа');
                        }
                    }
                    if(count($ajax->errors) < 1) {
                        if(!empty($results)) {
                            $notAllSaved=false;
                            foreach($results as $result) {
                                if(!$result->save()) {
                                    $notAllSaved=true;
                                }
                            }
                            if($notAllSaved) {
                                $ajax->addError('Не все ответы удалось сохранить');
                            }
                            else {
                        	Poll::model()->updateStats($pollId);
                                $ajax->success=true;
                            }
                        }
                    }
                }
            }
            else {
                $ajax->addError('Некорретный запрос');
            }
        }
        else {
            $ajax->addError('Некорретный запрос');
        }
        
        $ajax->end();
    }
}