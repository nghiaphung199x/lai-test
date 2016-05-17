
CREATE TABLE IF NOT EXISTS `phppos_measures` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`name`  varchar(255) NOT NULL ,
`deleted`  tinyint(1) NOT NULL DEFAULT 0 ,
PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `phppos_item_measures` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`item_id`  int(11) NOT NULL ,
`measure_id`  int(11) NOT NULL ,
`measure_converted_id`  int(11) NOT NULL ,
`qty_converted`  int(11) NOT NULL ,
`cost_price_percentage_converted`  decimal(23,0) NULL ,
`unit_price_percentage_converted`  decimal(23,0) NULL ,
PRIMARY KEY (`id`)
);

ALTER TABLE `phppos_items`
ADD COLUMN `measure_converted`  tinyint(1) NULL AFTER `product_id`;

ALTER TABLE `phppos_items`
ADD COLUMN `measure_id`  int(11) NULL AFTER `product_id`;