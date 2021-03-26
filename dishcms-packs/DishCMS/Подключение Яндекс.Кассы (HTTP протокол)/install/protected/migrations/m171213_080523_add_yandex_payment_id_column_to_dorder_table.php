<?php

class m171213_080523_add_yandex_payment_id_column_to_dorder_table extends CDbMigration
{
	public function up()
	{
		$this->addColumn('dorder', 'yandex_payment_id', 'string');
	}

	public function down()
	{
		echo "m171213_080523_add_yandex_payment_id_column_to_dorder_table does not support migration down.\n";
//		return false;
	}
}
