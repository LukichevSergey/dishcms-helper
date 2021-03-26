<?php
namespace ext\uploader\widgets;

use ext\uploader\components\helpers\HUploader;

class FileList extends \CWidget
{
	public $hash;
	public $path='webroot.images.uploader';
	
	public $header='Прикрепленные файлы:';
	
	public $deleteUrl;
	
	public $view='file_list';
	public $params=[];
	
	public $tag='div';
	public $tagOptions=['class'=>'row'];
	
	public function run()
	{
		$this->render($this->view, $this->params);
	}
	
	public function isImageFile($filename)
	{
		return preg_match('/\.(jpg|png|gif|jpeg)$/', $filename);
	}
	
	public function getFiles()
	{
		$files=[];
		
		if(HUploader::setBasePath($this->path)) {
			$files=HUploader::getFiles(HUploader::getBasePath(), false, $this->hash);
			$files=array_map(function($filename){ return HUploader::getBaseUrl() . "/{$filename}"; }, $files);
			usort($files, function($a,$b){
				if($this->isImageFile($a) && $this->isImageFile($b)) return strcasecmp($a,$b);
				elseif($this->isImageFile($a)) return 1;
				return -1;
			});
		}
		
		return $files;
	}
}
