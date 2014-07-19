CREATE TABLE IF NOT EXISTS `#__zoo_zl_zoocart_addresses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'billing',
  `elements` longtext NOT NULL,
  `default` tinyint(1) NOT NULL,
  `params` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `type` (`type`),
  KEY `user_id_type` (`user_id`,`type`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;