CREATE TABLE IF NOT EXISTS `#__zoo_zl_zoocart_orderstates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8  ;

-- QUERY SEPARATOR --
INSERT IGNORE INTO `#__zoo_zl_zoocart_orderstates` VALUES(1, 'PLG_ZOOCART_ORDER_STATE_PENDING', 'PLG_ZOOCART_ORDER_STATE_PENDING_DESCR');
-- QUERY SEPARATOR --
INSERT IGNORE INTO `#__zoo_zl_zoocart_orderstates` VALUES(2, 'PLG_ZOOCART_ORDER_STATE_RECEIVED', 'PLG_ZOOCART_ORDER_STATE_RECEIVED_DESCR');
-- QUERY SEPARATOR --
INSERT IGNORE INTO `#__zoo_zl_zoocart_orderstates` VALUES(3, 'PLG_ZOOCART_ORDER_STATE_PROCESSING', 'PLG_ZOOCART_ORDER_STATE_PROCESSING_DESCR');
-- QUERY SEPARATOR --
INSERT IGNORE INTO `#__zoo_zl_zoocart_orderstates` VALUES(4, 'PLG_ZOOCART_ORDER_STATE_SHIPPED', 'PLG_ZOOCART_ORDER_STATE_SHIPPED_DESCR');
-- QUERY SEPARATOR --
INSERT IGNORE INTO `#__zoo_zl_zoocart_orderstates` VALUES(5, 'PLG_ZOOCART_ORDER_STATE_COMPLETED', 'PLG_ZOOCART_ORDER_STATE_COMPLETED_DESCR');
-- QUERY SEPARATOR --
INSERT IGNORE INTO `#__zoo_zl_zoocart_orderstates` VALUES(6, 'PLG_ZOOCART_ORDER_STATE_FAILED', 'PLG_ZOOCART_ORDER_STATE_FAILED_DESCR');
-- QUERY SEPARATOR --
INSERT IGNORE INTO `#__zoo_zl_zoocart_orderstates` VALUES(7, 'PLG_ZOOCART_ORDER_STATE_CANCELED', 'PLG_ZOOCART_ORDER_STATE_CANCELED_DESCR');
-- QUERY SEPARATOR --
INSERT IGNORE INTO `#__zoo_zl_zoocart_orderstates` VALUES(8, 'PLG_ZOOCART_ORDER_STATE_REFUNDED', 'PLG_ZOOCART_ORDER_STATE_REFUNDED_DESCR');
-- QUERY SEPARATOR --
INSERT IGNORE INTO `#__zoo_zl_zoocart_orderstates` VALUES(9, 'PLG_ZOOCART_ORDER_STATE_VALIDATING', 'PLG_ZOOCART_ORDER_STATE_VALIDATING_DESCR');