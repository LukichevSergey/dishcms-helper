<?php

class m170417_073600_create_regions_table extends CDbMigration
{
	public $tableName = 'regions_regions';

	public function safeUp()
	{
		$this->createTable($this->tableName, [
			'id'=>'pk',
			'code'=>'VARCHAR(8)',
			'domain'=>'string',
			'title'=>'string',
			'active'=>'boolean',
			'is_default'=>'boolean',
			'update_time'=>'TIMESTAMP DEFAULT CURRENT_TIMESTAMP'	
		]);
		$this->createIndex('code', $this->tableName, 'code', true);
		$this->createIndex('domain', $this->tableName, 'domain', true);
	}

	public function safeDown()
	{
		echo "m170417_073600_create_regions_table does not support migration down.\n";
		// $this->dropTable($this->tableName);
		// return false;
	}
}