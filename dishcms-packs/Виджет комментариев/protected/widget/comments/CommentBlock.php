<?php
/**
 * Виджет комментарии
 */

class CommentBlock extends \CWidget
{
    public $model_name = '';
    public $model_id;
    public $relation_model;

    public function run()
    {
        $message = '';
        $model = new Comment;

        $this->model_name = get_class($this->relation_model);

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if(isset($_POST['Comment']))
        {
            $model->attributes=$_POST['Comment'];

            $model->model_id = $this->model_id;
            $model->model_name = $this->model_name;

            if($model->save()) {
                $message = 'Успешно! Ваш комментарий отправлен на модерацию.';
                $model = new Comment;
            }
        }

    	$this->render('index', array('model' => $model, 'message' => $message, 'relation_model' => $this->relation_model));
    }

    /**
     * Performs the AJAX validation.
     * @param Comment $model the model to be validated
     */
    protected function performAjaxValidation($model)
    {
        if(isset($_POST['ajax']) && $_POST['ajax']==='comment-form')
        {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }
}
