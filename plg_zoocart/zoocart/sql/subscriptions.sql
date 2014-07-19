CREATE TABLE IF NOT EXISTS `#__zoo_zl_zoocart_subscriptions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `publish_up` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down`  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `published` tinyint NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;