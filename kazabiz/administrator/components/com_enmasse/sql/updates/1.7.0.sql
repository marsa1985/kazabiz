
ALTER TABLE `#__enmasse_deal` ADD `deal_code` VARCHAR( 20 ) NOT NULL AFTER `id` , ADD `pay_by_point` tinyint(4) NOT NULL DEFAULT '0' ;
ALTER TABLE `#__enmasse_deal` ADD INDEX ( `deal_code` ) ;

CREATE TABLE #__enmasse_deal_new LIKE #__enmasse_deal;
INSERT #__enmasse_deal_new SELECT * FROM #__enmasse_deal;

UPDATE #__enmasse_deal SET deal_code = concat('DE', Date_Format(created_at, '%y%m'), '-', LPAD((SELECT COUNT(id) 
		      FROM #__enmasse_deal_new AS dn
		      WHERE dn.created_at < #__enmasse_deal.created_at AND month(dn.created_at) = month(#__enmasse_deal.created_at) ) + 1, 5, '0'));

DROP TABLE #__enmasse_deal_new;

ALTER TABLE `#__enmasse_setting`  ADD `sale_group` INT NOT NULL,  ADD `merchant_group` INT NOT NULL, ADD `active_guest_buying` tinyint(1) NOT NULL DEFAULT '0';

CREATE TABLE IF NOT EXISTS `#__enmasse_merchant_branch` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `user_name` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `sales_person_id` bigint(20) NOT NULL,
  `web_url` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `logo_url` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `location_id` int(11) NOT NULL,
  `description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `google_map_width` float NOT NULL,
  `google_map_height` float NOT NULL,
  `google_map_zoom` tinyint(4) NOT NULL,
  `branches` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `published` tinyint(1) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
