-- --------------------------------------------------------

--
-- Table structure for table `phppos_attributes`
--

CREATE TABLE IF NOT EXISTS `phppos_attributes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `description` varchar(525) NOT NULL,
  `type` int(4) unsigned NOT NULL,
  `sortable` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `filterable` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `required` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `options` blob NOT NULL,
  `sort_order` smallint(4) unsigned NOT NULL,
  `deleted` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `deleted` (`deleted`),
  KEY `type` (`type`),
  KEY `sort_order` (`sort_order`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=18 ;

-- --------------------------------------------------------

--
-- Table structure for table `phppos_attribute_groups`
--

CREATE TABLE IF NOT EXISTS `phppos_attribute_groups` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` varchar(525) NOT NULL,
  `sort_order` tinyint(4) unsigned NOT NULL,
  `deleted` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `deleted` (`deleted`),
  KEY `sort_order` (`sort_order`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `phppos_attribute_sets`
--

CREATE TABLE IF NOT EXISTS `phppos_attribute_sets` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `description` varchar(525) NOT NULL,
  `deleted` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `deleted` (`deleted`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Table structure for table `phppos_attribute_sets_combined`
--

CREATE TABLE IF NOT EXISTS `phppos_attribute_sets_combined` (
  `attribute_set_id` int(10) unsigned NOT NULL,
  `attribute_group_id` int(10) unsigned NOT NULL,
  `attribute_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`attribute_set_id`,`attribute_group_id`,`attribute_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `phppos_attribute_values`
--

CREATE TABLE IF NOT EXISTS `phppos_attribute_values` (
  `entity_id` int(10) unsigned NOT NULL,
  `entity_type` varchar(255) NOT NULL,
  `attribute_id` int(10) unsigned NOT NULL,
  `entity_value` text NOT NULL,
  PRIMARY KEY (`entity_id`,`entity_type`,`attribute_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `phppos_attribute_sets`
ADD `related_objects` TEXT NOT NULL AFTER `description` ;

ALTER TABLE `phppos_item_kits`
ADD COLUMN `attribute_set_id` int(10) NULL AFTER `item_kit_id`;

ALTER TABLE `phppos_items`
ADD COLUMN `attribute_set_id` int(10) NULL AFTER `item_id`;

ALTER TABLE `phppos_suppliers`
ADD COLUMN `attribute_set_id` int(10) NULL AFTER `id`;

ALTER TABLE `phppos_employees`
ADD COLUMN `attribute_set_id` int(10) NULL AFTER `id`;

ALTER TABLE `phppos_customers`
ADD COLUMN `attribute_set_id` int(10) NULL AFTER `id`;

INSERT INTO `phppos_modules` (`name_lang_key`, `desc_lang_key`, `sort`, `icon`, `module_id`, `main_menu`) VALUES
('module_attribute_sets', 'module_attribute_sets_desc', 122, 'settings', 'attribute_sets', 1),
('module_attribute_groups', 'module_attribute_group_desc', 123, 'settings', 'attribute_groups', 0),
('module_attributes', 'module_attributes_desc', 124, 'settings', 'attributes', 0);

INSERT INTO `phppos_modules_actions` (`action_id`, `module_id`, `action_name_key`, `sort`) VALUES
('add_update', 'attribute_groups', 'module_action_add_update', 1),
('add_update', 'attribute_sets', 'module_action_add_update', 1),
('add_update', 'attributes', 'module_action_add_update', 1),
('delete', 'attribute_groups', 'module_action_delete', 2),
('delete', 'attribute_sets', 'module_action_delete', 2),
('delete', 'attributes', 'module_action_delete', 2),
('search', 'attribute_groups', 'module_action_search_attribute_groups', 3),
('search', 'attribute_sets', 'module_action_search_attribute_sets', 3),
('search', 'attributes', 'module_search_attributes', 3);