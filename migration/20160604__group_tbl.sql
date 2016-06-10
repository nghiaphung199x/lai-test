CREATE TABLE IF NOT EXISTS `phppos_groups` (
  `group_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `description` varchar(525) DEFAULT NULL,
  `deleted` tinyint(1) unsigned DEFAULT '0',
  PRIMARY KEY (`group_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

CREATE TABLE IF NOT EXISTS `phppos_group_permissions` (
  `module_id` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `group_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`module_id`,`group_id`),
  KEY `phppos_group_permissions_module_id` (`module_id`),
  KEY `phppos_group_permissions_group_id` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `phppos_group_permissions_actions` (
  `module_id` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `group_id` int(10) unsigned NOT NULL,
  `action_id` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`module_id`,`group_id`,`action_id`),
  KEY `phppos_group_permissions_module_id` (`module_id`),
  KEY `phppos_group_permissions_group_id` (`group_id`),
  KEY `phppos_group_permissions_action_id` (`action_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `phppos_modules_actions`
ADD INDEX `action_id` (`action_id`);

ALTER TABLE `phppos_group_permissions`
ADD CONSTRAINT `phppos_group_permissions_fk_group_id` FOREIGN KEY (`group_id`) REFERENCES `phppos_groups` (`group_id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `phppos_group_permissions_fk_module_id` FOREIGN KEY (`module_id`) REFERENCES `phppos_modules` (`module_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `phppos_group_permissions_actions`
ADD CONSTRAINT `phppos_group_permissions_actions_fk_group_id` FOREIGN KEY (`group_id`) REFERENCES `phppos_groups` (`group_id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `phppos_group_permissions_actions_fk_module_id` FOREIGN KEY (`module_id`) REFERENCES `phppos_modules` (`module_id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `phppos_group_permissions_actions_fk_action_id` FOREIGN KEY (`action_id`) REFERENCES `phppos_modules_actions` (`action_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `phppos_employees`
ADD `group_id` INT(10) UNSIGNED NOT NULL AFTER `person_id`,
ADD INDEX (group_id);