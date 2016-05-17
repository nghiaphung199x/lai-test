-- phpMyAdmin SQL Dump
-- version 3.5.8.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 06, 2016 at 07:01 AM
-- Server version: 5.1.65-log
-- PHP Version: 5.3.29

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `phucsinh8_name`
--

-- --------------------------------------------------------

--
-- Table structure for table `phppos_additional_item_numbers`
--

CREATE TABLE IF NOT EXISTS `phppos_additional_item_numbers` (
  `item_id` int(11) NOT NULL AUTO_INCREMENT,
  `item_number` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`item_id`,`item_number`),
  UNIQUE KEY `item_number` (`item_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `phppos_app_config`
--

CREATE TABLE IF NOT EXISTS `phppos_app_config` (
  `key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `phppos_app_config`
--

INSERT INTO `phppos_app_config` (`key`, `value`) VALUES
('additional_payment_types', ''),
('always_show_item_grid', '0'),
('always_use_average_cost_method', '0'),
('announcement_special', 'Giảm 20% giá ngày 2-9'),
('auto_focus_on_item_after_sale_and_receiving', '0'),
('automatically_email_receipt', '0'),
('automatically_print_duplicate_receipt_for_cc_transactions', '0'),
('automatically_show_comments_on_receipt', '0'),
('averaging_method', 'moving_average'),
('barcode_price_include_tax', '0'),
('calculate_average_cost_price_from_receivings', '0'),
('calculate_profit_for_giftcard_when', ''),
('change_sale_date_for_new_sale', '0'),
('change_sale_date_when_completing_suspended_sale', '1'),
('change_sale_date_when_suspending', '1'),
('charge_tax_on_recv', '0'),
('commission_default_rate', '0'),
('commission_percent_type', 'selling_price'),
('company', 'LifeTek Co.,ltd'),
('company_logo', '1'),
('config_sales_receipt_pdf_size', 'a4'),
('currency_symbol', 'VNĐ'),
('customers_store_accounts', '1'),
('date_format', 'little_endian'),
('decimal_point', ','),
('default_new_items_to_service', '0'),
('default_payment_type', 'Tiền mặt'),
('default_sales_person', 'logged_in_employee'),
('default_tax_1_name', 'VAT'),
('default_tax_1_rate', '10'),
('default_tax_2_cumulative', '0'),
('default_tax_2_name', 'Thuế Môi Trường'),
('default_tax_2_rate', '2'),
('default_tax_3_name', ''),
('default_tax_3_rate', ''),
('default_tax_4_name', ''),
('default_tax_4_rate', ''),
('default_tax_5_name', ''),
('default_tax_5_rate', ''),
('default_tax_rate', '8'),
('default_type_for_grid', 'categories'),
('deleted_payment_types', ''),
('disable_confirmation_sale', '0'),
('disable_giftcard_detection', '0'),
('disable_margin_calculator', '0'),
('disable_quick_complete_sale', '0'),
('disable_quick_edit', '0'),
('disable_sale_notifications', '0'),
('disable_store_account_when_over_credit_limit', '0'),
('discount_percent_earned', '0'),
('do_not_allow_below_cost', '0'),
('do_not_allow_out_of_stock_items_to_be_sold', '0'),
('do_not_group_same_items', '0'),
('edit_item_price_if_zero_after_adding', '0'),
('enable_customer_loyalty_system', '0'),
('enable_sounds', '1'),
('fast_user_switching', '0'),
('group_all_taxes_on_receipt', '0'),
('hide_barcode_on_sales_and_recv_receipt', '0'),
('hide_customer_recent_sales', '0'),
('hide_dashboard_statistics', '0'),
('hide_layaways_sales_in_reports', '0'),
('hide_out_of_stock_grid', '0'),
('hide_points_on_receipt', '0'),
('hide_price_on_barcodes', '0'),
('hide_sales_to_discount_on_receipt', '0'),
('hide_signature', '0'),
('hide_store_account_balance_on_receipt', '0'),
('hide_store_account_payments_from_report_totals', '0'),
('hide_store_account_payments_in_reports', '0'),
('hide_suspended_recv_in_reports', '0'),
('hide_test_mode_home', '0'),
('highlight_low_inventory_items_in_items_module', '1'),
('id_to_show_on_barcode', 'id'),
('id_to_show_on_sale_interface', 'number'),
('keep_same_location_after_switching_employee', '0'),
('language', 'vietnam'),
('legacy_detailed_report_export', '0'),
('legacy_search_method', '0'),
('loyalty_option', 'simple'),
('mailing_labels_type', 'pdf'),
('mercury_activate_seen', '1'),
('number_of_decimals', '0'),
('number_of_items_in_grid', '14'),
('number_of_items_per_page', '20'),
('number_of_recent_sales', '10'),
('number_of_sales_for_discount', ''),
('override_receipt_title', 'Hóa Đơn Bán Hàng'),
('phppos_session_expiration', '0'),
('point_value', ''),
('prices_include_tax', '0'),
('print_after_receiving', '0'),
('print_after_sale', '0'),
('prompt_for_ccv_swipe', '0'),
('receipt_text_size', 'small'),
('redirect_to_sale_or_recv_screen_after_printing_receipt', '0'),
('remove_customer_contact_info_from_receipt', '0'),
('report_sort_order', 'desc'),
('require_customer_for_sale', '0'),
('require_customer_for_suspended_sale', '1'),
('require_employee_login_before_each_sale', '0'),
('return_policy', 'Đổi trả hàng miễn phí 1 năm'),
('round_cash_on_sales', '0'),
('round_tier_prices_to_2_decimals', '0'),
('sale_prefix', 'HĐBH'),
('select_sales_person_during_sale', '1'),
('show_clock_on_header', '1'),
('show_item_id_on_receipt', '0'),
('show_language_switcher', '0'),
('show_receipt_after_suspending_sale', '1'),
('speed_up_search_queries', '0'),
('spend_to_point_ratio', ''),
('spreadsheet_format', 'XLSX'),
('store_account_statement_message', ''),
('test_mode', '0'),
('thousands_separator', '.'),
('time_format', '12_hour'),
('timeclock', '0'),
('track_cash', '0'),
('version', '15.0'),
('website', 'http://lifetek.com.vn');

-- --------------------------------------------------------

--
-- Table structure for table `phppos_app_files`
--

CREATE TABLE IF NOT EXISTS `phppos_app_files` (
  `file_id` int(10) NOT NULL AUTO_INCREMENT,
  `file_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `file_data` longblob NOT NULL,
  PRIMARY KEY (`file_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `phppos_app_files`
--

INSERT INTO `phppos_app_files` (`file_id`, `file_name`, `file_data`) VALUES
(1, '4biz.png', 0x89504e470d0a1a0a0000000d494844520000009e0000005a080600000049fcefd90000200049444154789cedbd7b901cc97927f6fbb2ababababab1fd3d3f3c06030981d3c16af05f7fde2925c91224d8992ce924eb275d24994153ed966f81ce1f0c545ec862dfae45d8542771771b274b2ccd3899468eb444a22a53b1d498902c5e72db9b8c5627716c0029801e6ddd3d38feaeaea7a6665fa8f7e55f70cb8dc05b04b85e71731d13d559d59f9f8e597dff7e59759c03ef6b18f7dec631ffbd8c73ef6b18f7dec631ffbd8c73ef6b18f7dec631ffbd8c73ef6f1ff6bd03b5d0029cf414a3022f43f01b0ee5d3628a244f7bb9012208200a4000844ef177be7fd37008875be4b06502c7f894e3ed44b2b3a794b1091207aff5da9efa06ce77ae560521200c9a85338162b0f7ae501a490120048307677cbf676e01d255eb7f115008a945089a4daf924059d0e609dc606ba848194921311072497121e400111024082e803229eb7945088a00050637fb1bca5e8e4050e804b89800841ef19778b7c529e63dd41a50050a5941a111429a95b5e098084949d7275ead7ffe300eefac0b8db50dee9e7771bdd00c8005004c890121a11547409d2259de8fc5100c0911216803a112c2925bad701f4249d54884805a401504e4a148890ebe6ad75f2250ec0ebe4272d2232a5844544cee079771e528201a402c8013247440500b94eb9a4da95809c8802003600138029256c22e900c4a53cf7779a7cef18f18438a700d0882807c86922cc023443844929e59894a4a34bbc5e12001c9016801a409b805cee4ccfd4bdd7270a9392140006119500cc10c93980a689300e4007a4222505002c22ec10611dc03a9104d091a8b80bc4eb483b28dd015102680ec01c804900e352924604069007c09212db9db2c955222a77a4a0e444efb896745b78db89274447ef22822aa5cc019803e8c44e8b3df0d5d7534fadd5953955913a23a844920d2515c44301cf0dc86c7a547ff687eabf8d28e0be1f7a96e538839f1288a40a5001c09c7c7df93d78f1e5f793efcf43550da9281a018c8408c043477abe2941cbd10f3cf5397f662a0882d0b36dcfbb3b2d2019409a945404e4516c6e3f467ffea59f405a2bc864d24082a912044451402177e079a6288ebdc21f3afbef83e9496edb4e70e34639f8e427bf14bcf1b3be7ff1b6138f8898945205c820a2692971a2edd3137ffd7aeae9fff90bc6f14a40181812ddcf8e15c020a100520350d015ccff4fef0bef89bc76a55cae9b172f2e5900f8134f9c125114b14422a101284a29e771edc6e3f8f79f3f2b37d77560c85cd1a833f516e58119ddfbc0fbce374d7b797bbb617ef5ab176df4a5ec9d43d780d28850927e78142f5ef841f9479f3a3eb09dbaedd4e91b9d244ae2e90f08ffd891c59ad25caf541ae65ffdd579ebd39ffe6b86bba40abc1d606ffc933b879e3141041d9da9e5281778e8fc8afade7ff575fd78c5ef92ac9fa0fb49185cef76ce4fce075e14f893d56ab3b0bcbca57fe73b5734008ae785acd5721529a1032800388c8d8d59721c2d967c9015015249424c1f646d2363ecec98c5a5a54dfdd2a59b7d1df34eb601113100ba94b288283a8c5a6381d0319e0883bf7821a30393ccd6f55ca562e6ae5c59d35f7ae99a1a86fc8e97ededc4db56f00ee9a4222534808a809c9712ef5aae284ffeeb6fa58fbf584b0024bb248b11304e3e929d7b00ce1e0885e338b9adad7a6e6969c3b872655505a00641a8b8aeaf1041935216290867696bbd20ed16eb77682feb5ede9a0671f20cb36d37b7b55537ae5fdf309697cb1a0616f01d6a83bf8194bd364011513443ab1b855e9146ab0c0032a5c19f9ce4a6247d63632777f5eabab6bcbca54691e80d8cbf93785b0a3ef059914684023a7add29d3618fffd6b7f4b39fbd9e64a05873f71842d421dba8d7878093532e6f365adac6c68ebeb4b4a56f6e563500cad8589609215529a113a1202bd559344d837838e8508a7d4a00a994088e2ea056b3b4b5b58abeb4b4a9adaf5734007758ea51d785223500063cbf80575e66bda2f4ec85a101327f146eb6c82a15535d5dade8cbcb9b7aadd6d4a594efb447e2b670d70b2f44df41ac01c8019895521e0f237aecd3e7d30ffed6054d19d1e53a0987fcc6f1fb12990430aeb5bcca0d93adaded68ebeb3b6aa361ab0094b1314349a5922a005d4a14b0532fc2f5d55b169000a128b0274adef66a8dadafef686b6b3b5aa3d1527087251e00740c2662524aa052d550ddea0c8858d5fb438d003e35c91b4804ebeb3b6c65655bdddcac69ae1ba8a5524139756a4ef9c427fe1771f4e8c191670c6b2cf1eb40ef5e67d4111164f7c7714379e03f25f49cd78c7de00eb5c2db40bc6e43ab000c74f4ba05227ae8af2f698f3eff75bd30201d30443e4983d60762d78107c7395860793b1513e5728355ab4de6381e638cb18989024b2615950886941843a56ac06ef7ebd9ebe4beb4630c2237269a8a1a98664b69b55c158076cf3d33fae38f9f769e7cf234ffe99f7e9a1d393223e2c518a0e3ecedfed3f7fd4989bd561858771032482970f9ba31549eeef778feeda4ea6db63c6f7d7d47ecec988a10423d7d7a5ebffffe23c1473ef2049b9e1ee73df2740847dd76ef359b44dcf512ff7fd439dffd0f0044ec1a971282a8e33b4477e5e876574fee2af13aab07b2674c9400b920251e78f146eac9dff8ba3eb3ed017d06f42bba977f2a769f241e3f18c269d941bd6e897add1296d586ef73a4520a16160e288ac2ba44a7226e2e6ba8ee0cdbc9b1a956269310274ec38b0400a88582913d7b76a1542ae5bc93270feba74fcfdbb3b313ddc697b14eea745097741c185a5d0888c0a5fc1b010c96dfba3ec7ceaa8504643219d0238f0548280a00d617525d36867ec0cb8592b5da683bd5aa253817ecd0a149e3e4c9b9d2134f9c32eebb6f21d07575884ca3eebd517f5f9c74c3f77aadd3476f35a7eb608743040f9d9594dbb6f4ef1af1064b56a40128488939223ab3524d3cf9cfbfa12ffca7ed04933d0917976afd21dfbdded7fdbad724e148d1e54edb0e9a4ddbb3ac76d06e7b811042a4522a16160e2099543440e660b64ad8dad0d16e75b247ec31bd47249390f71e0363c4c6c6b2fab16389c9743a15cccc8c1b3333e3def878ce4b26139ce2d2b793874047327000819470d071465bdd5510bbeb040e8438d79520b2bb7c47aa24a9cac71face2f882498a52ece429fbe50b829057b66ad672c5aa6eadd72cd7f5bd4221c38e1c99293cf4d071e3f8f1432c9f3714c6983a9078c324dbfb5a57751ed29d473f21a4941c209b082640550015409a1d02d2f727f184e8afc16a000a809c057062c74abcfb77fe937ee24b37935a20869d569d1641d76aed5324f603f4b9389569794ebded5996ebb5db9ee338be0780e7f3193e3737a52412091da01c55aa25d976f5f82340c3ba149404686e16994c5a9b9d9d281211b259bd542864b8ae6b425593bd5582219f596c192f00e0104913a01d29e52611d6a5946580ea5d3daaebecedaca6002890e34dc1f14ac8191a18ebaf4977061710b53dd14aeb7653da6610704bd7535ea994571e393577e864123379d32c267dc740826912d4b7d8f730c586f03dae770844c24314993208ca329b7d0d93c54b514211be1f04afbebacc3efbd9ff4dfcd44ffdb3ef2db73d705788d759f281da5983c53440c7fd901efbec4be9b37f7239956b0d5655639af048d3edd54204dc978f60a0e56dd88ed36ebb8ee3785e18f200002f95f2627abad89bda73b2d6c8c5f5bb5eb6bda711015249802d1c6225d3ca4d7cf3ebbab278612ec19848241823c640c3face3024204182c64a1cf71cf1e4dc6c459e3a7e49940a17849057a4948c7351154270c34833009a94324744d3b878e961fad4ef9f80aaeaa00171bac5841672756cee98aa1f3812e8bae618465a1c3b76307b827b678a9ffcc3fb1352e85014158c75a7ef41da37225faf95477fdfff94524008011e0508034f3efeee59ffbff8a0d7cee52cd36cd9dffef6656f7dbd7a5bceeb3b4ebc9e93b8e3ce909352d251000ffced95d4c37ff86aaab46a13eb2bd440bf85924ce2b02ea126242e5989983131ac6d3f38c541a1e3b55aae67dbaee338beedba819748b0e0d4a9c32816b32a111952ca715a5bd7b1bdc37a8f1bf5a2801870e4242893865ead69895716195d7e6d5097d867cf85b867c7aa1af0d24b403a354387e6e6c50ffdf04cf8c4438aef73eefb41d06cb683b13143944a059d312a4a290fe1d2b53374f9b2b1270124a01098323e25d263596f4c5060181ae6e7a70bf9575e3d9158599926cfddd3da1e2a73bf4f303cae313cde6964acf7dba7e39242f4f813336dd074a5d2282c2d6d565f78e192f299cf7cf5b68228ee28f162a1481a208b52d23c11de75e166f2a9df3aafcdbdb49350b8dc63522060262bf08b677cfcd162aa7fad3ffdc694de85622842cf0d6cdb716cdb736cdb73841081a6a9e2f4e979964c2a1a111908c222b6370c59df19648741d6008004837ce001909480d9002d5d1ed2ffbedb3afc505e8107041ea80e55ee544ac4d8fdb23866ba53539669b6aceded861d4591c8e70d5d55951280792cdf280e95250622408e4f810ecf736d2cc78b9244b19855c6c7f3a56465a704210686c848dd46f3ec1953bb7c98b7986cfade2cd9756aa70d8493538a2590dfdcace62e5f5ed517176f2a52cad1c08c37853be6a31ae87552035024a239229c59af259efc172fa48f7e7925a90ef4badeb0eb1813c594c43f79d8c591e940bcdeeac5801280913f091cccb583c06b3bb6ed39edb6dbf2bc8e7ea7692a3f71620ea994aa4a0903a65582ddd64944fd32eea23c63c0c2618047a04a95c9c01fb678e3151ce94dc2707e3d6f10852163abeb45ba72fdb8ef07874dd32e552a0da356b334ce23434a590230838bdf51643ced487e989c004d1f60092581745a65b99caeeb7aaa44e7bfa952e0f77f3f322e7743c6548b58d977d913036fd5a02e04e0c83178c509a55a6fe92b2b15e3ead5556d6dad128f6b7c4bb823c493f25c4cafa39c947206c0093fa4473ef19df4892fbc9ed27ddeab5177ce020010520cf8c5933efeeb871db16327581827677c35a3db1af9a4e5056edb69b75dc7757dc7757d074090cf67c4dcdc94c218e9802cc8ed9d221a4d3dbe0c353aad8108727646c0f741cb6bbba6d1fea8c72071af53e2790e492d09a05655c4e67ac1f38269d36c97ea75bb60dbaec139cf0118c3f59579f85eff19bd7443cf29e44590cb0acf0b18e791c21853923bb502b90edbf5cc9e848ea7977b97359e70a8ae3452976e7a3139c12d4a88f5f51d75696953bf71a3acfb7ed80b597bcbb86de2757d7590128a94d200304344c7c3088ffcc1b7d30fff3f17b5523ddcc358202041123f7638c4c73f62093f022e6d2663bfa30e41636cf9c85c0843388165b5bd56cb715a2dd791129c88f8d1a307c5f4f498ca18d3018c51b566a05a6343231a23d35252859c9904fc00585d1d72b7f4a5429c7018d19b76b545774cf90e84636b8ee317eb75ab54ad368bb6ede682202a4889226eae1549ca61ddb3fbacde980c72d9c0d274cf346dde6e7b220838c76b57150421eb3fab9bbe375d8e12eb16f6d910c1fb65c730713b3f66b0733967bd1d784b4b9b627979936d6dd5d52812fd28ee3d1ef13de1b674bcee72183a91bebd9509b910707ae8ab57538fffbbc5d4ec921d2bdb8809f54829c23fffd1a64829024d2f89f39bc9e1df0e4947e0689103dcf52ccb756cdbb31dc7b35dd7f71425c1efbb6f01baae75562c842cc8b5559dd6d78694e93ebad38a78ef0f424a29846d832d2f331a7af63086f4a4d1fc467f539a063f30a7d8b6ab351ab66edbae11041c4244394016b1785995420cbc47f1f412809280974af31a1741bd6e09ce453035e502cb2b40180ea5eb49e55ba9a3a3d7e344ef4fbd233a2001904a02d13dc745b930655ddbac5b376e6cf1adad1a1a8d16a248f448f7ce181703a7286952a24844f33cc2bb1637938fffde4bdac2b9f52486348c98853a9d91f83f3e60f24ca219d8b6c2d69b9afa628d61a0e1c64daecebf53598f73cff16cdb75da6db7ed79a107204826157ef4e80cebaed1e6d06a97609a39784eac9cb192f43af8be53b09a6d2fdca838c5c02d5069022c91606034d8644400638c4922208a206b75f4a6c951f415754d4398d2d06a398a69da69d7f5f5eefd828c6409572e2b24447c157048d0e3e0617807e779a36107f57a2b08c3c8a9562dd777bc8a5a2814296be8482414494ce9ba3dd9402f231031965018406c30eaad1650af81a268d87d12235dff7a4a033f72afd878cf0f98af2432952b17ae5babab15a7523103c7f139206f7b5bc0ed5ab50ce8ac4c106156489cda68241efd938bda892f5e57955d2ddbfd3e9116f895c79afcb0be6196cb7e9036f2faebe5a4d61f8a432e944e13a90c984cdb5e64b59d76dbf5da6dcff3fdc00310e87a8a2f2ccc3045496852ca1c4cab48adb6010c4ba6f834490404b3337c73ab5e6f86896afee7fedbcd74268d7426ad25544507481542326250b4b4a6a6d229550d0325f1fffe31c30bdf00c270574c43af9aa290875f181396e5c0b2da0ae7919a4c2a8c3136466673129b2b0cbdb5d591b49df43904d92cb7acb6d768b41ccb721ae974cabcfc03effbf3e99ff989efa8aa321b45623a0ca36210047a180a454aa11011535585e9baa6160a869ec9682a0318766aa0bff822a3bff81c64cb1a181c234d2d01485d47f8e0a362f3c9f79a173c942f5e5caa5ebbb66e6e6c549b96d5b65dd7f7a44480dbdc93f2968927e5392625547436abcc0038d568b347fee3a5d4d9cfbe96329ae148cdbae2209f94f88513367f64e2467d6d75c754d5244432af5c2e2731443a89d83c4298cf44c8a0ed3976db6bb55cdb717cdbf3020f009f9c2c88b9b94995313200e4d0300ba835d478030f8100e8064c5d77b66e56eaebeb3b9b61c8abba8720e349958874ce232d08428d31a68f8d19c6dcdc54f1d0e143a5cc7bdeabd385f3906138a4b80fda05e086cedb46c6b336b68276db178c31359d4e6989042bd0b51b9388c46e032106aeebc2d6d2416b63db314ddbdede6e9844284f4ee6e1fbbcc218db68b5da072ccb29398e97f37dae015253d5a45a2864d4c9c9b1dcecec4431c9a8a86d5754fad3bf60f4b7e740b1a5c35d21580090cd2278ec49bef6e8bbcd0b7654be7871a97cf9f25a796d6da752ad36ab96e59851241c74d771df0a6f7a784bc48b45121b004d0238def6e9a1af5e531ffdbd97b4c9eb766c3b6c4c914833891fbdc7153f3c77b3be7173ad6adb8e3d3333ae06500a2f6c768b32d4220345e4cc4484b4f082ed96e3b4db9ee3babe1304dc638cf8d1a307512ae555a2ae61b1b56560e5e620f90808803c760ab520b23737ebe6b56b1be546c35e539484954eab8c8834cea3b4ef877a2a9534e6e6260b89049b191b339474ada626841844bbc811c9c11208349d3799e2d9b61bf87ec0f3f90c0c23ad2793891cdd583528e2c32b0d7116b204828c11586aca69b55cc734dbf6e666ddb42ca7aaaaaa53ab596a148972ad66559acd76c971fc1c20b3baae19a5525e5794446172929022e4944b57c0fec31719bdf802e0b407f5df351009726c1cee93ef0e561e79b779a1dc2c5fbcb8547efdf5d5f2ca4aa55cad9a5b96d5aebaae6f4a291d7476e6bdbd122fb617560764494a5ae0020fbcb2a13cfaaf5f4ccffde76aa2f7c36e0d7be69ac4fb6643fcf4bd9b9655be595d5d29d7354d0d5455cd35025d7da99688f502d0d7efbacb05474b8150e1069dd50acff1bcc09152048c317ef2e41c743da502d2a0202cc99ded1c55b64685ed90222d8f1f43a3d1762a15d35a59d9aeaeac6c6ffa7e5855558533468a1052e55c681313f95c3a9d9af2bc90479128e2eab51285e1ae76e95b8b992c82428937bdd0b36dd70b43ee67321acb66d39aa2240a78ed922283708870fd32490046167e619c37bdd069b51cafd5725a3b3ba6bdb2e259b59a654d4f17451445aa65b54d21504d261385f1f15ceef0e1746162223f75e8d0847ea898d1c6af5fcba97ff9972a5e7d058849e7a1ae012019414e4cc3fec10f7a378e9daa5f58ad965f7965b9f2faeb6be5d5d54ab95a35375a2d77d375838a10d204e000fda9f62de3ad48bcd8e23fcd4ae0cc5a3df1f8ef7e5b3ffa37abc9617dae5f53e081c9083f7f5fc3cb861be6d572d5725ddf2e16b330f239f65a3da30f3994faa0bee43b90f3455a093800ae288ca7d3295e2a1584aa26c5d9b347582a955489288766ab48969deb65b54b5dec7ef887e778d36a3ba6d9b277769acd9595ed6aad66551209e624120c524a964aa95a2e97ce150a46502c660b3ae782ad2c033cec556bd0813de615c7101c98e1ad96e3b4dbae27a50cf2f90c338c7426d1764a585d5628e2bb74abbe7a5b2c229c9e1196e578ad966bdbb6eb5896e3d4eb96b7bddd702e5e5c0a5435a9e4727a70f8f05470f060c93b73e61e71f2e461e3c89119fda0919a29bcfaca5cea0b5fc8d1f21203df9b7412e8c422ce1f45f3a9f738d78f9ca8bf7475637371f146e5ead5f5f2da5aa55cab595bb6ed967d3f2c0b21ab002c74a4dddb3bd50ed661a54144d3803cee06eca14fbea09ff9d32baa367010f57abcd39a0b5981ffe161070f4d9bc1fa0d2f4826153e3d5d64f3f3d36a61ac507c655155875a05b17c2031a14acce605f2a4b2b1b1ac323353523c2f540e1c282abaaeb163c70e2a8c31bd6358340b54a96bbdec7afead98ba0800684d943c6ba5e1742c64cfb66dd7f6bcc0426703750080e57219f5e4c9c3c1e9d3f3b983074b3c633634725a4a4f2a0f0d91eeb3a25c56b8a5126fad9b9ee3f84e22c178a19065ba9ed213e59d49b45b8a1403adb05fcb9e8b279f13eef878d05a6b38762710c20e82c09352060002c348070f3d744cdc7bef61e5f8f183fce8d183eadcdc64717272ec9e9c639f4d7ffd9b67125ff972816ede1ce891b1a9bcef30600471cfbda8fdc88fd85772a5ea855757caafbd76a37cedda7a7963a35ae9926e330c79554a54d1d9547e47a41df0a689d75912ebec7c97b342d27d7ff29fd38ffedb575286cd7b2d88580d810955e017dee5e123a73d91e48c39c5acaa28cc308cb476f060292735bdf41f6ea8436946c5d499718ef93162e309433f7468a210452297cf6774cf0bd5b13143999e1e675dc77101663387cd2da59f5dcc5cec53fae03c9ac994d3b29d6e589567872177d069581b9d518dfbef3faa3dfdf403da891373627c3ca724af5d37c0f990d374d42a0d5329d14aeb8e6d6f3abe1f7aaa9ae4c56256d1343547572a0678c4fabeb2b855dcfd0c532961a5d38e6d6f38edb6e7b4dbbe1786513ffae61ffda38fe0a9a7ceb2438726b55c2e5dd0756d56d792a79395eaa3892f7ef9143bf7e502aa95dd2e9a3808908f3d85ad77bfd7bca466aa172e2c955f7bed66656969a35c2ed7b71a8d56b9ddf6ca61c82b52a28e8ea4eb91ee8e6c747f53c4eb2c8b9126254a44347f7135f9e873df4c4f6eb86c30b446c8f3d8418e27e742b81e637694d3955c7a329711856452811529eaf5d59476b31567c5c81c04a0909670fc047332a562a2a0cea6a6269d13c5f51b3c708d6c36ad19461ae89c1a3006b36e60f5c67775aae2c449b402eeb55aae63db9da5b7ae4fb0ffb7b07000fff81fff38ce9e3d2a72395d51552547d7975472fd5dab18ddb6814c69080b25de8c8463dbae1704dc2b16b3a25030b4542a69d0da864641d85fe1d8a50aa4d208f2e3bcc9a5d36ab98ee3f86ddf0f9d28124126a3890f7ff811fcd22ffdb07ae0c0782e9954a689e83849f908ad6d3c894ffff1717af1db06ccfa6e8367a431e4877e18ab4f3c55bfd88eca17cf5fad5cbab452595ede2c6f6f37b69acd76d975fd7257d28d92ee8e1debf13d132f16dca913a124a5bce74f5f4a1fbd6626d8f0d08ab72af0e2b6827f722e03a52327ba7b4a3be1361280edb311675bacc5ba97bfbea960e94b59a88aa1ba4171a6edcbe2fff96474251399abc964a22aa544144506b5bd22ed548b70dbf195b25d048ce60e89961378dd089776bbed3b42080fb111fdf2cb9f403a9d628c914a440680022ebda2c6f3c668def902c2b9796ed99e67db9e1345919bcf6744a190d19249a54017cf2b70dabb0c8b1e64a180f0d03cb76cd7b16dd76bb75dc7f7034fd753fcc1078fe11ffc830fe8070f4ee8aaaacc10d10919458fd1956befc5ef7c621ed75ed7e00e3bcc47755b00e03ff153e2e6fd0fd72fecb4cb2f5fbc5ebe7c79b572f366b9bcbdddd8b22ca7ecba7e398a446f6aede974779474c09b205e2f08009d9d629300667fff8aaa0fb61fc608d75b6d20c2769bb0ed7497c2461741e35eccd8e5c130eda0ea11aa6ea2f75bf5c1b188e532a949b45981f328e7389ec864b442a2d52e52cdccc555cdd1a53210e01c980e5a8eefd8b6e7b5dbaee3795e4f61ee35aec866f5de2953ba9428d04679062d4b4577c5216ea8f4b3cf6410942678abe5388ee33944148c8d19cc30d219a5bc3389465d8588762ddef7078991413051e2ad96e3d9b66bf702200e1d9ac0873ef48876faf4bc9a4c266688e814847c8c16af3e2d7ff3b76670fdaa128fc219cab7f7259383f7f7ff3e5f3e76aa7a61ad567ef9e252e5d2a595f2ea6aa552a9981bb6ed943d2fa8748d08131d95a3e7b3bbe30718bd89a95632d93942cc9012a5d55a72a1e1c7969624c55a70e07feb93a847caf8441523681fa379c45dfadddf3e34150a5d9146c30f0dd7f573b6eda058cc15d0b40a58db5068289f58bedd6bed6221f0d62d0e2048a592c1ecec248e1d9b65f7de7b48f97b7fefddda073ff8b0d23d66a3488469227908e5ca3442aec4b31e6a1d004255453b93095ae56dcff3022f995482b1b12cd375cd60ab6bc5787a89f8d0ea205255d1ce1881bd55f6da6dcf09c3c81b1fcf8977bffb3ef5e9a7dfa54d4e16a689e804209f902fbcf47efab5e78ab02c06b19b134393502607ef1ffe43be74cff1eac59bdb9557176f54ae5fdf2c57abcd7218f29d4c26554da793d56432594fa5925632a9d89a96f45c37e0172f2eddd1233c7a7813c4eb1fb4a313517eb9a294a48c4527f47d6e72a4677a924d8e9029767daf89712f5276716c2244c403add16819aeebe71c276051242660b572726575e8c48021e55f02f28147c1b2596432019b9c2ca8f7ddb7a03ff040a270f4e8c1e0ecd905edde7b0f79998c0622d2014c03380ed0097973d540bbcd08c3d5885733482abc61188e6ddf743c2ff4342d29ba868541e58a063f188a48e949e69e2b265493dccc64ec76db738220f45435192c2ccc281ff8c003c5858503a56452390ee009f9675f7a1afff76f1b68b787ea3934507b7adea105885ffe25b173f090bdbeb46555ab4d2b0c233b9fcf38e9b4ea298ae2e5727a303666a050c8b24221c3f2f90c2b95f2509404969636f1d18ffe6ef13aa10000139249444154faf7c891ef1ddf33f13ab3a15488489352aadfb999d4a2deb0ed8998a169342601fb327f2fec25d680e1b08de114f38596d3324db1b5554b0b210dcf0b5419862534ea25da5add957d7ceac5d10518e3056d56500140e9c489b9c3f97c463d70607c666222ef1886c6d1394554077008c029b49dfbe95b5fcbc95a75b83d62d3ad2c8c213cf3a0302dc769b51c270cb9572ae579a190d5d5a492a3c5458d5aadc182fc68fa5c1ee13d2745b3e57a8ee37b8c317f7e7e4a79eaa9fb8af7df7fa490cf65ee65a6f5383effa587f1effe4083eb0d3551bcad09804c24204f9c81f86f7e4104a7ef157eb9ce9349854f4d156118bac218d3339954219f3790cf67d45c4ed73399b49e4eab6a3a9d62aaaa405515f1d453f7898f7ef4d7eff8e1406f4ac7eb928f4702fcdbeb0a13437e3b8cb85362f710bb36248246cd3a0cff76242c0a12d01212097fdbdcdaac78dbdb0d96cda675003a6bbb45dada29ecca634470cad238b40453e64ab9c9b1644240a2a0a592563aad062ae79cb5da202206828e484c238a66f1b5177258dd1872a3ec2a723a0d3e35cd2dcbe985e3bb8582817c3ea325c3b048e55555faeec051dc2b5e2f9fac81e89e79ee38bec77914e4f306e6e626730f3e786c72a2983ba2ec541fc5bff9f4297ce32b1a05fe770f8b22069c7d00f2e77f0eb8f7085421d8b896d494a97c61b6a0ab0418aaaa94526ad24ba592415249380946f584946526f80d52d2d7494988281281ef73fef18f7f547cfce39fdcdd47b78137e94e2101c0abb5c8f94e8d89ac2205230c9f7bd2871cf9dc7dadee63d88d327810008904003d29914c74ee0901bc67dc099ae56d6b63653ba8d72d18465a55d56496054181b677b4f853289e5df73abdba08322d964d30230b1c05d1c25e75950068a7c670e326e4fa0da06d7f17ff0c1025156167b39eb9d1746cbb6358140a1996c96859a5522d21e0fd633446dd1d3dfdd0cde703a7ea788c113f70a0983a73667eead054614e5fbef930fba3cf1ec5cbe7550afc21a7736f9a1e32a83259c0c882ce5f009dbfc000a0001885ceae3f0024e27e4d4411972177e0fb96cce72f85ef7fcf9f7b93138eebfaceb56b1bdef9f3afdfbae26f116f867802901e00cb7268ebbf3cecbfa23231934e8a5c82a406291529053a7adf6e22120109c698aa26144549301e117ee5db69f0ce810f31c363d0237346841f9a0f71d00885e787bc667a8111ee98eb3756ea2b37cb96e7853c9d56954c2665241cc790d7aef70fc0893fb7ff1d00bef9d55121cbf612babbb4cebdec9f1e711803cfe645554bdb8dc6badd6ebb8ea22482f1f11cd3f594c1969772e4b8bbda7a101645885229d1d2752f08ac209d4e616a6acc5898c8970a972f3fa87cfecf17e8f22555467cd7843154a0dea7dd047de32b7bba92ba0465bddf77eba012a04aa0204f9de6fe938f1d6e345a3737376bf56f7deb35fb85172ef5adfdd13abc55bc49e25120a5accfe4ddc55ff960e3538e131cf1fd60d6f3fc69cf0b0ddf0fb4308c14218422e530f9128904cb66756d6aaa60944a855cc5d6947ff69d34e3bdda63b718787886e3bf7fc2e48544d55e5e2ed797accdfacdadedfad2ca56756bab5e2f148c563e6f20a36b7a62bb96a3b51bfde7f5f787638f69312601e38103bb3a295624c4f2e9eb67bd047a06dec9b3c156cbb36a35cb6eb73d6762a2c0c7c6b27a2aa51ab4baaac26e0da51d32a954157c72463499e2711ef16c36cd0e1e2c1526d6d74ea89ffbec3c96aeab432b2f18fede977423e51e19c7fdaac7db6468902655449353cc1e2fea95f56ae1faf50de3d557974ddb76efc832591c6f9278f088c8741c8fedec3445b56a5ab55a6babd1681d68b59c82e3f84610846a8778c38953a9a4323939a61f3932534a2695d9d72bd952876fbd561be95d02c6742e22d7745e5b59a9bcf6dacdf2b56bebd572b95eaf56adbaebfa3b5353634ea99433f404e989aee3b8873de3cdb0c7b55b4c9f439d13231cd180a89ddf11a25c41548f1cb53737ebd6ce4ed3f2bca09dcdea229fcfa82a640137963458d620af51c3229d46b47054f44e44c86675562a6673e9f3df98649b9bda681977116ca8d0b1813452fed1b619ad2f0a45f085e38a6539b9cdcdaa71edda867eeddabaea7981827756e28103d22997ebb87a753d585dad385b5bb57aad66555b2d27e7ba81c179a44a2976e59bcdeac963c7660bf97c66a6d572d40b6b4a5148b0be48e9fb283a5d9a518031c50dacda76fdca95d5f22baf2caf77e2e6ac46bbed55b359dd1a1bcb627c3cafa584d068bdac0f4d43189158dd1b4301d1b885d41be9d0a141342afd522aac773de85d154a7d6dad52dfd931ad20089d5c4e1786915694a66590d55411f1dd7658efff540afecc016edb8ec77914a4d306cb0239c53275e93a837149bb8ad12dccc8b5d873803dd2c6ea37641f66b3c29f9c44ec2c3ead5cae6b183892ef18de8455fb7e48798e0310e7ce5d1057aeac06d7aead7bb55acb6eb73dd3f70383f3481342ee3aa99288303333ae4d4f178b52ca404a14be7833793c8ab7de882570508f5082e56d6d6cd76fde2c576edcd85abf71636bc3b69d2ae7513d9bd5bde9e931bd50308a491e72ac6db038d97a59dd2a2c2a4eb8f8f5f8972109b7c75c2c1309d88f3f155c3bfd40f5cad2666565a55caf54cc5a3aadda998c2652a9a460eb5b0a6c87c5f31d2d172712b5a46637eb961304a1c7180bd47a43652d5b8d977dd7c0ba9524bb856a11afd350fdbb08532aaf6bbab3b151c5cacab6bab151d56cdbededa1bda3789356edfb81c141353dd1db7b578489c10b4c86904824d8f47451cbe70d279fcfa8569099bf505784dcd5938396184b055c0b4d7b73b36a562acd7abddeda6936ed4dc7f1ab00ea85822166672773b99c6eb1eab643975e5546c9f1ddc2a27a1d38d4111848872189b207e9c43dc7d03c76ccb97afae1faf9f57af9ca95d5eaea6aa5de68b4cc54aa686b5a128a927068e9a640d3ea6f49ec93be973563f08d42b0ea4566ad6659be1fda9c47015b290bdaa929430b3d83af83b26250af516b3e2ec1e3a4dfd36d0ac015325871b9b5babacd3737aba8564de6bac15d3935f676981c271d47ec8d39bb1ea23076f0e0b876f8f0942895f25add4b574e65235b42325581c26267c30929e18752cc271b8edfd8a89737aa66a3d1325dd7af4791a8039dd8b0d9d90971f06049e87aaa4e515496e98c2d8f9ed4484928201a3ae201d8dbb9337a6faffb1200f239c8ac01212482743a68ab6a501b3fe0ada48cfae28d9deaebafaf56ae5fdf2c6f6dd5b6db6db73e39396625120910515d369b36c64a9ecc160025a1cad81c2784103c8a447d72ce59daa855b7b66a1600abd51a73c2b65317b9a24d47550645517a07f3ec2a7f77e4ecaac35e0368b4623d2d47080451c42be307ed2bab3bd6cd9bdb5eb9dce096e570cea35be5705bb813c4eb49c05b6ef0cd64349c39738f76fcf821512ae599c6a3173ff6704d4925c45c92f14992512eea6cae618ee38bbae904ed7ac5da5ebfbeb9b2b25da956cd9ae7f9a694b21731619d3c79183333e350d564994f4fbf16fef44f7d866bda2c6789492e91e342aa3c8ad4484826a57c73a356f63a4f0a21054452153ca18820e0c21632680491b3d568dbabafdf345756b6cdb5b59deaf676278e2d0878350cb929a5144248113c70f62bc1917baa5c49ce72c64a5cc2e091503d3f646ddbe166bd15ac599e79f5ea5aa55c6e5493c9c44eb198b336ef99f6931f9a32d554725a2a4a279d103a8f84f296eab4573525c03917aeed058da6edacb4fc7a47726f5bb59ae5b4db5ec079f44eafd5ee89b8a573cb86300c1d3ffee3ef110b0b07904a2591f102f194b263b75aee9c6d3b876cdb9d6cb7fd1cb71dd56db6995b6ff16aa5616f6ed6aa5b5bb5ad46c3ae7a5e680a217b1113dee9d387313b3b0155552aad745ab1def52e6edbeeac6dbb076ddb29b55d2fe779420fc3481342bca94e9212ddd311a410428828f2441084c2f342de6ebbbcd96c7bb59ae5542aa65da99866a3d1aa359bed8aeb7a6580aa9c733b0c39e73cf2aca30bdc71fc4aa76cdec176db29d9ae67d836574d3340ade607dbdba6b5b959ab369bf6562aa5567239bdbe343b5196876636555529795e78c0b6dd92e37b39cf8bf4300c55216e9f7842088461c46ddbe78d86e794cb756b63a35a2d971bdba6d9aa77f6b5c83b16fc19c79d541a6f59b0d5d56d3cfae8090ec0b16d57589683cdcd6a502e37ac4ac5acd66acd52abe58eb55a8ede6e7b4aabe508cb6a7ba6d96e9aa65d6db5da15db76cd288afab161f7df7f5464325a1086dcaa562d6c6dd582eded467d67a759a9d7adc956cb19735d5f0fc38e95bdd79113b74287783df209c17924828073df0f85eb0641bbedf276db75adcebaace5babee9fbbcce39af27938ae9fba1e37941e0babee3385eb0bddd70aad566bd52312b8d46ab64594ed6713ca3d57259abe5f066d3769a4da7e1797e555593554d53ebe3e3391e866199880aa6d92e371aad52abe58c795ea08721d7ee0cf124a228129e17f056cb0d9acdb6d368d80dcb6a57db6dd7e43cea6d03f8be26de1b400a29896f6f379cd75f5f13afbeba1cacad559c72b9619aa65d741c2fe779811604a11a041cbe1f72d7f51ddf0f2dcf0bcc20e0bd48580e407cf18bdf111ffbd88f07be1f8ad75ebbc12f5f5e0dd6d676ec4aa561369bed4abbede5c290eb420875288ae64d4008812812420809ce231186210f022e828073cff38328925e1070474a694b296d0056184676ad66059b9b35bebabaedd5eb2defead5756763a36a371aad7ab3d92e7a5e90f33c5f0bc348f1bc40b86e10f87e684b292cc698c9796486210f56572b4c08596d345a55cb720a9e17e48220d4efd43b2eba01b4e05c8820e0c2757dcff302a7fb7c330c796f1bc01d9f6e6fe13ebd3b90f21c16176fe0677ee657d9d6565df5fd50e33cd285103921a486ce89994ae7b7527476accb404ad8180426f60eb88694e7f09bbff967f897fff2b34aabe528ddfc0c29a5319adfed971de80c9efe7b6d7b3bad7ae1f241af7c8904e3939305313151c0f67683b96ea072ceb528128610d29052f6de53a174256beff06e0f80cd18398a92083abbdda00821f46eba3b5aa7d1fa75ca217b6fc78cef3fe949bd3b86b7957831f40c915e946fef2d3af133d77afa63afd243ef6bdd23cf5efa5be5772730fa5a811ef9e2e51a5dd7ecd533fededcbdea1acf2bded1df2ddd9d46bccde3e5b8e353ed3b453c6040be5ec7ec6515c73bf18d42b0f7ca0fb87bc41b2ddfe8bd78b97a9fa3f5642369f6aae7ddae531c7b0dacbbf2dede7792783dbc5163deaa43df6a7e7702e216dfbf1bd82dbe7f2f79be51da3b89b752b77dece3ef06be1f24def7279e5d2c013805a00ac8eb78eebedb7f31f133af2a207a2f0040e26b78fe0c1fb9af01380ea2022416f1fc99fa6d3ff38df0eca20a8913201421f1cadbf24cbc83af86ff3b803390f2d740f435807e1dc0ed7708910689df068183f01876598a6480f06100a7017c0acf2c721066d1312cce0158c77367f69efe9e599c06e1070128c391117d2c43caf378fe3e67e4ba01c22f03f22910fd329e5dbc8aceabea2d009b78eecc5d7953f93ef16e8541e73d0ce0bfc3b38b4eecde3a08e700e290f2e7005ac4f367bed6bfffece23480a7d059577e6157e7f5175347d0d9bbcc216519446721f1b390f24c97b03f0992253cbbf8283a567b0f0180ab002a00ee03a0826816c093e8046ebc00c080941c8427f1eca2d75da715001c485c8d6d042981e483907808a0cf75f3bc2bd827dead3090182a803c7a6d25651ac0cf42621e909f01e13700fc5f00be164b3d0fe07f04f032804574cf62e9604f69d4430ec06300729098056106129f017013040649ff149006885e46475a3200f390f26740f4bfe3b933ff1400f0cce253204c03b80ec85f85a41f03f0a3004d40c2ee86c830103e0cc2268075481440f8af203107a2bf05f0329e3b63bfd5e67b23ec13ef5618c48f7f0ba05fc7735ddde7d94515c08f00f8c90e296eb116b7572c3d30b8f8ddc2463a81850c1255103e85e7ce9cc333affe3c80a740f42b90f8033c7f26c0b38b0c1dc9fa39403e0ce0cb7b144205e13d905400f0bb78feccd5413de41c247d1824ff0444d3e848c9cf03f8049e3bb3fec68df4d6b14fbc5be1565b2f9f3b13e0994580a00d4565ee4a7fab8c7bd1d6b7883f8f07cc0d6d88a05e5ffd2c08efc1338b021d8957ec4e95ea504683e0c3de4b6f6601fa353cb3687583f418408fc60adb79c11f9140e7d4cfbb8a7de2dd1a013a6f5d9c015040cfb87866b177886477098f024859dc957a2f424ae9015405e4e49e122f1e021d3f93a683def1af1c527e0104af1f5047f43b90581ecea77f2f00c806a402c845105d8494a24bcacf81a042caf7015807701e441f02680bcf2cfe5b3c7fc67c532df626b04fbc5b6311c0efa1a3cffd219e5decb953140036a4fc17006c803e0dd0fbf1ece257fa29a5cc75f7ebbe3c94e3f3f7713cbbf81b007d0cc05fe2d9c5f85d814ee70f302c71bf0cc8cf43e2c320fa18800012ff2b9ebfef5bbb4ade8f3026748c0afa5310953a69f13488ae03f297f1dc7d1ccf2e1641f43e486982e88f013c02c22f0230f0cce2bfc1f36736df52ebbd01f689772b3c77c6c2338b7f06c2b7d0790d69ef256be84810ac831000f85548f93b006621e96320fc58370efd3c487e01a051a9f157005e81948501a924202140d45b9067e86cbad6d023e373672a7876f15741f85d74fa4d807073cfb213bd0c895f042100a10cc8ff08d0791072dd7a7840ff65c716207f0d201d909b009d8394bf0f900d4275cffcef00f61dc8770acf2e2ae84cc99dddfa121e08753c77e6f61dcffbd8c73ef6b18f7dec631ffbd8c73ef6b18f7dec631ffbd8c73ef6b18f7dec631ffbd8c73ef6b18f7dec631ffbd8c73ef6b18f7dec631f7be0ff03d5c93107bcbba3f00000000049454e44ae426082);

-- --------------------------------------------------------

--
-- Table structure for table `phppos_categories`
--

CREATE TABLE IF NOT EXISTS `phppos_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `deleted` int(1) NOT NULL DEFAULT '0',
  `hide_from_grid` int(1) NOT NULL DEFAULT '0',
  `parent_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `deleted` (`deleted`),
  KEY `phppos_categories_ibfk_1` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `phppos_customers`
--

CREATE TABLE IF NOT EXISTS `phppos_customers` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `person_id` int(10) NOT NULL,
  `account_number` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `override_default_tax` int(1) NOT NULL DEFAULT '0',
  `company_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `balance` decimal(23,10) NOT NULL DEFAULT '0.0000000000',
  `credit_limit` decimal(23,10) DEFAULT NULL,
  `points` decimal(23,10) NOT NULL DEFAULT '0.0000000000',
  `current_spend_for_points` decimal(23,10) NOT NULL DEFAULT '0.0000000000',
  `current_sales_for_discount` int(10) NOT NULL DEFAULT '0',
  `taxable` int(1) NOT NULL DEFAULT '1',
  `tax_certificate` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `cc_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cc_preview` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `card_issuer` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
  `tier_id` int(10) DEFAULT NULL,
  `deleted` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `account_number` (`account_number`),
  KEY `person_id` (`person_id`),
  KEY `deleted` (`deleted`),
  KEY `cc_token` (`cc_token`),
  KEY `phppos_customers_ibfk_2` (`tier_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `phppos_customers_taxes`
--

CREATE TABLE IF NOT EXISTS `phppos_customers_taxes` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `customer_id` int(10) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `percent` decimal(15,3) NOT NULL,
  `cumulative` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_tax` (`customer_id`,`name`,`percent`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `phppos_employees`
--

CREATE TABLE IF NOT EXISTS `phppos_employees` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `force_password_change` int(1) NOT NULL DEFAULT '0',
  `person_id` int(10) NOT NULL,
  `language` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `commission_percent` decimal(23,10) DEFAULT '0.0000000000',
  `commission_percent_type` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
  `hourly_pay_rate` decimal(23,10) NOT NULL DEFAULT '0.0000000000',
  `inactive` int(1) NOT NULL DEFAULT '0',
  `reason_inactive` text COLLATE utf8_unicode_ci,
  `hire_date` date DEFAULT NULL,
  `employee_number` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `termination_date` date DEFAULT NULL,
  `deleted` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `employee_number` (`employee_number`),
  KEY `person_id` (`person_id`),
  KEY `deleted` (`deleted`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `phppos_employees`
--

INSERT INTO `phppos_employees` (`id`, `username`, `password`, `force_password_change`, `person_id`, `language`, `commission_percent`, `commission_percent_type`, `hourly_pay_rate`, `inactive`, `reason_inactive`, `hire_date`, `employee_number`, `birthday`, `termination_date`, `deleted`) VALUES
(1, 'admin', '25d55ad283aa400af464c76d713c07ad', 0, 1, 'vietnam', '0.0000000000', 'selling_price', '0.0000000000', 0, NULL, NULL, NULL, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `phppos_employees_locations`
--

CREATE TABLE IF NOT EXISTS `phppos_employees_locations` (
  `employee_id` int(10) NOT NULL,
  `location_id` int(10) NOT NULL,
  PRIMARY KEY (`employee_id`,`location_id`),
  KEY `phppos_employees_locations_ibfk_2` (`location_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `phppos_employees_locations`
--

INSERT INTO `phppos_employees_locations` (`employee_id`, `location_id`) VALUES
(1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `phppos_employees_reset_password`
--

CREATE TABLE IF NOT EXISTS `phppos_employees_reset_password` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `employee_id` int(11) NOT NULL,
  `expire` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `phppos_employees_reset_password_ibfk_1` (`employee_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `phppos_employees_time_clock`
--

CREATE TABLE IF NOT EXISTS `phppos_employees_time_clock` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `location_id` int(11) NOT NULL,
  `clock_in` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `clock_out` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `clock_in_comment` text COLLATE utf8_unicode_ci NOT NULL,
  `clock_out_comment` text COLLATE utf8_unicode_ci NOT NULL,
  `hourly_pay_rate` decimal(23,10) NOT NULL DEFAULT '0.0000000000',
  PRIMARY KEY (`id`),
  KEY `phppos_employees_time_clock_ibfk_1` (`employee_id`),
  KEY `phppos_employees_time_clock_ibfk_2` (`location_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `phppos_expenses`
--

CREATE TABLE IF NOT EXISTS `phppos_expenses` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `location_id` int(10) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `expense_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `expense_description` text COLLATE utf8_unicode_ci,
  `expense_reason` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `expense_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `expense_amount` decimal(23,10) NOT NULL,
  `expense_tax` decimal(23,10) NOT NULL,
  `expense_note` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `employee_id` int(10) NOT NULL,
  `approved_employee_id` int(10) DEFAULT NULL,
  `deleted` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `location_id` (`location_id`),
  KEY `employee_id` (`employee_id`),
  KEY `approved_employee_id` (`approved_employee_id`),
  KEY `category_id` (`category_id`),
  KEY `deleted` (`deleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `phppos_giftcards`
--

CREATE TABLE IF NOT EXISTS `phppos_giftcards` (
  `giftcard_id` int(11) NOT NULL AUTO_INCREMENT,
  `giftcard_number` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `value` decimal(23,10) NOT NULL,
  `customer_id` int(10) DEFAULT NULL,
  `inactive` int(1) NOT NULL DEFAULT '0',
  `deleted` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`giftcard_id`),
  UNIQUE KEY `giftcard_number` (`giftcard_number`),
  KEY `deleted` (`deleted`),
  KEY `phppos_giftcards_ibfk_1` (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `phppos_giftcards_log`
--

CREATE TABLE IF NOT EXISTS `phppos_giftcards_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `log_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `giftcard_id` int(11) NOT NULL,
  `transaction_amount` decimal(23,10) NOT NULL,
  `log_message` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `phppos_giftcards_log_ibfk_1` (`giftcard_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `phppos_inventory`
--

CREATE TABLE IF NOT EXISTS `phppos_inventory` (
  `trans_id` int(11) NOT NULL AUTO_INCREMENT,
  `trans_items` int(11) NOT NULL DEFAULT '0',
  `trans_user` int(11) NOT NULL DEFAULT '0',
  `trans_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `trans_comment` text COLLATE utf8_unicode_ci NOT NULL,
  `trans_inventory` decimal(23,10) NOT NULL DEFAULT '0.0000000000',
  `location_id` int(11) NOT NULL,
  PRIMARY KEY (`trans_id`),
  KEY `phppos_inventory_ibfk_1` (`trans_items`),
  KEY `phppos_inventory_ibfk_2` (`trans_user`),
  KEY `location_id` (`location_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `phppos_inventory_counts`
--

CREATE TABLE IF NOT EXISTS `phppos_inventory_counts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `count_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `employee_id` int(11) NOT NULL,
  `location_id` int(11) NOT NULL,
  `status` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `comment` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `phppos_inventory_counts_ibfk_1` (`employee_id`),
  KEY `phppos_inventory_counts_ibfk_2` (`location_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `phppos_inventory_counts_items`
--

CREATE TABLE IF NOT EXISTS `phppos_inventory_counts_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `inventory_counts_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `count` decimal(23,10) DEFAULT '0.0000000000',
  `actual_quantity` decimal(23,10) DEFAULT '0.0000000000',
  `comment` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `phppos_inventory_counts_items_ibfk_1` (`inventory_counts_id`),
  KEY `phppos_inventory_counts_items_ibfk_2` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `phppos_items`
--

CREATE TABLE IF NOT EXISTS `phppos_items` (
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `item_number` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `product_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `size` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `tax_included` int(1) NOT NULL DEFAULT '0',
  `cost_price` decimal(23,10) NOT NULL,
  `unit_price` decimal(23,10) NOT NULL,
  `promo_price` decimal(23,10) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `reorder_level` decimal(23,10) DEFAULT NULL,
  `expire_days` int(10) DEFAULT NULL,
  `item_id` int(10) NOT NULL AUTO_INCREMENT,
  `allow_alt_description` tinyint(1) NOT NULL,
  `is_serialized` tinyint(1) NOT NULL,
  `image_id` int(10) DEFAULT NULL,
  `override_default_tax` int(1) NOT NULL DEFAULT '0',
  `is_service` int(1) NOT NULL DEFAULT '0',
  `commission_percent` decimal(23,10) DEFAULT '0.0000000000',
  `commission_percent_type` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
  `commission_fixed` decimal(23,10) DEFAULT '0.0000000000',
  `change_cost_price` int(1) NOT NULL DEFAULT '0',
  `deleted` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`item_id`),
  UNIQUE KEY `item_number` (`item_number`),
  UNIQUE KEY `product_id` (`product_id`),
  KEY `phppos_items_ibfk_1` (`supplier_id`),
  KEY `name` (`name`),
  KEY `deleted` (`deleted`),
  KEY `phppos_items_ibfk_2` (`image_id`),
  KEY `phppos_items_ibfk_3` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `phppos_items_tags`
--

CREATE TABLE IF NOT EXISTS `phppos_items_tags` (
  `item_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL,
  PRIMARY KEY (`item_id`,`tag_id`),
  KEY `phppos_items_tags_ibfk_2` (`tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `phppos_items_taxes`
--

CREATE TABLE IF NOT EXISTS `phppos_items_taxes` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `item_id` int(10) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `percent` decimal(15,3) NOT NULL,
  `cumulative` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_tax` (`item_id`,`name`,`percent`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `phppos_items_tier_prices`
--

CREATE TABLE IF NOT EXISTS `phppos_items_tier_prices` (
  `tier_id` int(10) NOT NULL,
  `item_id` int(10) NOT NULL,
  `unit_price` decimal(23,10) DEFAULT '0.0000000000',
  `percent_off` decimal(15,3) DEFAULT NULL,
  PRIMARY KEY (`tier_id`,`item_id`),
  KEY `phppos_items_tier_prices_ibfk_2` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `phppos_item_kits`
--

CREATE TABLE IF NOT EXISTS `phppos_item_kits` (
  `item_kit_id` int(11) NOT NULL AUTO_INCREMENT,
  `item_kit_number` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `product_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `tax_included` int(1) NOT NULL DEFAULT '0',
  `unit_price` decimal(23,10) DEFAULT NULL,
  `cost_price` decimal(23,10) DEFAULT NULL,
  `override_default_tax` int(1) NOT NULL DEFAULT '0',
  `commission_percent` decimal(23,10) DEFAULT '0.0000000000',
  `commission_percent_type` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
  `commission_fixed` decimal(23,10) DEFAULT '0.0000000000',
  `change_cost_price` int(1) NOT NULL DEFAULT '0',
  `deleted` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`item_kit_id`),
  UNIQUE KEY `item_kit_number` (`item_kit_number`),
  UNIQUE KEY `product_id` (`product_id`),
  KEY `name` (`name`),
  KEY `deleted` (`deleted`),
  KEY `phppos_item_kits_ibfk_1` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `phppos_item_kits_tags`
--

CREATE TABLE IF NOT EXISTS `phppos_item_kits_tags` (
  `item_kit_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL,
  PRIMARY KEY (`item_kit_id`,`tag_id`),
  KEY `phppos_item_kits_tags_ibfk_2` (`tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `phppos_item_kits_taxes`
--

CREATE TABLE IF NOT EXISTS `phppos_item_kits_taxes` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `item_kit_id` int(10) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `percent` decimal(15,3) NOT NULL,
  `cumulative` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_tax` (`item_kit_id`,`name`,`percent`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `phppos_item_kits_tier_prices`
--

CREATE TABLE IF NOT EXISTS `phppos_item_kits_tier_prices` (
  `tier_id` int(10) NOT NULL,
  `item_kit_id` int(10) NOT NULL,
  `unit_price` decimal(23,10) DEFAULT '0.0000000000',
  `percent_off` decimal(15,3) DEFAULT NULL,
  PRIMARY KEY (`tier_id`,`item_kit_id`),
  KEY `phppos_item_kits_tier_prices_ibfk_2` (`item_kit_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `phppos_item_kit_items`
--

CREATE TABLE IF NOT EXISTS `phppos_item_kit_items` (
  `item_kit_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `quantity` decimal(23,10) NOT NULL,
  PRIMARY KEY (`item_kit_id`,`item_id`,`quantity`),
  KEY `phppos_item_kit_items_ibfk_2` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `phppos_locations`
--

CREATE TABLE IF NOT EXISTS `phppos_locations` (
  `location_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text COLLATE utf8_unicode_ci,
  `address` text COLLATE utf8_unicode_ci,
  `phone` text COLLATE utf8_unicode_ci,
  `fax` text COLLATE utf8_unicode_ci,
  `email` text COLLATE utf8_unicode_ci,
  `color` text COLLATE utf8_unicode_ci,
  `return_policy` text COLLATE utf8_unicode_ci,
  `receive_stock_alert` text COLLATE utf8_unicode_ci,
  `stock_alert_email` text COLLATE utf8_unicode_ci,
  `timezone` text COLLATE utf8_unicode_ci,
  `mailchimp_api_key` text COLLATE utf8_unicode_ci,
  `enable_credit_card_processing` text COLLATE utf8_unicode_ci,
  `credit_card_processor` text COLLATE utf8_unicode_ci,
  `hosted_checkout_merchant_id` text COLLATE utf8_unicode_ci,
  `hosted_checkout_merchant_password` text COLLATE utf8_unicode_ci,
  `emv_merchant_id` text COLLATE utf8_unicode_ci,
  `listener_port` text COLLATE utf8_unicode_ci,
  `com_port` text COLLATE utf8_unicode_ci,
  `stripe_public` text COLLATE utf8_unicode_ci,
  `stripe_private` text COLLATE utf8_unicode_ci,
  `stripe_currency_code` text COLLATE utf8_unicode_ci,
  `braintree_merchant_id` text COLLATE utf8_unicode_ci,
  `braintree_public_key` text COLLATE utf8_unicode_ci,
  `braintree_private_key` text COLLATE utf8_unicode_ci,
  `default_tax_1_rate` text COLLATE utf8_unicode_ci,
  `default_tax_1_name` text COLLATE utf8_unicode_ci,
  `default_tax_2_rate` text COLLATE utf8_unicode_ci,
  `default_tax_2_name` text COLLATE utf8_unicode_ci,
  `default_tax_2_cumulative` text COLLATE utf8_unicode_ci,
  `default_tax_3_rate` text COLLATE utf8_unicode_ci,
  `default_tax_3_name` text COLLATE utf8_unicode_ci,
  `default_tax_4_rate` text COLLATE utf8_unicode_ci,
  `default_tax_4_name` text COLLATE utf8_unicode_ci,
  `default_tax_5_rate` text COLLATE utf8_unicode_ci,
  `default_tax_5_name` text COLLATE utf8_unicode_ci,
  `deleted` int(1) DEFAULT '0',
  PRIMARY KEY (`location_id`),
  KEY `deleted` (`deleted`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `phppos_locations`
--

INSERT INTO `phppos_locations` (`location_id`, `name`, `address`, `phone`, `fax`, `email`, `color`, `return_policy`, `receive_stock_alert`, `stock_alert_email`, `timezone`, `mailchimp_api_key`, `enable_credit_card_processing`, `credit_card_processor`, `hosted_checkout_merchant_id`, `hosted_checkout_merchant_password`, `emv_merchant_id`, `listener_port`, `com_port`, `stripe_public`, `stripe_private`, `stripe_currency_code`, `braintree_merchant_id`, `braintree_public_key`, `braintree_private_key`, `default_tax_1_rate`, `default_tax_1_name`, `default_tax_2_rate`, `default_tax_2_name`, `default_tax_2_cumulative`, `default_tax_3_rate`, `default_tax_3_name`, `default_tax_4_rate`, `default_tax_4_name`, `default_tax_5_rate`, `default_tax_5_name`, `deleted`) VALUES
(1, 'Truongwts', 'Số 8/8 Ngõ 379 Đội cấn', '0904143388', '', 'no-reply@4biz.vn', NULL, NULL, '0', '', 'America/New_York', '', '0', NULL, '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Thuế', NULL, 'Thuế 2', '0', NULL, '', NULL, '', NULL, '', 0);

-- --------------------------------------------------------

--
-- Table structure for table `phppos_location_items`
--

CREATE TABLE IF NOT EXISTS `phppos_location_items` (
  `location_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `location` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `cost_price` decimal(23,10) DEFAULT NULL,
  `unit_price` decimal(23,10) DEFAULT NULL,
  `promo_price` decimal(23,10) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `quantity` decimal(23,10) DEFAULT '0.0000000000',
  `reorder_level` decimal(23,10) DEFAULT NULL,
  `override_default_tax` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`location_id`,`item_id`),
  KEY `phppos_location_items_ibfk_2` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `phppos_location_items_taxes`
--

CREATE TABLE IF NOT EXISTS `phppos_location_items_taxes` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `location_id` int(11) NOT NULL,
  `item_id` int(10) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `percent` decimal(16,3) NOT NULL,
  `cumulative` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_tax` (`location_id`,`item_id`,`name`,`percent`),
  KEY `phppos_location_items_taxes_ibfk_2` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `phppos_location_items_tier_prices`
--

CREATE TABLE IF NOT EXISTS `phppos_location_items_tier_prices` (
  `tier_id` int(10) NOT NULL,
  `item_id` int(10) NOT NULL,
  `location_id` int(10) NOT NULL,
  `unit_price` decimal(23,10) DEFAULT '0.0000000000',
  `percent_off` decimal(15,3) DEFAULT NULL,
  PRIMARY KEY (`tier_id`,`item_id`,`location_id`),
  KEY `phppos_location_items_tier_prices_ibfk_2` (`location_id`),
  KEY `phppos_location_items_tier_prices_ibfk_3` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `phppos_location_item_kits`
--

CREATE TABLE IF NOT EXISTS `phppos_location_item_kits` (
  `location_id` int(11) NOT NULL,
  `item_kit_id` int(11) NOT NULL,
  `unit_price` decimal(23,10) DEFAULT NULL,
  `cost_price` decimal(23,10) DEFAULT NULL,
  `override_default_tax` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`location_id`,`item_kit_id`),
  KEY `phppos_location_item_kits_ibfk_2` (`item_kit_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `phppos_location_item_kits_taxes`
--

CREATE TABLE IF NOT EXISTS `phppos_location_item_kits_taxes` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `location_id` int(11) NOT NULL,
  `item_kit_id` int(10) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `percent` decimal(16,3) NOT NULL,
  `cumulative` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_tax` (`location_id`,`item_kit_id`,`name`,`percent`),
  KEY `phppos_location_item_kits_taxes_ibfk_2` (`item_kit_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `phppos_location_item_kits_tier_prices`
--

CREATE TABLE IF NOT EXISTS `phppos_location_item_kits_tier_prices` (
  `tier_id` int(10) NOT NULL,
  `item_kit_id` int(10) NOT NULL,
  `location_id` int(10) NOT NULL,
  `unit_price` decimal(23,10) DEFAULT '0.0000000000',
  `percent_off` decimal(15,3) DEFAULT NULL,
  PRIMARY KEY (`tier_id`,`item_kit_id`,`location_id`),
  KEY `phppos_location_item_kits_tier_prices_ibfk_2` (`location_id`),
  KEY `phppos_location_item_kits_tier_prices_ibfk_3` (`item_kit_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `phppos_messages`
--

CREATE TABLE IF NOT EXISTS `phppos_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `message` text COLLATE utf8_unicode_ci NOT NULL,
  `sender_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `phppos_messages_ibfk_1` (`sender_id`),
  KEY `phppos_messages_key_1` (`deleted`,`created_at`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `phppos_message_receiver`
--

CREATE TABLE IF NOT EXISTS `phppos_message_receiver` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `message_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message_read` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `phppos_message_receiver_ibfk_2` (`receiver_id`),
  KEY `phppos_message_receiver_key_1` (`message_id`,`receiver_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `phppos_modules`
--

CREATE TABLE IF NOT EXISTS `phppos_modules` (
  `name_lang_key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `desc_lang_key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `sort` int(10) NOT NULL,
  `icon` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `module_id` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`module_id`),
  UNIQUE KEY `desc_lang_key` (`desc_lang_key`),
  UNIQUE KEY `name_lang_key` (`name_lang_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `phppos_modules`
--

INSERT INTO `phppos_modules` (`name_lang_key`, `desc_lang_key`, `sort`, `icon`, `module_id`) VALUES
('module_config', 'module_config_desc', 100, 'settings', 'config'),
('module_customers', 'module_customers_desc', 10, 'user', 'customers'),
('module_employees', 'module_employees_desc', 80, 'id-badge', 'employees'),
('module_expenses', 'module_expenses_desc', 75, 'money', 'expenses'),
('module_giftcards', 'module_giftcards_desc', 90, 'credit-card', 'giftcards'),
('module_item_kits', 'module_item_kits_desc', 30, 'harddrives', 'item_kits'),
('module_items', 'module_items_desc', 20, 'harddrive', 'items'),
('module_locations', 'module_locations_desc', 110, 'home', 'locations'),
('module_messages', 'module_messages_desc', 120, 'email', 'messages'),
('module_receivings', 'module_receivings_desc', 60, 'cloud-down', 'receivings'),
('module_reports', 'module_reports_desc', 50, 'bar-chart', 'reports'),
('module_sales', 'module_sales_desc', 70, 'shopping-cart', 'sales'),
('module_suppliers', 'module_suppliers_desc', 40, 'download', 'suppliers');

-- --------------------------------------------------------

--
-- Table structure for table `phppos_modules_actions`
--

CREATE TABLE IF NOT EXISTS `phppos_modules_actions` (
  `action_id` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `module_id` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `action_name_key` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `sort` int(11) NOT NULL,
  PRIMARY KEY (`action_id`,`module_id`),
  KEY `phppos_modules_actions_ibfk_1` (`module_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `phppos_modules_actions`
--

INSERT INTO `phppos_modules_actions` (`action_id`, `module_id`, `action_name_key`, `sort`) VALUES
('add_update', 'customers', 'module_action_add_update', 1),
('add_update', 'employees', 'module_action_add_update', 130),
('add_update', 'expenses', 'module_expenses_add_update', 315),
('add_update', 'giftcards', 'module_action_add_update', 200),
('add_update', 'item_kits', 'module_action_add_update', 70),
('add_update', 'items', 'module_action_add_update', 40),
('add_update', 'locations', 'module_action_add_update', 240),
('add_update', 'suppliers', 'module_action_add_update', 100),
('assign_all_locations', 'employees', 'module_action_assign_all_locations', 151),
('count_inventory', 'items', 'items_count_inventory', 65),
('delete', 'customers', 'module_action_delete', 20),
('delete', 'employees', 'module_action_delete', 140),
('delete', 'expenses', 'module_expenses_delete', 330),
('delete', 'giftcards', 'module_action_delete', 210),
('delete', 'item_kits', 'module_action_delete', 80),
('delete', 'items', 'module_action_delete', 50),
('delete', 'locations', 'module_action_delete', 250),
('delete', 'suppliers', 'module_action_delete', 110),
('delete_receiving', 'receivings', 'module_action_delete_receiving', 306),
('delete_sale', 'sales', 'module_action_delete_sale', 230),
('delete_suspended_sale', 'sales', 'module_action_delete_suspended_sale', 181),
('delete_taxes', 'receivings', 'module_action_delete_taxes', 300),
('delete_taxes', 'sales', 'module_action_delete_taxes', 182),
('edit_customer_points', 'customers', 'module_edit_customer_points', 35),
('edit_giftcard_value', 'giftcards', 'module_edit_giftcard_value', 205),
('edit_quantity', 'items', 'items_edit_quantity', 62),
('edit_receiving', 'receivings', 'module_action_edit_receiving', 303),
('edit_sale', 'sales', 'module_edit_sale', 190),
('edit_sale_cost_price', 'sales', 'module_edit_sale_cost_price', 175),
('edit_sale_price', 'sales', 'module_edit_sale_price', 170),
('edit_store_account_balance', 'customers', 'customers_edit_store_account_balance', 31),
('give_discount', 'sales', 'module_give_discount', 180),
('manage_categories', 'items', 'items_manage_categories', 70),
('manage_tags', 'items', 'items_manage_tags', 75),
('search', 'customers', 'module_action_search_customers', 30),
('search', 'employees', 'module_action_search_employees', 150),
('search', 'expenses', 'module_expenses_search', 310),
('search', 'giftcards', 'module_action_search_giftcards', 220),
('search', 'item_kits', 'module_action_search_item_kits', 90),
('search', 'items', 'module_action_search_items', 60),
('search', 'locations', 'module_action_search_locations', 260),
('search', 'suppliers', 'module_action_search_suppliers', 120),
('see_cost_price', 'item_kits', 'module_see_cost_price', 91),
('see_cost_price', 'items', 'module_see_cost_price', 61),
('send_message', 'messages', 'employees_send_message', 350),
('show_cost_price', 'reports', 'reports_show_cost_price', 290),
('show_profit', 'reports', 'reports_show_profit', 280),
('view_all_employee_commissions', 'reports', 'reports_view_all_employee_commissions', 107),
('view_categories', 'reports', 'reports_categories', 100),
('view_closeout', 'reports', 'reports_closeout', 105),
('view_commissions', 'reports', 'reports_commission', 106),
('view_customers', 'reports', 'reports_customers', 120),
('view_deleted_sales', 'reports', 'reports_deleted_sales', 130),
('view_discounts', 'reports', 'reports_discounts', 140),
('view_employees', 'reports', 'reports_employees', 150),
('view_expenses', 'reports', 'module_expenses_report', 155),
('view_giftcards', 'reports', 'reports_giftcards', 160),
('view_inventory_at_all_locations', 'reports', 'reports_view_inventory_at_all_locations', 300),
('view_inventory_reports', 'reports', 'reports_inventory_reports', 170),
('view_item_kits', 'reports', 'module_item_kits', 180),
('view_items', 'reports', 'reports_items', 190),
('view_payments', 'reports', 'reports_payments', 200),
('view_profit_and_loss', 'reports', 'reports_profit_and_loss', 210),
('view_receivings', 'reports', 'reports_receivings', 220),
('view_register_log', 'reports', 'reports_register_log_title', 230),
('view_sales', 'reports', 'reports_sales', 240),
('view_sales_generator', 'reports', 'reports_sales_generator', 110),
('view_store_account', 'reports', 'reports_store_account', 250),
('view_suppliers', 'reports', 'reports_suppliers', 260),
('view_suspended_sales', 'reports', 'reports_suspended_sales', 261),
('view_tags', 'reports', 'common_tags', 264),
('view_taxes', 'reports', 'reports_taxes', 270),
('view_tiers', 'reports', 'reports_tiers', 275),
('view_timeclock', 'reports', 'employees_timeclock', 280);

-- --------------------------------------------------------

--
-- Table structure for table `phppos_people`
--

CREATE TABLE IF NOT EXISTS `phppos_people` (
  `first_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `last_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `phone_number` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `address_1` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `address_2` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `city` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `state` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `zip` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `country` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `comments` text COLLATE utf8_unicode_ci NOT NULL,
  `image_id` int(10) DEFAULT NULL,
  `person_id` int(10) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`person_id`),
  KEY `first_name` (`first_name`),
  KEY `last_name` (`last_name`),
  KEY `email` (`email`),
  KEY `phppos_people_ibfk_1` (`image_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `phppos_people`
--

INSERT INTO `phppos_people` (`first_name`, `last_name`, `phone_number`, `email`, `address_1`, `address_2`, `city`, `state`, `zip`, `country`, `comments`, `image_id`, `person_id`) VALUES
('Lan', 'Nguyễn', '0904143388', 'no-reply@4biz.vn', 'số 8 ngõ 379/8 đội cấn hà nội', '', '', '', '', '', '', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `phppos_permissions`
--

CREATE TABLE IF NOT EXISTS `phppos_permissions` (
  `module_id` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `person_id` int(10) NOT NULL,
  PRIMARY KEY (`module_id`,`person_id`),
  KEY `person_id` (`person_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `phppos_permissions`
--

INSERT INTO `phppos_permissions` (`module_id`, `person_id`) VALUES
('config', 1),
('customers', 1),
('employees', 1),
('expenses', 1),
('giftcards', 1),
('item_kits', 1),
('items', 1),
('locations', 1),
('messages', 1),
('receivings', 1),
('reports', 1),
('sales', 1),
('suppliers', 1);

-- --------------------------------------------------------

--
-- Table structure for table `phppos_permissions_actions`
--

CREATE TABLE IF NOT EXISTS `phppos_permissions_actions` (
  `module_id` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `person_id` int(11) NOT NULL,
  `action_id` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`module_id`,`person_id`,`action_id`),
  KEY `phppos_permissions_actions_ibfk_2` (`person_id`),
  KEY `phppos_permissions_actions_ibfk_3` (`action_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `phppos_permissions_actions`
--

INSERT INTO `phppos_permissions_actions` (`module_id`, `person_id`, `action_id`) VALUES
('customers', 1, 'add_update'),
('customers', 1, 'delete'),
('customers', 1, 'edit_customer_points'),
('customers', 1, 'edit_store_account_balance'),
('customers', 1, 'search'),
('employees', 1, 'add_update'),
('employees', 1, 'assign_all_locations'),
('employees', 1, 'delete'),
('employees', 1, 'search'),
('expenses', 1, 'add_update'),
('expenses', 1, 'delete'),
('expenses', 1, 'search'),
('giftcards', 1, 'add_update'),
('giftcards', 1, 'delete'),
('giftcards', 1, 'edit_giftcard_value'),
('giftcards', 1, 'search'),
('item_kits', 1, 'add_update'),
('item_kits', 1, 'delete'),
('item_kits', 1, 'search'),
('item_kits', 1, 'see_cost_price'),
('items', 1, 'add_update'),
('items', 1, 'count_inventory'),
('items', 1, 'delete'),
('items', 1, 'edit_quantity'),
('items', 1, 'manage_categories'),
('items', 1, 'manage_tags'),
('items', 1, 'search'),
('items', 1, 'see_cost_price'),
('locations', 1, 'add_update'),
('locations', 1, 'delete'),
('locations', 1, 'search'),
('messages', 1, 'send_message'),
('receivings', 1, 'delete_receiving'),
('receivings', 1, 'delete_taxes'),
('receivings', 1, 'edit_receiving'),
('reports', 1, 'show_cost_price'),
('reports', 1, 'show_profit'),
('reports', 1, 'view_all_employee_commissions'),
('reports', 1, 'view_categories'),
('reports', 1, 'view_closeout'),
('reports', 1, 'view_commissions'),
('reports', 1, 'view_customers'),
('reports', 1, 'view_deleted_sales'),
('reports', 1, 'view_discounts'),
('reports', 1, 'view_employees'),
('reports', 1, 'view_expenses'),
('reports', 1, 'view_giftcards'),
('reports', 1, 'view_inventory_at_all_locations'),
('reports', 1, 'view_inventory_reports'),
('reports', 1, 'view_item_kits'),
('reports', 1, 'view_items'),
('reports', 1, 'view_payments'),
('reports', 1, 'view_profit_and_loss'),
('reports', 1, 'view_receivings'),
('reports', 1, 'view_register_log'),
('reports', 1, 'view_sales'),
('reports', 1, 'view_sales_generator'),
('reports', 1, 'view_store_account'),
('reports', 1, 'view_suppliers'),
('reports', 1, 'view_suspended_sales'),
('reports', 1, 'view_tags'),
('reports', 1, 'view_taxes'),
('reports', 1, 'view_tiers'),
('reports', 1, 'view_timeclock'),
('sales', 1, 'delete_sale'),
('sales', 1, 'delete_suspended_sale'),
('sales', 1, 'delete_taxes'),
('sales', 1, 'edit_sale'),
('sales', 1, 'edit_sale_cost_price'),
('sales', 1, 'edit_sale_price'),
('sales', 1, 'give_discount'),
('suppliers', 1, 'add_update'),
('suppliers', 1, 'delete'),
('suppliers', 1, 'search');

-- --------------------------------------------------------

--
-- Table structure for table `phppos_price_tiers`
--

CREATE TABLE IF NOT EXISTS `phppos_price_tiers` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `order` int(10) NOT NULL DEFAULT '0',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Dumping data for table `phppos_price_tiers`
--

INSERT INTO `phppos_price_tiers` (`id`, `order`, `name`) VALUES
(1, 1, 'Đại lý cấp 1'),
(2, 2, 'Đại lý cấp 2');

-- --------------------------------------------------------

--
-- Table structure for table `phppos_receivings`
--

CREATE TABLE IF NOT EXISTS `phppos_receivings` (
  `receiving_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `supplier_id` int(10) DEFAULT NULL,
  `employee_id` int(10) NOT NULL DEFAULT '0',
  `comment` text COLLATE utf8_unicode_ci NOT NULL,
  `receiving_id` int(10) NOT NULL AUTO_INCREMENT,
  `payment_type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `deleted` int(1) NOT NULL DEFAULT '0',
  `deleted_by` int(10) DEFAULT NULL,
  `suspended` int(1) NOT NULL DEFAULT '0',
  `location_id` int(11) NOT NULL,
  `transfer_to_location_id` int(11) DEFAULT NULL,
  `deleted_taxes` text COLLATE utf8_unicode_ci,
  `is_po` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`receiving_id`),
  KEY `supplier_id` (`supplier_id`),
  KEY `employee_id` (`employee_id`),
  KEY `deleted` (`deleted`),
  KEY `location_id` (`location_id`),
  KEY `transfer_to_location_id` (`transfer_to_location_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `phppos_receivings_items`
--

CREATE TABLE IF NOT EXISTS `phppos_receivings_items` (
  `receiving_id` int(10) NOT NULL DEFAULT '0',
  `item_id` int(10) NOT NULL DEFAULT '0',
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `serialnumber` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `line` int(11) NOT NULL DEFAULT '0',
  `quantity_purchased` decimal(23,10) NOT NULL DEFAULT '0.0000000000',
  `quantity_received` decimal(23,10) NOT NULL DEFAULT '0.0000000000',
  `item_cost_price` decimal(23,10) NOT NULL,
  `item_unit_price` decimal(23,10) NOT NULL,
  `discount_percent` decimal(15,3) NOT NULL DEFAULT '0.000',
  `expire_date` date DEFAULT NULL,
  PRIMARY KEY (`receiving_id`,`item_id`,`line`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `phppos_receivings_items_taxes`
--

CREATE TABLE IF NOT EXISTS `phppos_receivings_items_taxes` (
  `receiving_id` int(10) NOT NULL,
  `item_id` int(10) NOT NULL,
  `line` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `percent` decimal(15,3) NOT NULL,
  `cumulative` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`receiving_id`,`item_id`,`line`,`name`,`percent`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `phppos_registers`
--

CREATE TABLE IF NOT EXISTS `phppos_registers` (
  `register_id` int(11) NOT NULL AUTO_INCREMENT,
  `location_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `deleted` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`register_id`),
  KEY `deleted` (`deleted`),
  KEY `phppos_registers_ibfk_1` (`location_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `phppos_registers`
--

INSERT INTO `phppos_registers` (`register_id`, `location_id`, `name`, `deleted`) VALUES
(1, 1, 'Default', 0);

-- --------------------------------------------------------

--
-- Table structure for table `phppos_registers_cart`
--

CREATE TABLE IF NOT EXISTS `phppos_registers_cart` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `register_id` int(11) NOT NULL,
  `data` longblob NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `register_id` (`register_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `phppos_register_currency_denominations`
--

CREATE TABLE IF NOT EXISTS `phppos_register_currency_denominations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` decimal(23,10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=8 ;

--
-- Dumping data for table `phppos_register_currency_denominations`
--

INSERT INTO `phppos_register_currency_denominations` (`id`, `name`, `value`) VALUES
(1, '500.000', '500000.0000000000'),
(2, '200.000', '200000.0000000000'),
(3, '100.000', '100000.0000000000'),
(4, '50.000', '50000.0000000000'),
(5, '20.000', '20000.0000000000'),
(6, '10.000', '10000.0000000000'),
(7, '5.000', '5000.0000000000');

-- --------------------------------------------------------

--
-- Table structure for table `phppos_register_log`
--

CREATE TABLE IF NOT EXISTS `phppos_register_log` (
  `register_log_id` int(10) NOT NULL AUTO_INCREMENT,
  `employee_id_open` int(10) NOT NULL,
  `employee_id_close` int(11) DEFAULT NULL,
  `register_id` int(11) DEFAULT NULL,
  `shift_start` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `shift_end` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `open_amount` decimal(23,10) NOT NULL,
  `close_amount` decimal(23,10) NOT NULL,
  `cash_sales_amount` decimal(23,10) NOT NULL,
  `total_cash_additions` decimal(23,10) NOT NULL DEFAULT '0.0000000000',
  `total_cash_subtractions` decimal(23,10) NOT NULL DEFAULT '0.0000000000',
  `notes` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `deleted` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`register_log_id`),
  KEY `phppos_register_log_ibfk_1` (`employee_id_open`),
  KEY `phppos_register_log_ibfk_2` (`register_id`),
  KEY `phppos_register_log_ibfk_3` (`employee_id_close`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `phppos_register_log_audit`
--

CREATE TABLE IF NOT EXISTS `phppos_register_log_audit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `register_log_id` int(10) NOT NULL,
  `employee_id` int(10) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `amount` decimal(23,10) NOT NULL DEFAULT '0.0000000000',
  `note` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `register_log_audit_ibfk_1` (`register_log_id`),
  KEY `register_log_audit_ibfk_2` (`employee_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `phppos_sales`
--

CREATE TABLE IF NOT EXISTS `phppos_sales` (
  `sale_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `customer_id` int(10) DEFAULT NULL,
  `employee_id` int(10) NOT NULL DEFAULT '0',
  `sold_by_employee_id` int(10) DEFAULT NULL,
  `comment` text COLLATE utf8_unicode_ci NOT NULL,
  `show_comment_on_receipt` int(1) NOT NULL DEFAULT '0',
  `sale_id` int(10) NOT NULL AUTO_INCREMENT,
  `payment_type` text COLLATE utf8_unicode_ci,
  `cc_ref_no` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `auth_code` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
  `deleted_by` int(10) DEFAULT NULL,
  `deleted` int(1) NOT NULL DEFAULT '0',
  `suspended` int(1) NOT NULL DEFAULT '0',
  `store_account_payment` int(1) NOT NULL DEFAULT '0',
  `was_layaway` int(1) NOT NULL DEFAULT '0',
  `was_estimate` int(1) NOT NULL DEFAULT '0',
  `location_id` int(11) NOT NULL,
  `register_id` int(11) DEFAULT NULL,
  `tier_id` int(10) DEFAULT NULL,
  `points_used` decimal(23,10) NOT NULL DEFAULT '0.0000000000',
  `points_gained` decimal(23,10) NOT NULL DEFAULT '0.0000000000',
  `did_redeem_discount` int(1) NOT NULL DEFAULT '0',
  `signature_image_id` int(10) DEFAULT NULL,
  `deleted_taxes` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`sale_id`),
  KEY `customer_id` (`customer_id`),
  KEY `employee_id` (`employee_id`),
  KEY `deleted` (`deleted`),
  KEY `location_id` (`location_id`),
  KEY `phppos_sales_ibfk_4` (`deleted_by`),
  KEY `sales_search` (`location_id`,`store_account_payment`,`sale_time`,`sale_id`),
  KEY `phppos_sales_ibfk_5` (`tier_id`),
  KEY `phppos_sales_ibfk_7` (`register_id`),
  KEY `phppos_sales_ibfk_6` (`sold_by_employee_id`),
  KEY `phppos_sales_ibfk_8` (`signature_image_id`),
  KEY `was_layaway` (`was_layaway`),
  KEY `was_estimate` (`was_estimate`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `phppos_sales_items`
--

CREATE TABLE IF NOT EXISTS `phppos_sales_items` (
  `sale_id` int(10) NOT NULL DEFAULT '0',
  `item_id` int(10) NOT NULL DEFAULT '0',
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `serialnumber` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `line` int(11) NOT NULL DEFAULT '0',
  `quantity_purchased` decimal(23,10) NOT NULL DEFAULT '0.0000000000',
  `item_cost_price` decimal(23,10) NOT NULL,
  `item_unit_price` decimal(23,10) NOT NULL,
  `discount_percent` decimal(15,3) NOT NULL DEFAULT '0.000',
  `commission` decimal(23,10) NOT NULL DEFAULT '0.0000000000',
  PRIMARY KEY (`sale_id`,`item_id`,`line`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `phppos_sales_items_taxes`
--

CREATE TABLE IF NOT EXISTS `phppos_sales_items_taxes` (
  `sale_id` int(10) NOT NULL,
  `item_id` int(10) NOT NULL,
  `line` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `percent` decimal(15,3) NOT NULL,
  `cumulative` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`sale_id`,`item_id`,`line`,`name`,`percent`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `phppos_sales_item_kits`
--

CREATE TABLE IF NOT EXISTS `phppos_sales_item_kits` (
  `sale_id` int(10) NOT NULL DEFAULT '0',
  `item_kit_id` int(10) NOT NULL DEFAULT '0',
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `line` int(11) NOT NULL DEFAULT '0',
  `quantity_purchased` decimal(23,10) NOT NULL DEFAULT '0.0000000000',
  `item_kit_cost_price` decimal(23,10) NOT NULL,
  `item_kit_unit_price` decimal(23,10) NOT NULL,
  `discount_percent` decimal(15,3) NOT NULL DEFAULT '0.000',
  `commission` decimal(23,10) NOT NULL DEFAULT '0.0000000000',
  PRIMARY KEY (`sale_id`,`item_kit_id`,`line`),
  KEY `item_kit_id` (`item_kit_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `phppos_sales_item_kits_taxes`
--

CREATE TABLE IF NOT EXISTS `phppos_sales_item_kits_taxes` (
  `sale_id` int(10) NOT NULL,
  `item_kit_id` int(10) NOT NULL,
  `line` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `percent` decimal(15,3) NOT NULL,
  `cumulative` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`sale_id`,`item_kit_id`,`line`,`name`,`percent`),
  KEY `item_id` (`item_kit_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `phppos_sales_payments`
--

CREATE TABLE IF NOT EXISTS `phppos_sales_payments` (
  `payment_id` int(10) NOT NULL AUTO_INCREMENT,
  `sale_id` int(10) NOT NULL,
  `payment_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `payment_amount` decimal(23,10) NOT NULL,
  `auth_code` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
  `ref_no` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
  `cc_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
  `acq_ref_data` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
  `process_data` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
  `entry_method` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
  `aid` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
  `tvr` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
  `iad` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
  `tsi` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
  `arc` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
  `cvm` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
  `tran_type` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
  `application_label` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
  `truncated_card` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
  `card_issuer` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
  `payment_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`payment_id`),
  KEY `sale_id` (`sale_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `phppos_sessions`
--

CREATE TABLE IF NOT EXISTS `phppos_sessions` (
  `id` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `ip_address` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `data` longblob NOT NULL,
  PRIMARY KEY (`id`),
  KEY `phppos_sessions_timestamp` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `phppos_sessions`
--

INSERT INTO `phppos_sessions` (`id`, `ip_address`, `timestamp`, `data`) VALUES
('4e4781c391f432f81b3b58feaac42834d217a2a1', '117.6.1.149', 1462518059, 0x706572736f6e5f69647c733a313a2231223b6b6565705f616c6976657c693a313436323531373636383b);

-- --------------------------------------------------------

--
-- Table structure for table `phppos_store_accounts`
--

CREATE TABLE IF NOT EXISTS `phppos_store_accounts` (
  `sno` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `sale_id` int(11) DEFAULT NULL,
  `transaction_amount` decimal(23,10) NOT NULL DEFAULT '0.0000000000',
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `balance` decimal(23,10) NOT NULL DEFAULT '0.0000000000',
  `comment` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`sno`),
  KEY `phppos_store_accounts_ibfk_1` (`sale_id`),
  KEY `phppos_store_accounts_ibfk_2` (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `phppos_suppliers`
--

CREATE TABLE IF NOT EXISTS `phppos_suppliers` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `person_id` int(10) NOT NULL,
  `company_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `account_number` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `override_default_tax` int(1) NOT NULL DEFAULT '0',
  `deleted` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `account_number` (`account_number`),
  KEY `person_id` (`person_id`),
  KEY `deleted` (`deleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `phppos_suppliers_taxes`
--

CREATE TABLE IF NOT EXISTS `phppos_suppliers_taxes` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `supplier_id` int(10) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `percent` decimal(15,3) NOT NULL,
  `cumulative` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_tax` (`supplier_id`,`name`,`percent`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `phppos_tags`
--

CREATE TABLE IF NOT EXISTS `phppos_tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `deleted` int(1) NOT NULL DEFAULT '0',
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tag_name` (`name`),
  KEY `deleted` (`deleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `phppos_additional_item_numbers`
--
ALTER TABLE `phppos_additional_item_numbers`
  ADD CONSTRAINT `phppos_additional_item_numbers_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `phppos_items` (`item_id`);

--
-- Constraints for table `phppos_categories`
--
ALTER TABLE `phppos_categories`
  ADD CONSTRAINT `phppos_categories_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `phppos_categories` (`id`);

--
-- Constraints for table `phppos_customers`
--
ALTER TABLE `phppos_customers`
  ADD CONSTRAINT `phppos_customers_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `phppos_people` (`person_id`),
  ADD CONSTRAINT `phppos_customers_ibfk_2` FOREIGN KEY (`tier_id`) REFERENCES `phppos_price_tiers` (`id`);

--
-- Constraints for table `phppos_customers_taxes`
--
ALTER TABLE `phppos_customers_taxes`
  ADD CONSTRAINT `phppos_customers_taxes_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `phppos_customers` (`person_id`) ON DELETE CASCADE;

--
-- Constraints for table `phppos_employees`
--
ALTER TABLE `phppos_employees`
  ADD CONSTRAINT `phppos_employees_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `phppos_people` (`person_id`);

--
-- Constraints for table `phppos_employees_locations`
--
ALTER TABLE `phppos_employees_locations`
  ADD CONSTRAINT `phppos_employees_locations_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `phppos_employees` (`person_id`),
  ADD CONSTRAINT `phppos_employees_locations_ibfk_2` FOREIGN KEY (`location_id`) REFERENCES `phppos_locations` (`location_id`);

--
-- Constraints for table `phppos_employees_reset_password`
--
ALTER TABLE `phppos_employees_reset_password`
  ADD CONSTRAINT `phppos_employees_reset_password_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `phppos_employees` (`person_id`);

--
-- Constraints for table `phppos_employees_time_clock`
--
ALTER TABLE `phppos_employees_time_clock`
  ADD CONSTRAINT `phppos_employees_time_clock_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `phppos_employees` (`person_id`),
  ADD CONSTRAINT `phppos_employees_time_clock_ibfk_2` FOREIGN KEY (`location_id`) REFERENCES `phppos_locations` (`location_id`);

--
-- Constraints for table `phppos_expenses`
--
ALTER TABLE `phppos_expenses`
  ADD CONSTRAINT `phppos_expenses_ibfk_1` FOREIGN KEY (`location_id`) REFERENCES `phppos_locations` (`location_id`),
  ADD CONSTRAINT `phppos_expenses_ibfk_2` FOREIGN KEY (`employee_id`) REFERENCES `phppos_employees` (`person_id`),
  ADD CONSTRAINT `phppos_expenses_ibfk_3` FOREIGN KEY (`category_id`) REFERENCES `phppos_categories` (`id`),
  ADD CONSTRAINT `phppos_expenses_ibfk_4` FOREIGN KEY (`approved_employee_id`) REFERENCES `phppos_employees` (`person_id`);

--
-- Constraints for table `phppos_giftcards`
--
ALTER TABLE `phppos_giftcards`
  ADD CONSTRAINT `phppos_giftcards_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `phppos_customers` (`person_id`);

--
-- Constraints for table `phppos_giftcards_log`
--
ALTER TABLE `phppos_giftcards_log`
  ADD CONSTRAINT `phppos_giftcards_log_ibfk_1` FOREIGN KEY (`giftcard_id`) REFERENCES `phppos_giftcards` (`giftcard_id`);

--
-- Constraints for table `phppos_inventory`
--
ALTER TABLE `phppos_inventory`
  ADD CONSTRAINT `phppos_inventory_ibfk_1` FOREIGN KEY (`trans_items`) REFERENCES `phppos_items` (`item_id`),
  ADD CONSTRAINT `phppos_inventory_ibfk_2` FOREIGN KEY (`trans_user`) REFERENCES `phppos_employees` (`person_id`),
  ADD CONSTRAINT `phppos_inventory_ibfk_3` FOREIGN KEY (`location_id`) REFERENCES `phppos_locations` (`location_id`);

--
-- Constraints for table `phppos_inventory_counts`
--
ALTER TABLE `phppos_inventory_counts`
  ADD CONSTRAINT `phppos_inventory_counts_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `phppos_employees` (`person_id`),
  ADD CONSTRAINT `phppos_inventory_counts_ibfk_2` FOREIGN KEY (`location_id`) REFERENCES `phppos_locations` (`location_id`);

--
-- Constraints for table `phppos_inventory_counts_items`
--
ALTER TABLE `phppos_inventory_counts_items`
  ADD CONSTRAINT `phppos_inventory_counts_items_ibfk_1` FOREIGN KEY (`inventory_counts_id`) REFERENCES `phppos_inventory_counts` (`id`),
  ADD CONSTRAINT `phppos_inventory_counts_items_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `phppos_items` (`item_id`);

--
-- Constraints for table `phppos_items`
--
ALTER TABLE `phppos_items`
  ADD CONSTRAINT `phppos_items_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `phppos_suppliers` (`person_id`),
  ADD CONSTRAINT `phppos_items_ibfk_2` FOREIGN KEY (`image_id`) REFERENCES `phppos_app_files` (`file_id`),
  ADD CONSTRAINT `phppos_items_ibfk_3` FOREIGN KEY (`category_id`) REFERENCES `phppos_categories` (`id`);

--
-- Constraints for table `phppos_items_tags`
--
ALTER TABLE `phppos_items_tags`
  ADD CONSTRAINT `phppos_items_tags_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `phppos_items` (`item_id`),
  ADD CONSTRAINT `phppos_items_tags_ibfk_2` FOREIGN KEY (`tag_id`) REFERENCES `phppos_tags` (`id`);

--
-- Constraints for table `phppos_items_taxes`
--
ALTER TABLE `phppos_items_taxes`
  ADD CONSTRAINT `phppos_items_taxes_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `phppos_items` (`item_id`) ON DELETE CASCADE;

--
-- Constraints for table `phppos_items_tier_prices`
--
ALTER TABLE `phppos_items_tier_prices`
  ADD CONSTRAINT `phppos_items_tier_prices_ibfk_1` FOREIGN KEY (`tier_id`) REFERENCES `phppos_price_tiers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `phppos_items_tier_prices_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `phppos_items` (`item_id`);

--
-- Constraints for table `phppos_item_kits`
--
ALTER TABLE `phppos_item_kits`
  ADD CONSTRAINT `phppos_item_kits_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `phppos_categories` (`id`);

--
-- Constraints for table `phppos_item_kits_tags`
--
ALTER TABLE `phppos_item_kits_tags`
  ADD CONSTRAINT `phppos_item_kits_tags_ibfk_1` FOREIGN KEY (`item_kit_id`) REFERENCES `phppos_item_kits` (`item_kit_id`),
  ADD CONSTRAINT `phppos_item_kits_tags_ibfk_2` FOREIGN KEY (`tag_id`) REFERENCES `phppos_tags` (`id`);

--
-- Constraints for table `phppos_item_kits_taxes`
--
ALTER TABLE `phppos_item_kits_taxes`
  ADD CONSTRAINT `phppos_item_kits_taxes_ibfk_1` FOREIGN KEY (`item_kit_id`) REFERENCES `phppos_item_kits` (`item_kit_id`) ON DELETE CASCADE;

--
-- Constraints for table `phppos_item_kits_tier_prices`
--
ALTER TABLE `phppos_item_kits_tier_prices`
  ADD CONSTRAINT `phppos_item_kits_tier_prices_ibfk_1` FOREIGN KEY (`tier_id`) REFERENCES `phppos_price_tiers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `phppos_item_kits_tier_prices_ibfk_2` FOREIGN KEY (`item_kit_id`) REFERENCES `phppos_item_kits` (`item_kit_id`);

--
-- Constraints for table `phppos_item_kit_items`
--
ALTER TABLE `phppos_item_kit_items`
  ADD CONSTRAINT `phppos_item_kit_items_ibfk_1` FOREIGN KEY (`item_kit_id`) REFERENCES `phppos_item_kits` (`item_kit_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `phppos_item_kit_items_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `phppos_items` (`item_id`) ON DELETE CASCADE;

--
-- Constraints for table `phppos_location_items`
--
ALTER TABLE `phppos_location_items`
  ADD CONSTRAINT `phppos_location_items_ibfk_1` FOREIGN KEY (`location_id`) REFERENCES `phppos_locations` (`location_id`),
  ADD CONSTRAINT `phppos_location_items_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `phppos_items` (`item_id`);

--
-- Constraints for table `phppos_location_items_taxes`
--
ALTER TABLE `phppos_location_items_taxes`
  ADD CONSTRAINT `phppos_location_items_taxes_ibfk_1` FOREIGN KEY (`location_id`) REFERENCES `phppos_locations` (`location_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `phppos_location_items_taxes_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `phppos_items` (`item_id`) ON DELETE CASCADE;

--
-- Constraints for table `phppos_location_items_tier_prices`
--
ALTER TABLE `phppos_location_items_tier_prices`
  ADD CONSTRAINT `phppos_location_items_tier_prices_ibfk_1` FOREIGN KEY (`tier_id`) REFERENCES `phppos_price_tiers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `phppos_location_items_tier_prices_ibfk_2` FOREIGN KEY (`location_id`) REFERENCES `phppos_locations` (`location_id`),
  ADD CONSTRAINT `phppos_location_items_tier_prices_ibfk_3` FOREIGN KEY (`item_id`) REFERENCES `phppos_items` (`item_id`);

--
-- Constraints for table `phppos_location_item_kits`
--
ALTER TABLE `phppos_location_item_kits`
  ADD CONSTRAINT `phppos_location_item_kits_ibfk_1` FOREIGN KEY (`location_id`) REFERENCES `phppos_locations` (`location_id`),
  ADD CONSTRAINT `phppos_location_item_kits_ibfk_2` FOREIGN KEY (`item_kit_id`) REFERENCES `phppos_item_kits` (`item_kit_id`);

--
-- Constraints for table `phppos_location_item_kits_taxes`
--
ALTER TABLE `phppos_location_item_kits_taxes`
  ADD CONSTRAINT `phppos_location_item_kits_taxes_ibfk_1` FOREIGN KEY (`location_id`) REFERENCES `phppos_locations` (`location_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `phppos_location_item_kits_taxes_ibfk_2` FOREIGN KEY (`item_kit_id`) REFERENCES `phppos_item_kits` (`item_kit_id`) ON DELETE CASCADE;

--
-- Constraints for table `phppos_location_item_kits_tier_prices`
--
ALTER TABLE `phppos_location_item_kits_tier_prices`
  ADD CONSTRAINT `phppos_location_item_kits_tier_prices_ibfk_1` FOREIGN KEY (`tier_id`) REFERENCES `phppos_price_tiers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `phppos_location_item_kits_tier_prices_ibfk_2` FOREIGN KEY (`location_id`) REFERENCES `phppos_locations` (`location_id`),
  ADD CONSTRAINT `phppos_location_item_kits_tier_prices_ibfk_3` FOREIGN KEY (`item_kit_id`) REFERENCES `phppos_item_kits` (`item_kit_id`);

--
-- Constraints for table `phppos_messages`
--
ALTER TABLE `phppos_messages`
  ADD CONSTRAINT `phppos_messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `phppos_employees` (`person_id`);

--
-- Constraints for table `phppos_message_receiver`
--
ALTER TABLE `phppos_message_receiver`
  ADD CONSTRAINT `phppos_message_receiver_ibfk_1` FOREIGN KEY (`message_id`) REFERENCES `phppos_messages` (`id`),
  ADD CONSTRAINT `phppos_message_receiver_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `phppos_employees` (`person_id`);

--
-- Constraints for table `phppos_modules_actions`
--
ALTER TABLE `phppos_modules_actions`
  ADD CONSTRAINT `phppos_modules_actions_ibfk_1` FOREIGN KEY (`module_id`) REFERENCES `phppos_modules` (`module_id`);

--
-- Constraints for table `phppos_people`
--
ALTER TABLE `phppos_people`
  ADD CONSTRAINT `phppos_people_ibfk_1` FOREIGN KEY (`image_id`) REFERENCES `phppos_app_files` (`file_id`);

--
-- Constraints for table `phppos_permissions`
--
ALTER TABLE `phppos_permissions`
  ADD CONSTRAINT `phppos_permissions_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `phppos_employees` (`person_id`),
  ADD CONSTRAINT `phppos_permissions_ibfk_2` FOREIGN KEY (`module_id`) REFERENCES `phppos_modules` (`module_id`);

--
-- Constraints for table `phppos_permissions_actions`
--
ALTER TABLE `phppos_permissions_actions`
  ADD CONSTRAINT `phppos_permissions_actions_ibfk_1` FOREIGN KEY (`module_id`) REFERENCES `phppos_modules` (`module_id`),
  ADD CONSTRAINT `phppos_permissions_actions_ibfk_2` FOREIGN KEY (`person_id`) REFERENCES `phppos_employees` (`person_id`),
  ADD CONSTRAINT `phppos_permissions_actions_ibfk_3` FOREIGN KEY (`action_id`) REFERENCES `phppos_modules_actions` (`action_id`);

--
-- Constraints for table `phppos_receivings`
--
ALTER TABLE `phppos_receivings`
  ADD CONSTRAINT `phppos_receivings_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `phppos_employees` (`person_id`),
  ADD CONSTRAINT `phppos_receivings_ibfk_2` FOREIGN KEY (`supplier_id`) REFERENCES `phppos_suppliers` (`person_id`),
  ADD CONSTRAINT `phppos_receivings_ibfk_3` FOREIGN KEY (`location_id`) REFERENCES `phppos_locations` (`location_id`),
  ADD CONSTRAINT `phppos_receivings_ibfk_4` FOREIGN KEY (`transfer_to_location_id`) REFERENCES `phppos_locations` (`location_id`);

--
-- Constraints for table `phppos_receivings_items`
--
ALTER TABLE `phppos_receivings_items`
  ADD CONSTRAINT `phppos_receivings_items_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `phppos_items` (`item_id`),
  ADD CONSTRAINT `phppos_receivings_items_ibfk_2` FOREIGN KEY (`receiving_id`) REFERENCES `phppos_receivings` (`receiving_id`);

--
-- Constraints for table `phppos_receivings_items_taxes`
--
ALTER TABLE `phppos_receivings_items_taxes`
  ADD CONSTRAINT `phppos_receivings_items_taxes_ibfk_1` FOREIGN KEY (`receiving_id`) REFERENCES `phppos_receivings` (`receiving_id`),
  ADD CONSTRAINT `phppos_receivings_items_taxes_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `phppos_items` (`item_id`);

--
-- Constraints for table `phppos_registers`
--
ALTER TABLE `phppos_registers`
  ADD CONSTRAINT `phppos_registers_ibfk_1` FOREIGN KEY (`location_id`) REFERENCES `phppos_locations` (`location_id`);

--
-- Constraints for table `phppos_registers_cart`
--
ALTER TABLE `phppos_registers_cart`
  ADD CONSTRAINT `phppos_registers_cart_ibfk_1` FOREIGN KEY (`register_id`) REFERENCES `phppos_registers` (`register_id`);

--
-- Constraints for table `phppos_register_log`
--
ALTER TABLE `phppos_register_log`
  ADD CONSTRAINT `phppos_register_log_ibfk_1` FOREIGN KEY (`employee_id_open`) REFERENCES `phppos_employees` (`person_id`),
  ADD CONSTRAINT `phppos_register_log_ibfk_2` FOREIGN KEY (`register_id`) REFERENCES `phppos_registers` (`register_id`),
  ADD CONSTRAINT `phppos_register_log_ibfk_3` FOREIGN KEY (`employee_id_close`) REFERENCES `phppos_employees` (`person_id`);

--
-- Constraints for table `phppos_register_log_audit`
--
ALTER TABLE `phppos_register_log_audit`
  ADD CONSTRAINT `register_log_audit_ibfk_1` FOREIGN KEY (`register_log_id`) REFERENCES `phppos_register_log` (`register_log_id`),
  ADD CONSTRAINT `register_log_audit_ibfk_2` FOREIGN KEY (`employee_id`) REFERENCES `phppos_employees` (`person_id`);

--
-- Constraints for table `phppos_sales`
--
ALTER TABLE `phppos_sales`
  ADD CONSTRAINT `phppos_sales_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `phppos_employees` (`person_id`),
  ADD CONSTRAINT `phppos_sales_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `phppos_customers` (`person_id`),
  ADD CONSTRAINT `phppos_sales_ibfk_3` FOREIGN KEY (`location_id`) REFERENCES `phppos_locations` (`location_id`),
  ADD CONSTRAINT `phppos_sales_ibfk_4` FOREIGN KEY (`deleted_by`) REFERENCES `phppos_employees` (`person_id`),
  ADD CONSTRAINT `phppos_sales_ibfk_5` FOREIGN KEY (`tier_id`) REFERENCES `phppos_price_tiers` (`id`),
  ADD CONSTRAINT `phppos_sales_ibfk_6` FOREIGN KEY (`sold_by_employee_id`) REFERENCES `phppos_employees` (`person_id`),
  ADD CONSTRAINT `phppos_sales_ibfk_7` FOREIGN KEY (`register_id`) REFERENCES `phppos_registers` (`register_id`),
  ADD CONSTRAINT `phppos_sales_ibfk_8` FOREIGN KEY (`signature_image_id`) REFERENCES `phppos_app_files` (`file_id`);

--
-- Constraints for table `phppos_sales_items`
--
ALTER TABLE `phppos_sales_items`
  ADD CONSTRAINT `phppos_sales_items_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `phppos_items` (`item_id`),
  ADD CONSTRAINT `phppos_sales_items_ibfk_2` FOREIGN KEY (`sale_id`) REFERENCES `phppos_sales` (`sale_id`);

--
-- Constraints for table `phppos_sales_items_taxes`
--
ALTER TABLE `phppos_sales_items_taxes`
  ADD CONSTRAINT `phppos_sales_items_taxes_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `phppos_sales_items` (`sale_id`),
  ADD CONSTRAINT `phppos_sales_items_taxes_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `phppos_items` (`item_id`);

--
-- Constraints for table `phppos_sales_item_kits`
--
ALTER TABLE `phppos_sales_item_kits`
  ADD CONSTRAINT `phppos_sales_item_kits_ibfk_1` FOREIGN KEY (`item_kit_id`) REFERENCES `phppos_item_kits` (`item_kit_id`),
  ADD CONSTRAINT `phppos_sales_item_kits_ibfk_2` FOREIGN KEY (`sale_id`) REFERENCES `phppos_sales` (`sale_id`);

--
-- Constraints for table `phppos_sales_item_kits_taxes`
--
ALTER TABLE `phppos_sales_item_kits_taxes`
  ADD CONSTRAINT `phppos_sales_item_kits_taxes_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `phppos_sales_item_kits` (`sale_id`),
  ADD CONSTRAINT `phppos_sales_item_kits_taxes_ibfk_2` FOREIGN KEY (`item_kit_id`) REFERENCES `phppos_item_kits` (`item_kit_id`);

--
-- Constraints for table `phppos_sales_payments`
--
ALTER TABLE `phppos_sales_payments`
  ADD CONSTRAINT `phppos_sales_payments_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `phppos_sales` (`sale_id`);

--
-- Constraints for table `phppos_store_accounts`
--
ALTER TABLE `phppos_store_accounts`
  ADD CONSTRAINT `phppos_store_accounts_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `phppos_sales` (`sale_id`),
  ADD CONSTRAINT `phppos_store_accounts_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `phppos_customers` (`person_id`);

--
-- Constraints for table `phppos_suppliers`
--
ALTER TABLE `phppos_suppliers`
  ADD CONSTRAINT `phppos_suppliers_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `phppos_people` (`person_id`);

--
-- Constraints for table `phppos_suppliers_taxes`
--
ALTER TABLE `phppos_suppliers_taxes`
  ADD CONSTRAINT `phppos_suppliers_taxes_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `phppos_suppliers` (`person_id`) ON DELETE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
