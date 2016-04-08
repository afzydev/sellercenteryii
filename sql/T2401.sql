INSERT INTO `ps_configuration` (`id_configuration`, `id_shop_group`, `id_shop`, `name`, `value`, `variable_type`, `date_add`, `date_upd`) VALUES
(NULL, NULL, NULL, 'PS_OS_RTO_INITIATED_DELIVERED', '24,25', 'system', '0000-00-00 00:00:00', '0000-00-00 00:00:00');
INSERT INTO `ps_configuration` (`id_configuration`, `id_shop_group`, `id_shop`, `name`, `value`, `variable_type`, `date_add`, `date_upd`) VALUES
(NULL, NULL, NULL, 'PS_ON_BACKORDER', '9', 'system', '2016-03-30 00:00:00', '2016-03-30 00:00:00');
CREATE TABLE IF NOT EXISTS `order_status_api_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_employee` int(11) NOT NULL,
  `id_order` varchar(255) NOT NULL,
  `id_order_state` int(11) NOT NULL,
  `return_msg` varchar(100) NOT NULL,
  `individual_return_status` varchar(100) NOT NULL,
  `overall_return_status` varchar(100) NOT NULL,
  `date_add` datetime NOT NULL,
  PRIMARY KEY (`id`)
)