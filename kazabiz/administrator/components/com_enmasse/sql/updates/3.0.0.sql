INSERT INTO `#__enmasse_coupon_element` (`id` ,`name` ,`x` ,`y` ,`font_size` ,`width` ,`height` ,`published`)
VALUES (NULL , 'qr_code', '557', '35', '12', '80', '80', '1');

INSERT INTO `#__enmasse_pay_gty` (`id`, `name`, `description`, `class_name`, `attributes`, `attribute_config`, `published`, `created_at`, `updated_at`) VALUES
(NULL, 'Pay Fast', '', 'payfast', 'merchant_id,merchant_key,sandbox', '{"merchant_id":"MERCHANT_ID","merchant_key":"MERCHANT_KEY","sandbox":"TRUE_OR_FALSE"}', 1, '2012-03-16 04:17:35', '2012-03-19 03:03:28'),
(NULL, 'World Pay', '', 'worldpay', 'transId,currency,testMode', '{"transId":"TRANS_ID","currency":"USD","testMode":"100_FOR_TEST_OR_0_FOR_LIVE"}', 1, '2012-03-20 06:53:23', '2012-03-20 09:37:53'),
(NULL, 'Ogone', '', 'ogone', 'pspid,currency_code,language,in_passphrase,out_passphrase', '{"pspid":"id_merchant","currency_code":"USD","language":"en_US","in_passphrase":"SHA-IN Pass phrase","out_passphrase":"SHA-OUT Pass phrase"}', 1, '2012-03-22 06:12:50', '2012-03-22 06:12:50'),
(NULL, 'GoogleCheckOut', '', 'google-check-out', 'MerchantID,MerchantKey,Currency', '{"MerchantID":"624370439819433","MerchantKey":"r86DDFDkmjzrqrr7kW","Currency":"USD"}', 1, '2012-03-22 11:54:36', '2012-03-22 11:55:00'),
(NULL, 'Pagseguro', '', 'pagseguro', 'merchant_email', '{"merchant_email":"Merchant Email"}', 1, '2012-03-23 08:54:01', '2012-03-23 08:54:01');

ALTER TABLE `#__enmasse_deal`
ADD ( `commission_percent` tinyint(1) DEFAULT 0);