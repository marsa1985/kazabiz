CREATE TABLE IF NOT EXISTS `#__enmasse_bill_template` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `slug_name` varchar(225) COLLATE utf8_unicode_ci NOT NULL,
  `avail_attribute` varchar(225) COLLATE utf8_unicode_ci NOT NULL,
  `content` text COLLATE utf8_unicode_ci,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug_name` (`slug_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
INSERT IGNORE INTO `#__enmasse_bill_template` (`id`, `slug_name`, `avail_attribute`, `content`, `created_at`, `updated_at`) VALUES
(1, 'buyer_receipt', '[BUYER_NAME], [BUYER_EMAIL], [BILL_NUMBER], [BILL_DATE], [BILL_DETAIL], [BILL_DESCRIPTION]', '<div style="position: absolute; left: 257px; top: 96px; width: 514px; height: 414px;"><img src="images/sampledata/fruitshop/bananas_2.jpg" border="0" alt="" style="left: 1px; width: 446px;" /></div>\r\n<div style="position: absolute; left: 256px; top: 197px; width: 239px; height: 165px;" title="admin address area 1">\r\n<p>Mc-Well Deal | Am Altheimer Eck 5 | 80331 München</p>\r\n<p>[BUYER_NAME]<br />[BUYER_EMAIL]</p>\r\n</div>\r\n<div style="position: absolute; left: 497px; top: 20px; width: 273px; height: auto;">\r\n<p style="text-align: left;"> </p>\r\n<p style="text-align: left;"> </p>\r\n<p style="text-align: left;">Mc-Well Deal<br />Altheimer Eck 5<br />80331 München<br /><br />Telefon: 089 12 00 00 00<br />Mail: info@mc-welldeal.<br />de<br />Web: www.mc-welldeal.<br />de</p>\r\n</div>\r\n<p style="text-align: center;"> </p>\r\n<p> </p>\r\n<p> </p>\r\n<p> </p>\r\n<p> </p>\r\n<p> </p>\r\n<p> </p>\r\n<p> </p>\r\n<p> </p>\r\n<p> </p>\r\n<p> </p>\r\n<p> </p>\r\n<p> </p>\r\n<p> </p>\r\n<p style="padding-left: 300px;"><strong><span style="font-size: medium;">BILL (German: RECHNUNG)</span></strong></p>\r\n<p style="text-align: center; padding-left: 450px;"><strong><span style="font-size: medium;"><br /></span></strong></p>\r\n<div style="position: absolute; left: 497px; top: 300px; width: 273px; height: 60px;">\r\n<p>Number :             [BILL_NUMBER]<br />Date :                 [BILL_DATE]</p>\r\n</div>\r\n<p> </p>\r\n<p> </p>\r\n<div style="position: absolute; text-align: left; width: 736px; height: 400px; top: 480px; left: 33px;">\r\n<p style="text-align: left;">[BILL_DETAIL]</p>\r\nPayment Method:   [PAYMENT_METHOD]\r\n<p> </p>\r\n<p> </p>\r\n<p>Note: [BILL_DESCRIPTION]</p>\r\n<p style="text-align: left;"> </p>\r\n<p> </p>\r\n<p> </p>\r\n<p style="text-align: left;"> </p>\r\n<p style="text-align: left;"> </p>\r\n<p style="text-align: left;"> </p>\r\n<p style="text-align: left;">Mc-Well Deal | Am Altheimer Eck 5 | 80331 München</p>\r\n</div>', '2011-09-05 13:03:46', '2011-11-03 04:35:56');

ALTER TABLE `#__enmasse_setting` 
	ADD ( `sending_bill_auto` tinyint(1) DEFAULT 1);
	
ALTER TABLE `#__enmasse_setting` 
	ADD ( `delivery_group` bigint(20) DEFAULT NULL);
	
ALTER TABLE `#__enmasse_setting` 
	ADD ( `mobile_theme` varchar(50) COLLATE utf8_unicode_ci NOT NULL);	
	
ALTER TABLE `#__enmasse_invty` 
	ADD ( `settlement_status` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Not_Paid_Out');

CREATE TABLE IF NOT EXISTS `#__enmasse_comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `deal_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment` varchar(1000) NOT NULL,
  `rating` tinyint(1) NOT NULL,
  `created_at` datetime NOT NULL,
  `status` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__enmasse_comment_spammer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
	
ALTER TABLE `#__enmasse_deal` 
	ADD ( `prepay_percent` tinyint(1) DEFAULT 100);

ALTER TABLE `#__enmasse_deal` 
	ADD ( `auto_confirm` tinyint(1) DEFAULT 0);

ALTER TABLE `#__enmasse_order` 
	ADD ( `paid_amount` decimal(10,2) DEFAULT 0.00);

UPDATE 	`#__enmasse_order` SET `paid_amount` = `total_buyer_paid` WHERE `status` = 'Paid';
CREATE TABLE IF NOT EXISTS `#__enmasse_order_deliverer` (
  `order_id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `status` varchar(50) DEFAULT 'undelivered',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`order_id`, `user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;