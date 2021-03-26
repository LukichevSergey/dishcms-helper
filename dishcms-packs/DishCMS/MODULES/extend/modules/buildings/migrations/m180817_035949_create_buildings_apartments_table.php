<?php

class m180817_035949_create_buildings_apartments_table extends CDbMigration
{
	public function safeUp()
	{
	    $this->createTable('buildings_apartments', [
	        'id'=>'pk',
	        'floor_id'=>'integer',
	        'map_hash'=>'string',
	        'title'=>'string',
	        'image'=>'string',
	        'image_alt'=>'string',
	        'published'=>'boolean',
	        'update_time'=>'TIMESTAMP',
	        
	        'sold'=>'boolean',
	        'price'=>'DECIMAL(15,2)',
	        'sale_price'=>'DECIMAL(15,2)',
	        'area'=>'DECIMAL(15,2)',
	        'rooms'=>'TINYINT',
	        'props'=>'LONGTEXT',
	        
	        'text'=>'LONGTEXT',	        
	    ]);
	    $this->createIndex('floor_id', 'buildings_apartments', 'floor_id');
	}

	public function safeDown()
	{
		echo "m180817_035949_create_buildings_apartments_table does not support migration down.\n";
	}
}