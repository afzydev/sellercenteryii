UPDATE `shopdev`.`ps_configuration` SET `value` = 'PS' WHERE `ps_configuration`.`id_configuration` = 42;

INSERT INTO `ps_configuration` (`id_configuration`, `id_shop_group`, `id_shop`, `name`, `value`, `variable_type`, `date_add`, `date_upd`) VALUES
(NULL, NULL, 1, 'PS_OS_ORDERCONFIRMATION', '2', 'custom', '2016-01-21 00:00:00', '2016-01-21 00:00:00'),
(NULL, NULL, 2, 'PS_OS_ORDERCONFIRMATION', '2', 'custom', '2016-01-21 00:00:00', '2016-01-21 00:00:00');


INSERT INTO `ps_configuration` (`id_configuration`, `id_shop_group`, `id_shop`, `name`, `value`, `variable_type`, `date_add`, `date_upd`) VALUES
(NULL, NULL, 1, 'PS_OS_HANDEDOVERTOCOURIER', '23', 'custom', '2016-01-21 00:00:00', '2016-01-21 00:00:00'),
(NULL, NULL, 2, 'PS_OS_HANDEDOVERTOCOURIER', '23', 'custom', '2016-01-21 00:00:00', '2016-01-21 00:00:00');


INSERT INTO `ps_configuration` (`id_configuration`, `id_shop_group`, `id_shop`, `name`, `value`, `variable_type`, `date_add`, `date_upd`) VALUES
(NULL, NULL, NULL, 'PS_INVOICE_PREFIX', 'PS', 'system', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(NULL, NULL, NULL, 'PS_INVOICE_NUMBER', '1', 'system', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(NULL, NULL, NULL, 'PS_INVOICE', '1', 'system', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(NULL, NULL, NULL, 'PS_INVOICE_MODEL', 'invoice', 'system', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(NULL, 1, 1, 'PS_LOGO_INVOICE', 'cardekho-shop-1430732839-1.jpg', 'system', '2015-05-04 15:17:19', '2015-05-04 15:17:19'),
(NULL, 1, 3, 'PS_LOGO_INVOICE', 'gaadi-shop-1450847132-3.jpg', 'system', '2015-12-23 10:35:32', '2015-12-23 10:35:32');


INSERT INTO `ps_configuration` (`id_configuration`, `id_shop_group`, `id_shop`, `name`, `value`, `variable_type`, `date_add`, `date_upd`) VALUES
(NULL, NULL, 1, 'LOW_IN_STOCK_QUANTITY', '5', 'custom', '2016-01-22 00:00:00', '2016-01-22 00:00:00'),
(NULL, NULL, 2, 'LOW_IN_STOCK_QUANTITY', '5', 'custom', '2016-01-22 00:00:00', '2016-01-22 00:00:00');

INSERT INTO `ps_configuration` (`id_configuration`, `id_shop_group`, `id_shop`, `name`, `value`, `variable_type`, `date_add`, `date_upd`) VALUES
(NULL, NULL, 2, 'PS_SHIPPED', '4', 'custom', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(NULL, NULL, 1, 'PS_SHIPPED', '4', 'custom', '0000-00-00 00:00:00', '0000-00-00 00:00:00');