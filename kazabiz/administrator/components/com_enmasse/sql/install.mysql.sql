CREATE TABLE IF NOT EXISTS `#__enmasse_bill_template` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `slug_name` varchar(225) COLLATE utf8_unicode_ci NOT NULL,
  `avail_attribute` varchar(225) COLLATE utf8_unicode_ci NOT NULL,
  `content` text COLLATE utf8_unicode_ci,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug_name` (`slug_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

INSERT IGNORE INTO `#__enmasse_bill_template` (`id`, `slug_name`, `avail_attribute`, `content`, `created_at`, `updated_at`) VALUES
(1, 'buyer_receipt', '[BUYER_NAME], [BUYER_EMAIL], [BILL_NUMBER], [BILL_DATE], [BILL_DETAIL], [BILL_DESCRIPTION]', '<div style="position: absolute; left: 257px; top: 96px; width: 514px; height: 414px;"><img src="images/sampledata/fruitshop/bananas_2.jpg" border="0" alt="" style="left: 1px; width: 446px;" /></div>\r\n<div style="position: absolute; left: 256px; top: 197px; width: 239px; height: 165px;" title="admin address area 1">\r\n<p>Mc-Well Deal | Am Altheimer Eck 5 | 80331 München</p>\r\n<p>[BUYER_NAME]<br />[BUYER_EMAIL]</p>\r\n</div>\r\n<div style="position: absolute; left: 497px; top: 20px; width: 273px; height: auto;">\r\n<p style="text-align: left;"> </p>\r\n<p style="text-align: left;"> </p>\r\n<p style="text-align: left;">Mc-Well Deal<br />Altheimer Eck 5<br />80331 München<br /><br />Telefon: 089 12 00 00 00<br />Mail: info@mc-welldeal.<br />de<br />Web: www.mc-welldeal.<br />de</p>\r\n</div>\r\n<p style="text-align: center;"> </p>\r\n<p> </p>\r\n<p> </p>\r\n<p> </p>\r\n<p> </p>\r\n<p> </p>\r\n<p> </p>\r\n<p> </p>\r\n<p> </p>\r\n<p> </p>\r\n<p> </p>\r\n<p> </p>\r\n<p> </p>\r\n<p> </p>\r\n<p style="padding-left: 300px;"><strong><span style="font-size: medium;">BILL (German: RECHNUNG)</span></strong></p>\r\n<p style="text-align: center; padding-left: 450px;"><strong><span style="font-size: medium;"><br /></span></strong></p>\r\n<div style="position: absolute; left: 497px; top: 300px; width: 273px; height: 60px;">\r\n<p>Number :             [BILL_NUMBER]<br />Date :                 [BILL_DATE]</p>\r\n</div>\r\n<p> </p>\r\n<p> </p>\r\n<div style="position: absolute; text-align: left; width: 736px; height: 400px; top: 480px; left: 33px;">\r\n<p style="text-align: left;">[BILL_DETAIL]</p>\r\nPayment Method:   [PAYMENT_METHOD]\r\n<p> </p>\r\n<p> </p>\r\n<p>Note: [BILL_DESCRIPTION]</p>\r\n<p style="text-align: left;"> </p>\r\n<p> </p>\r\n<p> </p>\r\n<p style="text-align: left;"> </p>\r\n<p style="text-align: left;"> </p>\r\n<p style="text-align: left;"> </p>\r\n<p style="text-align: left;">Mc-Well Deal | Am Altheimer Eck 5 | 80331 München</p>\r\n</div>', '2011-09-05 13:03:46', '2011-11-03 04:35:56');

