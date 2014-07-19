CREATE TABLE IF NOT EXISTS `#__zoo_zl_zoocart_shippingrates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `price_from` decimal(10,2) NOT NULL,
  `price_to` decimal(10,2) NOT NULL,
  `quantity_from` decimal(10,2) NOT NULL,
  `quantity_to` decimal(10,2) NOT NULL,
  `weight_from` decimal(10,2) NOT NULL,
  `weight_to` decimal(10,2) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `countries` varchar(255) NOT NULL,
  `states` varchar(1024) NOT NULL,
  `cities` varchar(1024) NOT NULL,
  `zips` varchar(1024) NOT NULL,
  `user_groups` varchar(255) NOT NULL,
  `published` tinyint NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;