<?php

class m171213_100820_add_in_paid_to_dorder_table extends CDbMigration
{
	public function up()
	{
		$this->addColumn('dorder', 'in_paid', 'boolean');
	}

	public function down()
	{
		echo "m171213_100820_add_in_paid_to_dorder_table does not support migration down.\n";
//		return false;
	}
}
