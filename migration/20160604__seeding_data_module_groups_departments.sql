--DELETE FROM `phppos_permissions_actions` WHERE `module_id` IN ('groups', 'departments');
--DELETE FROM `phppos_permissions` WHERE `module_id` IN ('groups', 'departments');
--DELETE FROM `phppos_modules_actions` WHERE `module_id` IN ('groups', 'departments');
--DELETE FROM `phppos_modules` WHERE `module_id` IN ('groups', 'departments');

INSERT INTO `phppos_modules` (`name_lang_key`, `desc_lang_key`, `sort`, `icon`, `module_id`) VALUES
('module_departments', 'module_departments_desc', 300, 'location-pin', 'departments'),
('module_groups', 'module_groups_desc', 100, 'user', 'groups');

INSERT INTO `phppos_modules_actions` (`action_id`, `module_id`, `action_name_key`, `sort`) VALUES
('add_update', 'departments', 'module_action_add_update', 1),
('delete', 'departments', 'module_action_delete', 2),
('search', 'departments', 'module_action_search_departments', 3),
('add_update', 'groups', 'module_action_add_update', 1),
('delete', 'groups', 'module_action_delete', 2),
('search', 'groups', 'module_action_search_groups', 3);