<?php

class m180817_035836_create_buildings_floors_table extends CDbMigration
{
	public function safeUp()
	{
	    $this->createTable('buildings_floors', [
	        'id'=>'pk',
	        'porch_id'=>'integer',
	        'map_hash'=>'string',
	        'number'=>'integer',
	        'title'=>'string',
	        'image'=>'string',
	        'image_alt'=>'string',
	        'published'=>'boolean',
	        'update_time'=>'TIMESTAMP',
	        'text'=>'LONGTEXT',
	    ]);
	    $this->createIndex('porch_id', 'buildings_floors', 'porch_id');
	}

	public function safeDown()
	{
		echo "m180817_035836_create_buildings_floors_table does not support migration down.\n";
	}
}