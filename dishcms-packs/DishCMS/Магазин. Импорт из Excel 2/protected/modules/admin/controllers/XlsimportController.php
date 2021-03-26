<?php
/**
 *
 */
Yii::import('application.modules.admin.models.XlsImportForm');

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HModel;
use common\components\helpers\HFile;

class XlsimportController extends AdminController
{
	public $imagesAlias='webroot.uploads.xlsimport';
	public $imagesUrl='/uploads/xlsimport';
	public $stepResult;

	public function filters()
	{
		return A::m(parent::filters(), [
			'ajaxOnly +upload,removeAllImages'
		]);
	}

    public function actionIndex()
    {
		$model=HModel::massiveAssignment('XlsImportForm', true, true, (int)A::get(A::get($_POST, 'XlsImportForm', []), 'step', 1)>1?'step':'start' );

		$result=false;
		if(isset($_POST['XlsImportForm'])) {
			$result=$model->import();
		}

		if($result === true) {
			Y::setFlash(Y::FLASH_SYSTEM_SUCCESS, 'Импорт товаров успешно завершен');
			Y::cacheFlush();
			$this->redirect('index');
		}
		elseif(is_array($result)) {
			$this->stepResult=$result;
		}

		$this->render('index', compact('model'));
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
