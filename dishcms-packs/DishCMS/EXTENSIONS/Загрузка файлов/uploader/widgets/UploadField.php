<?php
namespace ext\uploader\widgets;

class UploadField extends \CWidget
{
	// required
	public $uploadUrl;
	public $deleteUrl;
	
	public $hash;
	public $form;
	public $model;
	public $attribute='filehash';
	public $label='Прикрепить файлы';

	public $view='upload_field';
	public $params=[];
	
	public $tag='div';
	public $tagOptions=['class'=>'row'];
	
	private static $seeds=[];
	private static $seedsCount=0;
	
	public function run()
	{
		$this->render($this->view, $this->params);
	}
	
	public function generateHash()
	{
		static::$seedsCount++;

        list($usec, $sec) = explode(' ', microtime());
        $seed = (float) $sec + ((float) $usec * 100000);
        if(isset(static::$seeds[$seed])) $seed+=1000*static::$seedsCount;
        mt_srand($seed);
        
        static::$seeds[$seed]=true;

        return mt_rand();
	}
}