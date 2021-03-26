<?php

class m170302_103635_create_ecommerce_concurent_goods_table extends CDbMigration
{
	public $table='ecommerce_concurent_goods';
	
	/**
	 * (non-PHPdoc)
	 * @see CDbMigration::safeUp()
	 */
	public function safeUp()
	{
		$this->createTable($this->table, [
			'id'=>'pk',
			'title'=>'string',
			'price'=>'DECIMAL(15,2)',
			'currency'=>'VARCHAR(5)',
			'active'=>'boolean',
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
		echo "m170302_103635_create_ecommerce_concurent_goods_table does not support migration down.\n";
		// $this->dropTable($this->table);
		// return false;
	}
}
