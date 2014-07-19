CREATE TABLE IF NOT EXISTS `#__zoo_zl_zoocart_payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `payment_method` varchar(255) NOT NULL,
  `order_id` int(11) NOT NULL,
  `transaction_id` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `total` float NOT NULL,
  `data` text NOT NULL,
  `created_on` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`,`transaction_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;