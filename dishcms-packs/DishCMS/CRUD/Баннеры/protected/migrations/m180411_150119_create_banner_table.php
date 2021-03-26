<?php

class m180411_150119_create_banner_table extends CDbMigration
{
	public function up()
	{
		$this->createTable('banners', [
			'id'=>'pk',
			'title'=>'string',
			'image'=>'string',
			'link'=>'string',
			'actived'=>'boolean',
			'update_time'=>'TIMESTAMP'
		]);
	}

	public function down()
	{
		echo "m180411_150119_create_banner_table does not support migration down.\n";
//		return false;
	}
}
