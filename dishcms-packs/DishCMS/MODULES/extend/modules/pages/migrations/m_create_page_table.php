<?php
namespace extend\modules\pages\migrations;

class m_create_page_table extends \CDbMigration
{
	/**
	 * @var string имя таблицы.
	 */
	public $table='pages_page';
	
	/**
	 * @var boolean создать индекс атрибута "published".
	 * По умочанию (TRUE) - создать.
	 */
	protected $createIndexPublished=true;
	
	/**
	 * @var boolean создать индекс атрибута "update_time".
	 * По умочанию (TRUE) - создать.
	 */
	protected $createIndexUpdateTime=true;
	
	/**
	 * @var boolean создать индекс атрибута "sef".
	 * По умочанию (TRUE) - создать.
	 */
	protected $createIndexSef=true;
	
	/**
	 * Получить массив полей для создания таблицы.
	 * @return array
	 */
	protected function getCreateTableColumns()
	{
		return [
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
		];
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CDbMigration::safeUp()
	 */
	public function safeUp()
	{
		$this->createTable($this->table, $this->getCreateTableColumns());
		
		if($this->createIndexPublished) {
			$this->createIndex('published', $this->table, 'published');
		}
		
		if($this->createIndexUpdateTime) {
			$this->createIndex('update_time', $this->table, 'update_time');
		}
		
		if($this->createIndexSef) {
			$this->createIndex('sef', $this->table, 'sef');
		}

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
