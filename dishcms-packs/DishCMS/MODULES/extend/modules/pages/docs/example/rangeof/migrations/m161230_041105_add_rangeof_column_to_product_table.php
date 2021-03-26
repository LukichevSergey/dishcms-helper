<?php

class m161230_041105_add_rangeof_column_to_product_table extends CDbMigration
{
	public function up()
	{
		$this->addColumn('product', 'rangeof', 'text');
	}

	public function down()
	{
		echo "m161230_041105_add_rangeof_column_to_product_table does not support migration down.\n";
//		return false;
	}
}