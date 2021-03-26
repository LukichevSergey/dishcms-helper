<?php

class m171205_035051_create_product_sizes_table extends CDbMigration
{
	public function up()
	{
		$this->createTable('product_sizes', [
            'id'=>'pk',
            'title'=>'string',
            'active'=>'boolean',
        ]);
	}

	public function down()
	{
		echo "m171205_035051_create_product_sizes_table does not support migration down.\n";
//		return false;
	}
}
