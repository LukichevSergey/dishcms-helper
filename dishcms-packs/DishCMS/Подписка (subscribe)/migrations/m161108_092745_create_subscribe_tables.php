<?php

class m161108_092745_create_subscribe_tables extends CDbMigration
{
	public function safeUp()
	{
		$this->execute(
'CREATE TABLE IF NOT EXISTS `subscribe` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(200) NOT NULL,
  `phone` varchar(200) NOT NULL,
  `hash` varchar(500) NOT NULL,
  `active` tinyint(4) NOT NULL DEFAULT \'1\',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `subscribe_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `theme` varchar(300) NOT NULL,
  `message` text NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `send_time` datetime NOT NULL,
  `from` varchar(200) NOT NULL,
  `from_name` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;'
		);
	}

	public function safeDown()
	{
		echo "m161108_092745_create_subscribe_tables does not support migration down.\n";
		// return false;
	}
}