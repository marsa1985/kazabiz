CREATE TABLE IF NOT EXISTS `#__enmasse_ws_session` (
  `token` varchar(50) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `merchant_id` bigint(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `expired_at` datetime NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `#__enmasse_ws_session`
--

INSERT INTO `#__enmasse_ws_session` (`token`, `user_id`, `merchant_id`, `created_at`, `expired_at`) VALUES
('b81067ed0802fd1444e43e79f1872109', 44, 1, '2012-04-26 17:12:07', '2012-04-26 17:17:09');