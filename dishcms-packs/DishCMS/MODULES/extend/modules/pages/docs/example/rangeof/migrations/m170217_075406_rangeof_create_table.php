<?php

class m170217_075406_rangeof_create_table extends \extend\modules\pages\migrations\m_create_page_table
{
	public $table='rangeof';
	
	protected function getCreateTableColumns()
	{
		return \CMap::mergeArray(parent::getCreateTableColumns(), [
			'display_index_page'=>'boolean'
		]);
	}
}