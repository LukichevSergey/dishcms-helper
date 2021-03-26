<?php
class m170417_073601_create_regions_data_table extends CDbMigration
{
	public $regionsTableName = 'regions_regions';
	public $tableName = 'regions_data';

	public function safeUp()
	{
		$this->createTable($this->tableName, array(
			'id'=>'pk',
			'region_id'=>'INT(11)',
			'hash'=>'BIGINT(20)',
			'uhash'=>'BIGINT(20)',
			'model_name'=>'VARCHAR(255) NOT NULL DEFAULT \'\'',
			'model_id'=>'INT(11) NOT NULL DEFAULT 0',
			'model_attribute'=>'VARCHAR(255) NOT NULL DEFAULT \'\'',
			'use_default'=>'TINYINT(1) DEFAULT 1',
			'is_forced'=>'TINYINT(1) DEFAULT 0',
			'value'=>'LONGTEXT',
			'update_time'=>'TIMESTAMP DEFAULT CURRENT_TIMESTAMP'
		));
		$this->AddForeignKey('region_id', $this->tableName, 'region_id', $this->regionsTableName, 'id', 'CASCADE', 'CASCADE');
		$this->createIndex('hash', $this->tableName, 'hash');
		$this->createIndex('uhash', $this->tableName, 'uhash');
//		SQL Error: Specified key was too long; max key length is 1000 bytes.
//		$this->createIndex('ukey', $this->tableName, ['region_id','model_name', 'model_id', 'model_attribute'], true);
	}

	public function down()
	{
		echo "m170417_073601_create_regions_data_table does not support migration down.\n";
		// $this->dropTable($this->tableName);
		// return false;
	}
}
