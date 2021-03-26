<?php

class m180116_110820_add_currency_column_to_product_table extends CDbMigration
{
	public function up()
	{
		$this->addColumn('product', 'currency', 'VARCHAR(4)');
	}

	public function down()
	{
		echo "m180116_110820_add_currency_column_to_product_table does not support migration down.\n";
//		return false;
	}
}
