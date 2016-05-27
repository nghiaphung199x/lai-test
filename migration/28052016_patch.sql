
ALTER TABLE `phppos_receivings_items` ADD COLUMN `measure_id`  int(11) NULL AFTER `quantity_received`;

ALTER TABLE `phppos_receivings_items`
ADD COLUMN `measure_qty`  int(11) NULL AFTER `measure_id`;
ALTER TABLE `phppos_receivings_items`
ADD COLUMN `measure_qty_received`  int(11) NULL AFTER `measure_qty`;

ALTER TABLE `phppos_sales_items`
ADD COLUMN `measure_qty`  int(11) NULL AFTER `measure_id`;

