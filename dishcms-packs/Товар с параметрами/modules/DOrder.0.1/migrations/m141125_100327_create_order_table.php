<?php

class m141125_100327_create_order_table extends CDbMigration
{
	private function _tableName() 
	{
		return \Yii::app()->getModule('DOrder')->tableName;
	}
	
	public function up()
	{
		$this->createTable($this->_tableName(), array(
			'id' => 'pk',
			'customer_data' => 'text',	
			'order_data' => 'text',
			'comment' => 'text',
			'create_time' => 'timestamp',
			'completed' => 'boolean DEFAULT 0',
			'paid' => 'boolean DEFAULT 0'
		));
	}

	public function down()
	{
		$this->dropTable($this->_tableName());
		return true;
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}