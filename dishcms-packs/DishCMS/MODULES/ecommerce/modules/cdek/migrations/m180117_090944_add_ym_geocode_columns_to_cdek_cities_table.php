<?php

class m180117_090944_add_ym_geocode_columns_to_cdek_cities_table extends CDbMigration
{
	public function safeUp()
	{
        $this->addColumn('cdek_cities', 'ym_point_x', 'VARCHAR(12)');
        $this->addColumn('cdek_cities', 'ym_point_y', 'VARCHAR(12)');
        $this->addColumn('cdek_cities', 'ym_bounds_lx', 'VARCHAR(12)');
        $this->addColumn('cdek_cities', 'ym_bounds_ly', 'VARCHAR(12)');
        $this->addColumn('cdek_cities', 'ym_bounds_ux', 'VARCHAR(12)');
        $this->addColumn('cdek_cities', 'ym_bounds_uy', 'VARCHAR(12)');
	}

	public function safeDown()
	{
		echo "m180117_090944_add_ym_geocode_columns_to_cdek_cities_table does not support migration down.\n";
		//return false;
	}
}
