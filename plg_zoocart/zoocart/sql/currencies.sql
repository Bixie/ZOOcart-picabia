CREATE TABLE IF NOT EXISTS `#__zoo_zl_zoocart_currencies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `symbol` varchar(6) NOT NULL,
  `format` varchar(255) NOT NULL,
  `code` varchar(6) NOT NULL,
  `num_decimals` smallint(6) NOT NULL,
  `num_decimals_show` smallint(6) NOT NULL,
  `decimal_sep` char(1) NOT NULL,
  `thousand_sep` char(1) NOT NULL,
  `conversion_rate` float NOT NULL,
  `published` tinyint NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `code` (`code`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;