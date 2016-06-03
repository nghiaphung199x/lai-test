CREATE TABLE IF NOT EXISTS `phppos_departments` (
  `department_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `description` varchar(525) DEFAULT NULL,
  `parent_id` int(10) unsigned DEFAULT '0',
  `path` varchar(255) DEFAULT NULL,
  `deleted` tinyint(1) unsigned DEFAULT '0',
  PRIMARY KEY (`department_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4;

ALTER TABLE `phppos_employees`
ADD `department_id` INT(10) UNSIGNED NOT NULL AFTER `person_id`,
ADD INDEX (department_id);