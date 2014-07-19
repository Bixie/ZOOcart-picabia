CREATE TABLE IF NOT EXISTS `#__zoo_zl_zoocart_cartitems` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `session_id` varchar(255) NOT NULL,
  `item_id` int(11) NOT NULL,
  `quantity` float NOT NULL,
  `subscription` text NOT NULL,
  `variations` text NOT NULL,
  `params` text NOT NULL,
  `modified_on` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id_session_item` (`user_id`,`session_id`,`item_id`),
  KEY `user_id` (`user_id`),
  KEY `user_id_session` (`user_id`,`session_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;