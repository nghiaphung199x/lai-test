
INSERT INTO `phppos_modules` (`name_lang_key`, `desc_lang_key`, `sort`, `icon`, `module_id`, `main_menu`) VALUES('module_stock_out', 'module_stock_out_desc', '7', 'printer', 'stock_out', 1);
INSERT INTO `phppos_modules_actions` (`action_id`, `module_id`, `action_name_key`, `sort`) VALUES ('search', 'stock_out', 'stock_out_search', 1010);


CREATE TABLE `phppos_stock_out` (
`id`  int NOT NULL AUTO_INCREMENT,
`sale_id`  int NULL,
`customer_id`  int NULL ,
`deliverer_id`  int NULL ,
`location_id`  int NULL ,
`comment`  text NULL ,
`created_time`  datetime NULL ,
PRIMARY KEY (`id`)
);


CREATE TABLE `phppos_stock_out_items` (
`id`  int NOT NULL AUTO_INCREMENT,
`stock_out_id`  int NULL ,
`item_id`  int NULL ,
`item_kit_id`  int NULL ,
`qty`  int NULL ,
`measure_id`  int NULL ,
PRIMARY KEY (`id`)
);



