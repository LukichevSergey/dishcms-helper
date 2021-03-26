<?php

class m170216_031335_create_page_table extends CDbMigration
{
	public $table='pages_page';
	
	/**
	 * (non-PHPdoc)
	 * @see CDbMigration::safeUp()
	 */
	public function safeUp()
	{
		$this->createTable($this->table, [
			'id'=>'pk',
			'title'=>'string',
			'published'=>'boolean',
			'sef'=>'string',
			'preview_text'=>'TEXT',
			'detail_text'=>'LONGTEXT',
			'preview_image'=>'string',
			'preview_image_alt'=>'string',
			'preview_image_enable'=>'boolean',
			'properties'=>'LONGTEXT',
			'create_time'=>'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
			'update_time'=>'TIMESTAMP'
		]);
		$this->createIndex('published', $this->table, 'published');
		$this->createIndex('update_time', $this->table, 'update_time');
		$this->createIndex('sef', $this->table, 'sef');
		$this->execute('UPDATE '.$this->table.' SET update_time=NOW()');
	}

	/**
	 * (non-PHPdoc)
	 * @see CDbMigration::safeDown()
	 */
	public function safeDown()
	{
		echo "m170216_031335_create_page_table does not support migration down.\n";
		// $this->dropTable($this->table);
		// return false;
	}
}
