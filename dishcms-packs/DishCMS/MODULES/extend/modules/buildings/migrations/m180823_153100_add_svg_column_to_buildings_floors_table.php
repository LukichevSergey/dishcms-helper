<?php

class m180823_153100_add_svg_column_to_buildings_floors_table extends CDbMigration
{
	public function safeUp()
	{
	    $this->addColumn('buildings_floors', 'svg', 'string');
	}

	public function safeDown()
	{
		echo "m180823_153100_add_svg_column_to_buildings_floors_table does not support migration down.\n";
	}
}
