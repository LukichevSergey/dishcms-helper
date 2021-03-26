<?php

class m170111_093103_create_banks_table extends CDbMigration
{
	public function up()
	{
		$this->createTable('banks', [
			'id'=>'pk',
			'active'=>'boolean',				
			'title'=>'string',				
			'alias'=>'string',				
			'logo'=>'string',				
			'logo_enable'=>'boolean',				
			'logo_alt'=>'string',				
			'preview_text'=>'TEXT',				
			'detail_text'=>'LONGTEXT',	
			'bank_rate'=>'string',	
			'down_payment'=>'string',	
			'term_loan'=>'string',
			'decrease'=>'string',
		]);
	}

	public function down()
	{
		echo "m170111_093103_create_banks_table does not support migration down.\n";
		//return false;
	}
}