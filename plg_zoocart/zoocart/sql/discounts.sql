CREATE TABLE IF NOT EXISTS `#__zoo_zl_zoocart_discounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `discount`	decimal(10,2) DEFAULT 0,
  `type`	tinyint DEFAULT 0,
  `published` int(3) DEFAULT 1,
  `used` int(11) DEFAULT 0,
  `valid_from` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `valid_to` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `usergroups` varchar(255) NOT NULL,
  `hits_per_user` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;