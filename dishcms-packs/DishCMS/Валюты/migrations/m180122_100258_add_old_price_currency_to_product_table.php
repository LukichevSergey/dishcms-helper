<?php

class m180122_100258_add_old_price_currency_to_product_table extends CDbMigration
{
	public function up()
	{
		$this->addColumn('product', 'old_price_currency', 'VARCHAR(4)');
	}

	public function down()
	{
		echo "m180122_100258_add_old_price_currency_to_product_table does not support migration down.\n";
//		return false;
	}
}
