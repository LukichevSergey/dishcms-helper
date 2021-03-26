<?php

class m171122_090944_create_cdek_cities_table extends CDbMigration
{
	public function up()
	{
        $this->createTable('cdek_cities', [
            'id'=>'pk',
            'cdek_id'=>'integer',
            'fullname'=>'string',
            'cityname'=>'string',
            'oblname'=>'string',
            'postcode'=>'string',
            'center'=>'boolean'
        ]);
	}

	public function down()
	{
		echo "m171122_090944_create_cdek_cities_table does not support migration down.\n";
		//return false;
	}
}
