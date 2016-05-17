ALTER TABLE `phppos_receivings`
ADD COLUMN `transfer_status`  enum('rejected','approved','pending') NULL DEFAULT 'pending' AFTER `transfer_to_location_id`;

INSERT INTO `phppos_modules_actions` (`action_id`, `module_id`, `action_name_key`, `sort`) VALUES ('transfer_pending', 'items', 'items_transfer_pending', 1000)