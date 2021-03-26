<?php

class m180412_113500_add_colors_column_to_product_table extends CDbMigration
{
	public function up()
	{
		$this->addColumn('product', 'colors', 'string');
	}

	public function down()
	{
		echo "m180412_113500_add_colors_column_to_product_table does not support migration down.\n";
//		return false;
	}
}
