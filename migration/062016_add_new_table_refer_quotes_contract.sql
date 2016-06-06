
SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for phppos_quotes_contract
-- ----------------------------
CREATE TABLE IF NOT EXISTS `phppos_quotes_contract` (
  `id_quotes_contract` int(11) NOT NULL AUTO_INCREMENT,
  `title_quotes_contract` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `content_quotes_contract` text COLLATE utf8_unicode_ci,
  `cat_quotes_contract` tinyint(2) DEFAULT '0' COMMENT '1: Mẫu hợp đồng - 2: Mẫu báo giá',
  PRIMARY KEY (`id_quotes_contract`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
