CREATE TABLE IF NOT EXISTS `#__zoo_zl_zoocart_orderhistories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `timestamp` int(11) NOT NULL,
  `property` varchar(20) NOT NULL,
  `value_old` text NOT NULL,
  `value_new` text NOT NULL,
  `modified_by` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;