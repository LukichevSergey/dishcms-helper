<?php
namespace ext\uploader\actions;

use ext\uploader\components\helpers\HUploader;

class UploadFileAction extends \CAction
{
	public $path='webroot.images.uploader';
	public $limit=3;
	public $maxsize=2097152;
	/**
	 * @var string extensions "jpg,jpeg,gif,png,xls,doc,pdf,xlsx,docx"
	 */
	public $extensions='jpg,jpeg,gif,png,xls,doc,pdf,xlsx,docx';
	
	public function run()
	{
		$result=['success'=>false];
		
		if(!empty($_REQUEST['hash'])) {
			if(HUploader::setBasePath($this->path)) {
				$hash=$_REQUEST['hash'];
	        	$files=HUploader::getFiles(HUploader::getBasePath(), false, $hash);
	        	if (count($files) >= $this->limit) {
	            	$result['error'] = 1;
	            	$result['errors'] = ['Загружено максимальное кол-во файлов'];
	        	}
	        	else {
			        $file=\CUploadedFile::getInstanceByName('files[0]');
			        if($file) {
			        	if($this->maxsize && (filesize($file->getTempName()) > $this->maxsize)) {
			        		$result['error']=1;
		            		$result['errors']=['Допустимый размер файла ' . HUploader::formatSize($this->maxsize)];
			        	}
			        	else {
					        $ext=$file->getExtensionName();
				        	if ($ext && in_array($ext, explode(',', str_replace(' ', '', $this->extensions)))) {
				            	$rand=substr(md5(microtime()),rand(0,26),12);
				            	$result['filename']="{$hash}_{$rand}.{$ext}";
				            	$result['img']=HUploader::getBaseUrl() . "/{$result['filename']}";
				            	$result['ext']=$ext;
				            	$result['error']=0;
				            	$result['origin']=$file->getName();
				            	if($file->saveAs( trim($result['img'], '/') )) {
				            		$result['success']=true;
				            	}
				            	else {
				            		$result['error']=1;
				            		$result['errors']=['Не удалось сохранить файл'];
				            	}
				        	}
				        	else {
					            $result['error']=1;
					            $result['errors']=['Разрешены к загрузке только файлы с расширением: ' . $this->extensions];
					        }
			        	}
			        }
			        else {
			        	$result['error']=1;
	            		$result['errors']=['Не удалось загрузить файл'];
			        }
	        	}
			}
			else {
				$result['error']=1;
				$result['errors']=['Возникла ошибка на стороне сервера'];
			}
		}
		else {
			$result['error']=1;
			$result['errors']=['Некорректный запрос'];
		}

        echo json_encode($result);
        \Yii::app()->end();
    	exit;
	}
}