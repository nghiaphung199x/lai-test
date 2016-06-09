ALTER TABLE `phppos_modules`
ADD `main_menu` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1';

UPDATE `phppos_modules` SET `main_menu` = 0 WHERE `module_id` IN ('groups', 'departments');