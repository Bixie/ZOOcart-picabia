CREATE TABLE IF NOT EXISTS `#__zoo_zl_zoocart_taxes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `country` char(2) NOT NULL,
  `state` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `vies` varchar(255) NOT NULL,
  `zip` varchar(10) NOT NULL,
  `taxrate` float NOT NULL,
  `published` tinyint(1) NOT NULL,
  `ordering` int(11) NOT NULL,
  `tax_class_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;