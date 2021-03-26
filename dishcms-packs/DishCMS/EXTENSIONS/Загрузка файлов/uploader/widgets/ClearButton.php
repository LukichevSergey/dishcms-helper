<?php
namespace ext\uploader\widgets;

class ClearButton extends \CWidget
{
	// required
	public $clearUrl;
	public $label='Очистить временные файлы';
	public $htmlOptions=['class'=>'btn btn-warning'];
	
	public function run()
	{
		echo \CHtml::ajaxLink($this->label, $this->clearUrl, [
			'type'=>'post',
			'dataType'=>'json',
			'beforeSend'=>'js:function(){return confirm("Подтвердите удаление временных файлов");}',
			'success'=>'js:function(data){alert("Удалено " + data.count + " файлов");}'
		], $this->htmlOptions);
	}
}