CREATE TABLE IF NOT EXISTS `#__zoo_zl_zoocart_user_discount` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `discount_id` int(11) NOT NULL,
  `hits` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;