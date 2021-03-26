
CREATE TABLE IF NOT EXISTS `subscribe` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(200) NOT NULL,
  `phone` varchar(200) NOT NULL,
  `hash` varchar(500) NOT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Дамп данных таблицы `subscribe`
--

CREATE TABLE IF NOT EXISTS `subscribe_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `theme` varchar(300) NOT NULL,
  `message` text NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `send_time` datetime NOT NULL,
  `from` varchar(200) NOT NULL,
  `from_name` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

#id title type  options ordering  default hidden
INSERT INTO menu (title, options, ordering, hidden)
VALUES ( 'Управление подпиской' , '{"model":"subscribe"}', '-1', '1');