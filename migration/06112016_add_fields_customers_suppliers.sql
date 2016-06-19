CREATE TABLE IF NOT EXISTS `phppos_customer_type` (
  `customer_type_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `desc` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `status_agent` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`customer_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `phppos_customers`
ADD COLUMN `type_customer`  int(11) NULL;

ALTER TABLE `phppos_customers`
ADD COLUMN `sex`  enum('2','1') NULL DEFAULT '1';

ALTER TABLE `phppos_customers`
ADD COLUMN `family_info`  text CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL;

ALTER TABLE `phppos_customers`
ADD COLUMN `company_manage_name`  varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL;

ALTER TABLE `phppos_customers`
ADD COLUMN `company_birth_date`  date NULL;

ALTER TABLE `phppos_customers`
ADD COLUMN `position`  varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL;

ALTER TABLE `phppos_customers`
ADD COLUMN `code_tax`  varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL; 


ALTER TABLE `phppos_people`
ADD COLUMN `birth_date`  date NULL DEFAULT NULL;



