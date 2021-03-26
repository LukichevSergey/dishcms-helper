<?php

class m180411_060511_create_price_tables extends CDbMigration
{
	public function safeUp()
	{
		$this->createTable('price_sections', [
			'id'=>'pk',
			'title'=>'string',
			'actived'=>'boolean',
			'update_time'=>'TIMESTAMP'
		]);
		$this->createTable('price_subsections', [
			'id'=>'pk',
			'section_id'=>'integer NOT NULL',
			'title'=>'string',
			'actived'=>'boolean',
			'text'=>'LONGTEXT',
			'update_time'=>'TIMESTAMP'
		]);
	}

	public function safeDown()
	{
		echo "m180411_060511_create_price_tables does not support migration down.\n";
		// return false;
	}
}
