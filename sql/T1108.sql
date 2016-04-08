CREATE TABLE IF NOT EXISTS `site_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `config_key` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  `label` varchar(60) NOT NULL,
  `type` enum('API_URL','System','Custom') NOT NULL DEFAULT 'System',
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;