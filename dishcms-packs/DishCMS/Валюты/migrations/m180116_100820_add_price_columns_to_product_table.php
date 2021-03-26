<?php

class m180116_100820_add_price_columns_to_product_table extends CDbMigration
{
	public function up()
	{
		$this->addColumn('product', 'price_eur', 'DECIMAL(15,2)');
		$this->addColumn('product', 'price_usd', 'DECIMAL(15,2)');
	}

	public function down()
	{
		echo "m180116_100820_add_price_columns_to_product_table does not support migration down.\n";
//		return false;
	}
}
