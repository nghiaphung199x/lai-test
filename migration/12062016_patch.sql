
ALTER TABLE `phppos_sales`
ADD COLUMN `deliverer`  int(11) NULL AFTER `deleted_taxes`,
ADD COLUMN `delivery_date`  datetime NULL AFTER `deliverer`;