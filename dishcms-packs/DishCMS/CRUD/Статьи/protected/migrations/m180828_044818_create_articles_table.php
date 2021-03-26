<?php

class m180828_044818_create_articles_table extends CDbMigration
{
	public function up()
	{
		$this->createTable('articles', [
            'id'=>'pk',
            'alias'=>'string',
            'title'=>'string',
            'active'=>'boolean',
            'preview'=>'VARCHAR(32)',
            'enable_preview'=>'boolean',
            'preview_text'=>'text',
            'text'=>'longtext',
            'create_time'=>'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
            'update_time'=>'TIMESTAMP'            
        ]);
	}

	public function down()
	{
		echo "m180828_044818_create_articles_table does not support migration down.\n";
	}
}
