<?php
/**
 * Виджет формы добавления комментария
 *
 */
namespace extend\modules\comments\widgets;

use common\components\helpers\HArray as A;
use common\components\helpers\HDb;
use crud\models\ar\extend\modules\comments\Comment;
use extend\modules\comments\components\helpers\HComment;

class AddCommentForm extends \common\components\base\Widget
{
    /**
     * @var string значение атрибута "model" у модели
     * \crud\models\ar\extend\modules\comments\Comment
     */
    public $model=null;
    
    /**
     * @var integer значение атрибута "model_id" у модели
     * \crud\models\ar\extend\modules\comments\Comment
     */
    public $model_id=null;
    
    /**
     * @var integer значение атрибута "model_hash" у модели
     * \crud\models\ar\extend\modules\comments\Comment
     */
    public $model_hash=null;
    
    /**
     * URL обработки ajax-запроса добавления комментария.
     * @var string 
     */
    public $action='/common/crud/default/ajax';
    
    /**
     * {@inheritDoc}
     * @see \common\components\base\Widget::$view
     */
    public $view='add_comment_form';
    
    /**
     * Дополнительные опции для формы добавления комментария
     * @var array
     */
    public $formOptions=[];
    
    /**
     * {@inheritDoc}
     * @see \common\components\base\Widget::$params
     * 
     * В базовых шаблонах доступны параметры:
     * "disableRating"=>boolean не отображать выбор оценки;
     */
    public $params=[];
    
    /**
     * {@inheritDoc}
     * @see \common\components\base\Widget::run()
     */
    public function run()
    {
        if(is_object($this->model)) {
            $this->model=get_class($this->model);
        }
        
        $model=new Comment;
        $model->model=uniqid('m');
        $model->model_id=$this->model_id;
        $model->model_hash=HComment::getParentHashByModel($this->model);
        
        $this->params['model']=$model;
        
        $this->render($this->view, $this->params);
    }
}