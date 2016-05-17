INSERT INTO `phppos_modules_actions` (`action_id`, `module_id`, `action_name_key`, `sort`) VALUES ('view_scope_owner', 'customers', 'module_view_scope_owner', 1000);
INSERT INTO `phppos_modules_actions` (`action_id`, `module_id`, `action_name_key`, `sort`) VALUES ('view_scope_location', 'customers', 'module_view_scope_location', 1001);
INSERT INTO `phppos_modules_actions` (`action_id`, `module_id`, `action_name_key`, `sort`) VALUES ('view_scope_all', 'customers', 'module_view_scope_all', 1002);

ALTER TABLE `phppos_customers` ADD COLUMN `created_location_id`  int(10) NULL AFTER `created_by`;