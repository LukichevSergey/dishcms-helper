<?php

class m170209_071036_create_slider_tables extends CDbMigration
{
	public function up()
	{
		$sql='
CREATE TABLE `slider_sliders` (
	`id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`code` VARCHAR(255),
	`active` TINYINT(1) DEFAULT 0,
	`title` VARCHAR(255),
	`description` TEXT,
	`options` TEXT,
	`slide_properties` TEXT,
	`update_time` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
				
CREATE TABLE `slider_slides` (
	`id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`slider_id` INT(11) NOT NULL,
	`active` TINYINT(1) DEFAULT 0,
	`title` VARCHAR(255),
	`image` VARCHAR(255),
	`image_alt` VARCHAR(255),
	`url` VARCHAR(255),
	`description` TEXT,
	`options` TEXT,
	`update_time` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	FOREIGN KEY `fk_slider_id` (`slider_id`) REFERENCES `slider_sliders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
);';
		
		$this->execute($sql);
	}

	public function down()
	{
		echo "m170209_071036_create_slider_tables does not support migration down.\n";
//		return false;
	}
}
