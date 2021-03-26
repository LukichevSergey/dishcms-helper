<?php
namespace ext\uploader\actions;

use ext\uploader\components\helpers\HUploader;

class DeleteFileAction extends \CAction
{
	public $path='webroot.images.uploader';
	public $maxtime=1800;
	
	public function run()
	{
		$result=['success'=>false];
		
		if(!empty($_REQUEST['filename'])) {
			if(HUploader::setBasePath($this->path)) {
				$webroot=\Yii::getPathOfAlias('webroot');
				$filename=$webroot . $_REQUEST['filename'];
				if(is_file($filename)) {
					if((time() - filemtime($filename)) < $this->maxtime) {
						unlink($filename);
						$result=['success'=>true];
					}
					else {
						$result['time']=time();
						$result['mtime']=filemtime($filename);
					}
				}
				else {
					$result['filename']=$filename;
				}
			}
			else {
				$result['errors']=['Файл не найден'];
			}
		}
		else {
			$result['errors']=['Не корректный запрос'];
		}
		
		echo json_encode($result);
        \Yii::app()->end();
    	exit;
	}
}