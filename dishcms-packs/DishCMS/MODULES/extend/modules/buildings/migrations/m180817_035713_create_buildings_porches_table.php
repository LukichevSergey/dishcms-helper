<?php

class m180817_035713_create_buildings_porches_table extends CDbMigration
{
	public function up()
	{
	    $this->createTable('buildings_porches', [
	        'id'=>'pk',
	        'map_hash'=>'string',
	        'number'=>'integer',
	        'title'=>'string',
	        'published'=>'boolean',
	        'update_time'=>'TIMESTAMP'
	    ]);
	}

	public function down()
	{
		echo "m180817_035713_create_buildings_porches_table does not support migration down.\n";
	}
}
