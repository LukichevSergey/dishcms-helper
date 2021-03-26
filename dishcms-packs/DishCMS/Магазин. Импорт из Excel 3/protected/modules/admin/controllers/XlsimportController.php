<?php
/**
 *
 */
Yii::import('application.modules.admin.models.XlsImportForm');

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HModel;
use common\components\helpers\HFile;
use common\components\helpers\HAjax;

class XlsimportController extends AdminController
{
	public $imagesAlias='webroot.uploads.xlsimport';
	public $imagesUrl='/uploads/xlsimport';

	public function filters()
	{
		return A::m(parent::filters(), [
			'ajaxOnly +upload,removeAllImages, next'
		]);
	}
	
	public function actionNext()
	{
	    $ajax=HAjax::start();
	    
	    $model=new \XlsImportForm('next');
	    $model->setIteration(A::get($_POST, 'iteration', 1));
	    $model->hash=A::get($_POST, 'hash', 1);
	    if($model->import() !== false) {
	        $ajax->data=$model->getAjaxData();
	        $ajax->success=true;
	        if($ajax->data['percent'] >= 100) {
	            Y::cacheFlush();
	        }
	    }
	    else {
	        $ajax->addErrors($model->getErrors());
	    }
	    
	    $ajax->end();
	}

    public function actionIndex()
    {
		$model=HModel::massiveAssignment('XlsImportForm', true);

		$view='index';
		if(isset($_POST['XlsImportForm']) && ($model->import() !== false)) {
	        $view='iteration';
		}
		
		$this->render($view, compact('model'));
    }

	//Загрузка и обрезка изображений
    public function actionUpload()
    {   
        $imagesPath=Yii::getPathOfAlias($this->imagesAlias);
        $result=array();

        //Получаем файл из $_FILES
        $upload=CUploadedFile::getInstanceByName('files[0]');

        //Проводим валидацию файла, если всё ок идём дальше. Если нет пишем ошибку.
        if ($upload && exif_imagetype($upload->getTempName())) {
            $result['img']=trim($this->imagesUrl, '/') . '/' .$upload->getName();
            $result['error']=0;
            $result['filename']=$upload->getName();
            //Сохраняем картинку (оригинал).
            $upload->saveAs($imagesPath . '/' . $result['filename']);

            echo json_encode($result);
        } else {
            $result['error']=1;
            $result['errors']='Файл не является изображением';
            echo json_encode($result);
        }
    }

	public function actionRemoveAllImages()
	{
		// HFile::rm(Y::getPathOfAlias($this->imagesAlias), false, true);
		HFile::rm(Y::getPathOfAlias('webroot.uploads.xlsimport'), false, true);
	}

	public function actionRemoveImage()
    {
		if($filename=A::get($_POST, 'image')) {
			$filename=Y::getPathOfAlias('webroot.uploads.xlsimport') . '/' . $filename;
			if(is_file($filename)) {
		        unlink($filename);
			}
		}
    }

}
