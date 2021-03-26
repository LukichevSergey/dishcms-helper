<?php

class m180412_113200_create_product_colors_table extends CDbMigration
{
	public function up()
	{
		$this->createTable('product_colors', [
            'id'=>'pk',
            'title'=>'string',
            'active'=>'boolean',
			'image'=>'string',
			'hexcode'=>'string',
        ]);
	}

	public function down()
	{
		echo "m180412_113200_create_product_colors_table does not support migration down.\n";
//		return false;
	}
}
