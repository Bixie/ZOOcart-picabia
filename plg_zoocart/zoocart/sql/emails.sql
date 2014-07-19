CREATE TABLE IF NOT EXISTS `#__zoo_zl_zoocart_emails` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(255) NOT NULL,
  `groups` varchar(255) NOT NULL,
  `cc` varchar(255) NOT NULL,
  `bcc` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `template` text NOT NULL,
  `published` tinyint NOT NULL DEFAULT 1,
  `language` varchar(5) NOT NULL DEFAULT 'en-GB',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

