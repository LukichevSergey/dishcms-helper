Добавить в модель атрибут filehash
public $filehash;
в rules() ['filehash', 'safe'],

!!!
!!! ВАЖНО! В параметре "models" необходимо указывать все модели, для которых используется виджет загрузки! Иначе удаляться фото не указанных моделей!
!!!

В раздел администрирования в контроллер:
public function actions()
{
	return \CMap::mergeArray(parent::actions(), [
		'clearFiles'=>[
			'class'=>'\ext\uploader\actions\ClearAction',
			'models'=>['\Question'=>'filehash']
		]
	]);
}

public function filters()
{
	return \CMap::mergeArray(parent::actions(), [
		'ajaxOnly +clearFile'
	]);
}

в публичной части в контроллер
public function actions()
{
	return \CMap::mergeArray(parent::actions(), [
		'uploadFile'=>'\ext\uploader\actions\UploadFileAction',
		'deleteFile'=>'\ext\uploader\actions\DeleteFileAction',
	]);
}

public function filters()
{
	return \CMap::mergeArray(parent::actions(), [
		'ajaxOnly +uploadFile, deleteFile'
	]);
}

в форму
<?php $this->widget('\ext\uploader\widgets\UploadField', [
	'form'=>$form,
	'model'=>$model,
	'attribute'=>'filehash',
	'uploadUrl'=>'/mycontroller/uploadFile',
	'deleteUrl'=>'/mycontroller/deleteFile',
]); ?>

в раздел администрирования 
кнопка очистки временных файлов
<? $this->widget('\ext\uploader\widgets\ClearButton', [
	'clearUrl'=>'/cp/mycontroller/clearFiles', 
	'htmlOptions'=>['class'=>'btn btn-warning pull-right']
]); ?>

для отображения списка файлов
<? $this->widget('\ext\uploader\widgets\FileList', ['hash'=>$model->filehash]); ?>

=== Рецепты ===
--- для раздела администрирования ---
модель 
public function getFileHash()
{
	return md5(get_class($this) . "_{$this->id}");
}
контроллер
public function actions()
{
	return \CMap::mergeArray(parent::actions(), [
		'uploadFile'=>['class'=>'\ext\uploader\actions\UploadFileAction', 'extensions'=>'jpg,png,jpeg'], // разрешно заргружать только картинки
		'deleteFile'=>['class'=>'\ext\uploader\actions\DeleteFileAction', 'maxtime'=>315360000] // всегда разрешено удаление файлоа (10 лет)
	]);
}
шаблон
<?php $this->widget('\ext\uploader\widgets\UploadField', [
	'hash'=>$item->getFileHash(),
	'uploadUrl'=>'/cp/question/uploadFile',
	'deleteUrl'=>'/cp/question/deleteFile',
	'tagOptions'=>['class'=>'row', 'style'=>'margin:10px -20px;padding:0 40px;']
]); ?>
<? $this->widget('\ext\uploader\widgets\FileList', [
	'hash'=>$item->getFileHash(),
	'tagOptions'=>['class'=>'row', 'style'=>'margin:10px -20px;padding:0 40px;'],
	'deleteUrl'=>'/cp/question/deleteFile'
]); ?>


-- для формы обратной связи / раздел администрирования --
 public function actions()
    {
        $myFeedbackModel=\feedback\components\FeedbackFactory::factory('my_feedback_id')->getModelFactory()->getModel();
        return \CMap::mergeArray(parent::actions(), [
            'clearFiles'=>[
                'class'=>'\ext\uploader\actions\ClearAction',
                'models'=>[[$myFeedbackModel, 'filehash']]
            ],
            
            
            
прикрепление файлов к письму
 $model=$event->params['model'];
$attacheFiles=[];
if($model->filehash) {
    $attacheFiles=HUploader::getFiles(\Yii::getPathOfAlias('webroot.images.uploader'), true, $model->filehash);
}
HEmail::cmsAdminSend(true, [
    'factory'=>$event->params['factory'],
    'model'=>$event->params['model'],
], 'feedback.views._email.new_message_success', false, $attacheFiles);
* если требуется добавить возможность прикреплять файл в 
HEmail::cmsAdminSend(..., $attachfiles=[]) 
и HEmail::send(..., $attachfiles=[]);

	if(!empty($attachfiles)) {
        foreach($attachfiles as $attachfile) {
            $mail->addAttachment($attachfile);
        }
    }
    
    return $mail->send();


---------------------------------------------------------------------------
Для формы обратной связи

1) в файл protected\modules\feedback\controllers\AjaxController.php добавить
	public function actions()
	{
		return \CMap::mergeArray(parent::actions(), [
			'uploadFile'=>[
				'class'=>'\ext\uploader\actions\UploadFileAction',
				'extensions'=>'jpg,jpeg,png,doc,docx,pdf,rtf,xls,xlsx,txt,tiff',
				'limit'=>3
			],
			'deleteFile'=>'\ext\uploader\actions\DeleteFileAction',
		]);
	}

	public function filters()
	{
		return \CMap::mergeArray(parent::filters(), array(
			'ajaxOnly +uploadFile, deleteFile'
		));
	} 

2) в конфигурацию добавить
	'photo_hash' => array(
		'label' => 'Хэш фото',
		'type' => 'Hidden',
		'rules' => array(
			array('photo_hash', 'required')
		),
	),

3) добавить в шаблон отображения виджета FeedbackWidget
<?php $this->widget('\ext\uploader\widgets\UploadField', [
					'form'=>$form,
					'model'=>$factory->getModelFactory()->getModel(),
					'attribute'=>'photo_hash',
					'uploadUrl'=>'/feedback/ajax/uploadFile',
					'deleteUrl'=>'/feedback/ajax/deleteFile',
					'view'=>'my_upload_field' // если требуется поправить верстку
				]); ?>


4) не забыть добавить в исключения photo_hash в шаблоне почтового уведомления protected\modules\feedback\views\_email\new_message_success.php

5) В раздел администрирования protected\modules\feedback\controllers\AdminController.php
!!!
!!! ВАЖНО! В параметре "models" необходимо указывать все модели, для которых используется виджет загрузки! Иначе удаляться фото не указанных моделей!
!!!
 public function actions()
	$byPhotoFeedbackModel=\feedback\components\FeedbackFactory::factory('by_photo')->getModelFactory()->getModel();
        $actions['clearFiles']=[
            'class'=>'\ext\uploader\actions\ClearAction',
            'models'=>[[$byPhotoFeedbackModel, 'photo_hash']]
        ];

 в шаблон /admin/index.php
 вставить
<? if($factory->getId() == 'by_photo') $this->widget('\ext\uploader\widgets\FileList', [
 	'header'=>'Фотография',
	'hash'=>$feedback->photo_hash,
	'tagOptions'=>['class'=>'row', 'style'=>'margin:0;padding:0;'],
]); ?>

кнопка очистки временных файлов
<? $this->widget('\ext\uploader\widgets\ClearButton', [
	'clearUrl'=>'/cp/feedback/<FEEDBACK_ID>/clearFiles', 
	'htmlOptions'=>['class'=>'btn btn-warning pull-right']
]); ?>

 а также иструкция выше...