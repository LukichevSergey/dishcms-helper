<?php
/**
 * Виджет списка комментариев
 * 
 */
namespace extend\modules\comments\widgets;

use common\components\helpers\HArray as A;
use common\components\helpers\HDb;

class CommentsList extends \common\components\base\Widget
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
     * @var integer кол-во комментариев на странице
     */
    public $pageSize=10;
    
    /**
     * @var array дополнительные параметры для \CActiveDataProvider
     */
    public $dataProviderOptions=[];
    
    /**
     * @var array дополнительные параметры для \CListView
     */
    public $listViewOptions=[];
    
    /**
     * {@inheritDoc}
     * @see \common\components\base\Widget::$view
     */
    public $view='comments_list';
    
    /**
     * @var string шаблон отображение комментария
     */
    public $itemView='_comments_list_item';
    
    /**
     * {@inheritDoc}
     * @see \common\components\base\Widget::run()
     */
    public function run()
    {
        if(is_object($this->model)) {
            $this->model=get_class($this->model);
        }
        
        $this->render($this->view, $this->params);
    }
    
    public function getDataProvider()
    {   
        $criteria=HDb::criteria();
        $criteria->scopes=['published'];
        if($this->model) $criteria->scopes['byModel']=$this->model;
        if($this->model_id) $criteria->addColumnCondition(['model_id'=>$this->model_id]);
        
        return new \CActiveDataProvider('\crud\models\ar\extend\modules\comments\Comment', A::m([
            'criteria'=>$criteria,
            'pagination'=>['pageSize'=>$this->pageSize, 'pageVar'=>'page_comments'],
            'sort'=>['defaultOrder'=>'`t`.`sort` DESC, `t`.`create_time` DESC, `t`.`id` DESC']
        ], $this->dataProviderOptions));
    }
}