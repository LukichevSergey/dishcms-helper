<?php
/**
 * Очистка непривязанных файлов
 */
namespace ext\uploader\actions;

use ext\uploader\components\helpers\HUploader;

class ClearAction extends \CAction
{
	public $path='webroot.images.uploader';
	/**
	 * Модели должны быть \CActiveRecord и содержать статически метод model()
	 * @var array [modelClass=>attributeFileHash] или [[modelObject, attributeFileHash]]
	 */
	public $models=[];
	public $hashes=[];
	
	public function run()
	{
		$result=['count'=>0];
		
		if(HUploader::setBasePath($this->path)) {
			if(count($this->models) > 0) {
				foreach($this->models as $modelClass=>$attributeFileHash) {
					if(is_array($attributeFileHash)) {
				        $model=$attributeFileHash[0];
				        $attributeFileHash=$attributeFileHash[1];
				    }
				    else {
				        $model=$modelClass::model();
				    }
					if($models=$model->findAll(['select'=>$attributeFileHash, 'group'=>$attributeFileHash])) {
						foreach($models as $model) {
							$this->hashes[]=$model->$attributeFileHash;
						}
					}
				}
			}
			
			if(count($this->hashes) > 0) {
				$delete=HUploader::getFiles(HUploader::getBasePath(), true, $this->hashes, true);
				if(!empty($delete)) {
					foreach($delete as $filename) {
						unlink($filename);
						$result['count']++;
					}
				}
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
