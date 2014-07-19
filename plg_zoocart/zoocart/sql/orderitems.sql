CREATE TABLE IF NOT EXISTS `#__zoo_zl_zoocart_orderitems` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `elements` text NOT NULL,
  `quantity` float NOT NULL,
  `weight` float NOT NULL,
  `subscription` text NOT NULL,
  `variations` text NOT NULL,
  `price` float NOT NULL,
  `tax` float NOT NULL,
  `total` float NOT NULL,
  `params` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`,`item_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;