CREATE TABLE IF NOT EXISTS `#__enmasse_category` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `parent_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `published` tinyint(1) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__enmasse_coupon_element` (
  `id` bigint(255) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `x` int(11) NOT NULL,
  `y` int(11) NOT NULL,
  `font_size` int(2) NOT NULL,
  `width` int(2) NOT NULL,
  `height` int(2) NOT NULL,
  `published` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=7 ;

INSERT IGNORE INTO `#__enmasse_coupon_element` (`id`, `name`, `x`, `y`, `font_size`, `width`, `height`, `published`) VALUES
(1, 'dealName', 12, 132, 20, 654, 72, 1),
(2, 'serial', 422, 59, 12, 200, 70, 1),
(3, 'merchantName', 335, 245, 14, 329, 50, 1),
(4, 'highlight', 16, 321, 10, 310, 66, 1),
(5, 'personName', 15, 244, 14, 310, 51, 1),
(6, 'term', 336, 322, 10, 327, 67, 1),
(7 , 'qr_code', '557', '35', '12', '80', '80', '1');

CREATE TABLE IF NOT EXISTS `#__enmasse_deal` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `deal_code` varchar(12) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `slug_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `short_desc` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `highlight` text COLLATE utf8_unicode_ci NOT NULL,
  `pic_dir` varchar(550) COLLATE utf8_unicode_ci NOT NULL,
  `terms` text COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `origin_price` decimal(10,2) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `min_needed_qty` int(11) NOT NULL,
  `max_buy_qty` int(11) NOT NULL,
  `max_coupon_qty` int(11) NOT NULL DEFAULT '-1',
  `max_qty` int(11) NOT NULL,
  `cur_sold_qty` int(11) NOT NULL,
  `start_at` datetime DEFAULT NULL,
  `end_at` datetime DEFAULT NULL,
  `merchant_id` bigint(20) DEFAULT NULL,
  `sales_person_id` bigint(20) DEFAULT NULL,
  `status` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'On Sales',
  `published` tinyint(1) NOT NULL,
  `position` int(11) NOT NULL,
  `pay_by_point` tinyint(4) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `prepay_percent` tinyint(1) DEFAULT '100',
  `auto_confirm` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `slug_name` (`slug_name`),
  KEY `merchant_id_idx` (`merchant_id`),
  KEY `deal_code` (`deal_code`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__enmasse_deal_category` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `deal_id` int(20) NOT NULL,
  `category_id` int(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__enmasse_deal_location` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `deal_id` int(20) NOT NULL,
  `location_id` int(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__enmasse_delivery_gty` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `class_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

INSERT IGNORE INTO `#__enmasse_delivery_gty` (`id`, `name`, `class_name`, `created_at`, `updated_at`) VALUES
(1, 'Email', 'email', '2010-10-25 12:00:00', '2010-10-25 12:00:00');

CREATE TABLE IF NOT EXISTS `#__enmasse_email_template` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `slug_name` varchar(225) COLLATE utf8_unicode_ci NOT NULL,
  `avail_attribute` varchar(225) COLLATE utf8_unicode_ci NOT NULL,
  `subject` varchar(225) COLLATE utf8_unicode_ci DEFAULT NULL,
  `content` text COLLATE utf8_unicode_ci,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug_name` (`slug_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6 ;

INSERT IGNORE INTO `#__enmasse_email_template` (`id`, `slug_name`, `avail_attribute`, `subject`, `content`, `created_at`, `updated_at`) VALUES
(1, 'receipt', '$buyerName, $buyerEmail, $deliveryName, $deliveryEmail, $orderId, $dealName, $totalPrice, $createdAt', 'You have made an Order', '<p>Hi $buyerName,</p>\r\n<p>You have made an Order at EnMasse with following detail:</p>\r\n<table border="0">\r\n<tr><td><b>Order:</b><td><td>$orderId</td></tr>\r\n<tr><td><b>Deal:</b><td><td>$dealName</td></tr>\r\n<tr><td><b>Total Qty:</b><td><td>$totalQty</td></tr>\r\n<tr><td><b>Total Price:</b><td><td>$totalPrice</td></tr>\r\n<tr><td><b>Purchase Date:</b><td><td>$createdAt</td></tr>\r\n<tr><td><b>Delivery:</b><td><td>$deliveryName ($deliveryEmail)</td></tr>\r\n</table>', '0000-00-00 00:00:00', '2011-04-15 10:10:28'),
(2, 'confirm_deal_buyer', '$orderId, $dealName, $buyerName, $deliveryName, $deliveryEmail', 'Deal $dealName has been confirmed.', '<p>Hi $buyerName,</p>\r\n<p>Your deal $dealName you ordered has been confirmed.</p>\r\n<p>The coupon will be delivered to $deliveryName ($deliveryEmail)</p>\r\nOrder Id: $orderId', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(3, 'confirm_deal_receiver', '$orderId, $dealName, $buyerName, $deliveryName, $deliveryMsg, $linkToCoupon', 'Receive your coupon !!!', '<p>Hi $deliveryName,</p>\r\n<p>\r\n$buyerName has bought you a set of coupon for <a href="$linkToCoupon" target="_blank">$dealName</a></p>\r\n<p>$deliveryMsg</p>\r\n<br/>\r\n<font size=''1''>Please go to <a href="$linkToCoupon" target="_blank">$linkToCoupon</a> if the hyperlink has being blocked.</font>\r\n', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(4, 'void_deal', '$buyerName, $orderId, $dealName, $refundAmt', 'Deal $dealName has been canceled', '<p>Hi $buyerName,</p>\r\n<p>The Order($orderId) for deal $dealName has been cancel.</p>\r\n<p>$refundAmt will be refunded to you.</p>', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(5, 'void_deal_with_point', '$buyerName, $orderId, $dealName, $refundAmt, $refundPoint', 'Deal $dealName has been canceled', '<p>Hi $buyerName,</p>\r\n<p>The Order($orderId) for deal $dealName has been cancel.</p>\r\n<p>$refundAmt cash and $refundPoint point(s) will be refunded to you.</p>\r\n<p>However you can get all the refund in point by going to My Orders page and choose the amount of point you want to get back.</p>', '0000-00-00 00:00:00', '0000-00-00 00:00:00');

CREATE TABLE IF NOT EXISTS `#__enmasse_invty` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `order_item_id` bigint(20) NOT NULL,
  `pdt_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `deallocated_at` datetime NOT NULL,
  `status` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `settlement_status` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Not_Paid_Out',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__enmasse_location` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `parent_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `published` tinyint(1) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

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

CREATE TABLE IF NOT EXISTS `#__enmasse_order` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `total_buyer_paid` decimal(10,2) DEFAULT NULL,
  `point_used_to_pay` int(11) NOT NULL DEFAULT '0',
  `status` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `refunded_amount` int(11) NOT NULL,
  `session_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `buyer_id` bigint(20) DEFAULT NULL,
  `buyer_detail` longtext COLLATE utf8_unicode_ci,
  `referral_id` bigint(20) NOT NULL,
  `pay_gty_id` bigint(20) DEFAULT NULL,
  `pay_detail` longtext COLLATE utf8_unicode_ci,
  `delivery_gty_id` bigint(20) DEFAULT NULL,
  `delivery_detail` longtext COLLATE utf8_unicode_ci,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `paid_amount` decimal(10,2) DEFAULT '0.00',
  PRIMARY KEY (`id`),
  KEY `buyer_id_idx` (`buyer_id`),
  KEY `pay_gty_id_idx` (`pay_gty_id`),
  KEY `delivery_gty_id_idx` (`delivery_gty_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__enmasse_order_deliverer` (
  `order_id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `status` varchar(50) COLLATE utf8_unicode_ci DEFAULT 'undelivered',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`order_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__enmasse_order_item` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `signature` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `unit_price` decimal(10,2) DEFAULT NULL,
  `qty` bigint(20) DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  `pdt_id` bigint(20) DEFAULT NULL,
  `pdt_promo_id` bigint(20) DEFAULT NULL,
  `order_id` bigint(20) DEFAULT NULL,
  `status` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `pdt_id_idx` (`pdt_id`),
  KEY `pdt_promo_id_idx` (`pdt_promo_id`),
  KEY `order_id_idx` (`order_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__enmasse_pay_gty` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8_unicode_ci NOT NULL,
  `class_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `attributes` longtext COLLATE utf8_unicode_ci NOT NULL,
  `attribute_config` longtext COLLATE utf8_unicode_ci NOT NULL,
  `published` tinyint(1) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `class_name` (`class_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=8 ;

INSERT IGNORE INTO `#__enmasse_pay_gty` (`id`, `name`, `description`, `class_name`, `attributes`, `attribute_config`, `published`, `created_at`, `updated_at`) VALUES
(1, 'Cash / Bank Transfer', '', 'cash', 'instruction', '{"instruction":"<p>Dear customers,<\\/p>\\r\\n<p>Cash\\/Bank Transfer payment is only convenient for customers living in Singapore. For overseas payment, we would like to encourage users to pay through\\u00a0Credit\\/Debit card or\\u00a0PayPal option.<\\/p>\\r\\n<p>For payment through Cash\\/Bank Transfer, please kindly follow these steps:<\\/p>\\r\\n<ol>\\r\\n<li>Go to your nearest ATM or online iBanking and transfer the payment to account: 123-234456-7<\\/li>\\r\\n<li>Print screen your transfer page if you are using iBanking, or get a receipt from the machine if you transfer through ATM<\\/li>\\r\\n<li>Email us the image of the receipt\\/print screen and kindly state the reference no.<\\/li>\\r\\n<li>We will mark your order as paid as soon when we receive your email.<\\/li>\\r\\n<li>Payment is to be done within 7 days from the date of purchase or else your order will be cancelled automatically.<\\/li>\\r\\n<\\/ol>\\r\\n<p>Thank you!<\\/p>"}', 1, '0000-00-00 00:00:00', '2011-12-08 11:37:43'),
(2, 'Credit Card / Debit Card / Paypal', '<p><img src="./components/com_enmasse/images/paypal_logo.png"/><p><br/><br/><p>Matamko''s merchants integrate PayPal as their payment option to increase their sales, expand globally, attract more buyers, and keep their business secure. PayPal is a global leader in online payments with a total payment volume of US$71 billion in 2009 - approximately 15% of global ecommerce and 16.5% of US ecommerce.<br/><a href="https://www.paypal.com/sg/mrb/pal=HUASLHP6T2UVU&mrb=R-6BG16433XS203062L" target="_blank">Click Here</a> to Register Your PayPal Merchant Account to start your social buying site today!</p>\r\n', 'paypal', 'merchant_email,api_username,signature,country_code,currency_code', '{"merchant_email":"account@matamko.com","api_username":"account_api1.matamko.com","signature":"AHTJGXIeu6pqPQTY4IgtDMcydaNXABXByLeQ.ZUvtfSMkGyt4.jLJyZ-","country_code":"SG","currency_code":"SGD"}', 1, '0000-00-00 00:00:00', '2011-12-08 11:37:57'),
(3, 'Point payment', '<p>This payment is only used when users buy a deal by all points (integration with point systems link AlphaUserPoints).</p>', 'point', 'instruction', '{"instruction":"<p>Dear customers,<\\/p>\\r\\n<p>You have just did a payment with points, no cash is paid. In future if this order is refunded, points will be given back to you automatically.<\\/p>\\r\\n<p>Thank you!<\\/p>"}', 1, '0000-00-00 00:00:00', '2011-12-08 11:38:01'),
(4, 'Authorize.net', '', 'authorizenet', 'api_login_id,transaction_key,sandbox,type', '{"api_login_id":"your API login","transaction_key":"your transaction key","sandbox":"\\"true\\" or \\"false\\"","type":"\\"AUTH_CAPTURE\\" or \\"AUTH_ONLY\\""}', 1, '2012-02-13 16:30:24', '2012-02-13 16:30:24'),
(5, 'eWay', '', 'ewayhosted', 'ewayCustomerID', '{"ewayCustomerID":"your customer ID"}', 1, '2012-02-13 16:31:02', '2012-02-13 16:31:02'),
(6, '2Checkout', '', 'twocheckout', 'sid,secret_word,demo', '{"sid":"your sid","secret_word":"your secret word","demo":"\\"Y\\" or \\"N\\""}', 1, '2012-02-13 16:32:08', '2012-02-13 16:32:08'),
(7, 'MoneyBookes', '', 'moneybookers', 'merchant_email,language_code,country_code,currency_code', '{"merchant_email":"your email","language_code":"language code","country_code":"country code","currency_code":"currency code"}', 1, '2012-02-13 16:35:58', '2012-02-13 16:35:58'),
(8, 'Pay Fast', '', 'payfast', 'merchant_id,merchant_key,sandbox', '{"merchant_id":"MERCHANT_ID","merchant_key":"MERCHANT_KEY","sandbox":"TRUE_OR_FALSE"}', 1, '2012-03-16 04:17:35', '2012-03-19 03:03:28'),
(9, 'World Pay', '', 'worldpay', 'transId,currency,testMode', '{"transId":"TRANS_ID","currency":"USD","testMode":"100_FOR_TEST_OR_0_FOR_LIVE"}', 1, '2012-03-20 06:53:23', '2012-03-20 09:37:53'),
(10, 'Ogone', '', 'ogone', 'pspid,currency_code,language,in_passphrase,out_passphrase', '{"pspid":"id_merchant","currency_code":"USD","language":"en_US","in_passphrase":"SHA-IN Pass phrase","out_passphrase":"SHA-OUT Pass phrase"}', 1, '2012-03-22 06:12:50', '2012-03-22 06:12:50'),
(11, 'GoogleCheckOut', '', 'google-check-out', 'MerchantID,MerchantKey,Currency', '{"MerchantID":"624370439819433","MerchantKey":"r86DDFDkmjzrqrr7kW","Currency":"USD"}', 1, '2012-03-22 11:54:36', '2012-03-22 11:55:00'),
(12, 'Pagseguro', '', 'pagseguro', 'merchant_email', '{"merchant_email":"Merchant Email"}', 1, '2012-03-23 08:54:01', '2012-03-23 08:54:01');

CREATE TABLE IF NOT EXISTS `#__enmasse_sales_person` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `user_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `published` tinyint(1) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__enmasse_setting` (
  `company_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `address1` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `address2` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `city` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `state` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `country` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `postal_code` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `tax` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `tax_number1` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `tax_number2` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `logo_url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `contact_number` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `contact_fax` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `customer_support_email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `default_currency` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `currency_prefix` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `currency_postfix` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `currency_decimal` tinyint(2) NOT NULL,
  `currency_separator` varchar(1) COLLATE utf8_unicode_ci NOT NULL,
  `currency_decimal_separator` varchar(1) COLLATE utf8_unicode_ci NOT NULL,
  `image_height` int(5) NOT NULL,
  `image_width` int(5) NOT NULL,
  `article_id` int(5) NOT NULL,
  `subscription_class` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `theme` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `mobile_theme` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `coupon_bg_url` varchar(225) COLLATE utf8_unicode_ci NOT NULL,
  `minute_release_invty` int(2) NOT NULL,
  `cash_minute_release_invty` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `point_system_class` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `active_guest_buying` tinyint(1) NOT NULL DEFAULT '0',
  `sale_group` int(11) NOT NULL,
  `merchant_group` int(11) NOT NULL,
  `sending_bill_auto` tinyint(1) DEFAULT '1',
  `delivery_group` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `logo_url` (`logo_url`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;


INSERT IGNORE INTO `#__enmasse_setting` (`company_name`, `id`, `address1`, `address2`, `city`, `state`, `country`, `postal_code`, `tax`, `tax_number1`, `tax_number2`, `logo_url`, `contact_number`, `contact_fax`, `customer_support_email`, `default_currency`, `currency_prefix`, `currency_postfix`, `currency_decimal`, `currency_separator`, `currency_decimal_separator`, `image_height`, `image_width`, `article_id`, `subscription_class`, `theme`, `mobile_theme`, `coupon_bg_url`, `minute_release_invty`, `cash_minute_release_invty`, `created_at`, `updated_at`, `point_system_class`, `active_guest_buying`, `sale_group`, `merchant_group`, `sending_bill_auto`, `delivery_group`) VALUES
('Your company name', 1, 'Your company''s address', '', 'Singapore', 'Singapore', 'SG', '12345', '', '', '', '', '', '', 'support@yourcompany.com', 'USD', '$', '', 2, ',', '.', 252, 400, 1, 'acystarter', 'dark_blue', 'mobile', 'a%3A1%3A%7Bi%3A0%3Bs%3A51%3A%22components%5Ccom_enmasse%5Cupload%5C18040samplecoupon.png%22%3B%7D', 10, 2, '0000-00-00 00:00:00', '2012-01-06 14:28:23', 'no', 0, 9, 10, 0, 11);

CREATE TABLE IF NOT EXISTS `#__enmasse_tax` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `tax_rate` double NOT NULL,
  `published` tinyint(1) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

ALTER TABLE `#__enmasse_deal`
ADD ( `commission_percent` tinyint(1) DEFAULT 0);