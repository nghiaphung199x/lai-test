
CREATE TABLE IF NOT EXISTS `phppos_mail_template` (
  `mail_id` int(11) NOT NULL AUTO_INCREMENT,
  `mail_title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `mail_content` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `deleted` int(11) NOT NULL,
  PRIMARY KEY (`mail_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `phppos_mail_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `person_id` int(11) NOT NULL COMMENT 'mã khách hàng',
  `employee_id` int(11) NOT NULL COMMENT 'mã nhân viên',
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `content` text COLLATE utf8_unicode_ci NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `note` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `file` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(4) NOT NULL COMMENT '0: Chưa gửi ; 1: Đã gửi',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;