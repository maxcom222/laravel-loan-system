

--
-- Database: `repairshop`
--

-- --------------------------------------------------------

--
-- Table structure for table `activations`
--

DROP TABLE IF EXISTS `activations`;
CREATE TABLE IF NOT EXISTS `activations` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED NOT NULL,
  `code` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `completed` tinyint(1) NOT NULL DEFAULT '0',
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `activations`
--

INSERT INTO `activations` (`id`, `user_id`, `code`, `completed`, `completed_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'KnMwPS9dSmOFbBY3Mz24e4SJVvW4RG7o', 1, '2016-12-09 15:23:31', '2016-12-09 15:23:31', '2016-12-09 15:23:31'),
(2, 2, '8oKXkjb1YgCgzKHiIxbiAUuNkkMPbftz', 1, '2016-12-09 15:50:23', '2016-12-09 15:50:22', '2016-12-09 15:50:23');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

DROP TABLE IF EXISTS `departments`;
CREATE TABLE IF NOT EXISTS `departments` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

DROP TABLE IF EXISTS `invoices`;
CREATE TABLE IF NOT EXISTS `invoices` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `repair_id` int(10) UNSIGNED DEFAULT NULL,
  `title` text COLLATE utf8_unicode_ci,
  `invoice_amount` decimal(10,2) NOT NULL,
  `status` enum('paid','unpaid','partially_paid') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'unpaid',
  `notes` text COLLATE utf8_unicode_ci,
  `emailed` tinyint(4) NOT NULL DEFAULT '0',
  `emailed_at` timestamp NULL DEFAULT NULL,
  `alert_overdue` tinyint(4) NOT NULL DEFAULT '0',
  `created_on` date NOT NULL,
  `due_on` date DEFAULT NULL,
  `modified_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `tax` decimal(10,2) NOT NULL DEFAULT '0.00',
  `discount` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `invoices`
--

-- --------------------------------------------------------

--
-- Table structure for table `invoice_items`
--

DROP TABLE IF EXISTS `invoice_items`;
CREATE TABLE IF NOT EXISTS `invoice_items` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `invoice_id` int(10) UNSIGNED DEFAULT NULL,
  `tax_rate` decimal(10,2) NOT NULL DEFAULT '0.00',
  `qty` decimal(10,2) NOT NULL DEFAULT '0.00',
  `unit_cost` decimal(10,2) NOT NULL DEFAULT '0.00',
  `tax_total` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_cost` decimal(10,2) NOT NULL DEFAULT '0.00',
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `invoice_items`
--



-- --------------------------------------------------------

--
-- Table structure for table `invoice_payments`
--

DROP TABLE IF EXISTS `invoice_payments`;
CREATE TABLE IF NOT EXISTS `invoice_payments` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED NOT NULL,
  `invoice_id` int(10) UNSIGNED NOT NULL,
  `payment_method` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `receipt` text COLLATE utf8_unicode_ci,
  `payment_slip` text COLLATE utf8_unicode_ci,
  `invoice_amount` decimal(10,2) NOT NULL,
  `notes` text COLLATE utf8_unicode_ci,
  `payment_date` date NOT NULL,
  `payment_month` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `payment_year` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_on` date NOT NULL,
  `modified_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `invoice_payments_invoice_id_foreign` (`invoice_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `invoice_payments`
--



-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
CREATE TABLE IF NOT EXISTS `messages` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `message_id` int(10) UNSIGNED DEFAULT NULL,
  `title` text COLLATE utf8_unicode_ci,
  `message` text COLLATE utf8_unicode_ci,
  `attach_file` text COLLATE utf8_unicode_ci,
  `to_id` int(11) NOT NULL,
  `from_id` int(11) NOT NULL,
  `read` tinyint(4) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `migration` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`migration`, `batch`) VALUES
('2014_07_02_230147_migration_cartalyst_sentinel', 1),
('2016_07_23_173004_create_repairs_table', 1),
('2016_07_23_173022_create_invoices_table', 1),
('2016_07_23_173038_create_invoice_payments_table', 1),
('2016_07_23_173125_create_tax_table', 1),
('2016_07_23_173157_create_messages_table', 1),
('2016_07_23_173226_create_sms_table', 1),
('2016_07_23_173242_create_settings_table', 1),
('2016_07_23_191320_create_tasks_table', 1),
('2016_07_24_054644_create_repairs_categories_table', 1),
('2016_10_27_054220_create_products_table', 1),
('2016_10_27_080658_create_tickets_table', 1),
('2016_10_27_080737_create_ticket_replies_table', 1),
('2016_10_27_081757_create_departments_table', 1),
('2016_11_05_062734_create_permissions_table', 1),
('2016_11_06_060828_update_from_v1_0tov1_1', 1),
('2016_11_10_061314_create_repair_status_table', 1),
('2016_12_09_173510_create_invoice_items_table', 2),
('2016_12_09_181951_drop_invoice_tax_id_column', 2),
('2016_12_09_182431_add_invoice_columns', 3),
('2016_12_09_182754_insert_status_data', 4),
('2016_12_09_183917_add_repair_columns', 5),
('2016_12_09_184350_update_current_repair_status', 6),
('2016_12_09_190343_update_invoice_items_table', 7),
('2016_12_09_205730_drop_repair_columns', 8),
('2016_12_11_092035_add_more_settings', 9),
('2016_12_11_113748_update_system_version', 10);

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `parent_id`, `name`, `slug`, `description`) VALUES
(1, 0, 'Repairs', 'repairs', 'Access Repairs Menu'),
(2, 1, 'create_repair', 'create_repair', ''),
(3, 1, 'edit_repair', 'edit_repair', ''),
(4, 1, 'delete_repair', 'delete_repair', ''),
(5, 0, 'Sales', 'sales', 'Access sales menu'),
(6, 5, 'invoices', 'invoices', 'Access Invoices'),
(7, 5, 'edit_invoice', 'edit_invoice', ''),
(8, 5, 'add_payment', 'add_payment', ''),
(9, 5, 'custom_reports', 'custom_reports', ''),
(10, 5, 'financial_overview', 'financial_overview', ''),
(11, 0, 'Products', 'products', 'Access Products  Menu'),
(12, 11, 'create_product', 'create_product', ''),
(13, 11, 'edit_product', 'edit_product', ''),
(14, 11, 'delete_product', 'delete_product', ''),
(15, 0, 'Tickets', 'tickets', 'Access Tickets Menu'),
(16, 15, 'create_ticket', 'create_ticket', ''),
(17, 15, 'edit_ticket', 'edit_ticket', ''),
(18, 15, 'delete_ticket', 'delete_ticket', ''),
(19, 15, 'change_status', 'change_status', ''),
(20, 0, 'Users', 'users', 'Access Users Menu'),
(21, 20, 'create_user', 'create_user', ''),
(22, 20, 'edit_user', 'edit_user', ''),
(23, 20, 'delete_user', 'delete_user', ''),
(24, 20, 'manage_roles', 'manage_roles', ''),
(25, 0, 'Settings', 'settings', 'Access Settings Menu'),
(26, 5, 'manage_expense', 'manage_expense', ''),
(27, 5, 'create_expense', 'create_expense', ''),
(28, 5, 'edit_expense', 'edit_expense', ''),
(29, 5, 'delete_expense', 'delete_expense', ''),
(30, 1, 'manage_category', 'manage_category', ''),
(31, 5, 'generate_invoice', 'generate_invoice', '');

-- --------------------------------------------------------

--
-- Table structure for table `persistences`
--

DROP TABLE IF EXISTS `persistences`;
CREATE TABLE IF NOT EXISTS `persistences` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED NOT NULL,
  `code` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `persistences_code_unique` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `persistences`
--

INSERT INTO `persistences` (`id`, `user_id`, `code`, `created_at`, `updated_at`) VALUES
(1, 1, '26Y0rM6qZAdwgFdhIzqUljSqSOOFNjfu', '2016-12-09 15:23:50', '2016-12-09 15:23:50');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
CREATE TABLE IF NOT EXISTS `products` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `category_id` int(10) UNSIGNED DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `brand` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `code` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `qty` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `description` text COLLATE utf8_unicode_ci,
  `notes` text COLLATE utf8_unicode_ci,
  `picture` text COLLATE utf8_unicode_ci,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `products`
--


-- --------------------------------------------------------

--
-- Table structure for table `reminders`
--

DROP TABLE IF EXISTS `reminders`;
CREATE TABLE IF NOT EXISTS `reminders` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED NOT NULL,
  `code` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `completed` tinyint(1) NOT NULL DEFAULT '0',
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `repairs`
--

DROP TABLE IF EXISTS `repairs`;
CREATE TABLE IF NOT EXISTS `repairs` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `category_id` int(10) UNSIGNED DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `problem` text COLLATE utf8_unicode_ci,
  `amount` decimal(10,2) DEFAULT NULL,
  `model` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `serial_number` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8_unicode_ci,
  `picture` text COLLATE utf8_unicode_ci,
  `email_sent` tinyint(4) NOT NULL DEFAULT '0',
  `auto_email` tinyint(4) NOT NULL DEFAULT '0',
  `sms_sent` tinyint(4) NOT NULL DEFAULT '0',
  `auto_sms` tinyint(4) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `repair_status` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `repairs`
--



-- --------------------------------------------------------

--
-- Table structure for table `repairs_categories`
--

DROP TABLE IF EXISTS `repairs_categories`;
CREATE TABLE IF NOT EXISTS `repairs_categories` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `repairs_categories`
--

INSERT INTO `repairs_categories` (`id`, `name`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Laptops', '2016-12-09 15:49:45', '2016-12-09 15:49:45', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `repair_status`
--

DROP TABLE IF EXISTS `repair_status`;
CREATE TABLE IF NOT EXISTS `repair_status` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `label` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `repair_status`
--

INSERT INTO `repair_status` (`id`, `name`, `label`) VALUES
(1, 'Pending', NULL),
(2, 'In progress', NULL),
(3, 'Fixed', NULL),
(4, 'Cancelled', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `permissions` text COLLATE utf8_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `slug`, `name`, `permissions`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'Admin', '{"repairs":true,"create_repair":true,"edit_repair":true,"delete_repair":true,"manage_category":true,"sales":true,"invoices":true,"edit_invoice":true,"add_payment":true,"custom_reports":true,"financial_overview":true,"manage_expense":true,"create_expense":true,"edit_expense":true,"delete_expense":true,"generate_invoice":true,"products":true,"create_product":true,"edit_product":true,"delete_product":true,"tickets":true,"create_ticket":true,"edit_ticket":true,"delete_ticket":true,"change_status":true,"users":true,"create_user":true,"edit_user":true,"delete_user":true,"manage_roles":true,"settings":true}', NULL, NULL),
(2, 'client', 'Client', '{}', NULL, NULL),
(3, 'staff', 'Staff', '{"repairs":true,"create_repair":true,"edit_repair":true,"delete_repair":true,"sales":true,"invoices":true,"edit_invoice":true,"add_payment":true,"custom_reports":true,"financial_overview":true,"manage_expense":true,"create_expense":true,"edit_expense":true,"delete_expense":true,"products":true,"create_product":true,"edit_product":true,"delete_product":true,"tickets":true,"create_ticket":true,"edit_ticket":true,"delete_ticket":true,"change_status":true,"users":true,"create_user":true,"edit_user":true,"delete_user":true,"manage_roles":true}', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `role_users`
--

DROP TABLE IF EXISTS `role_users`;
CREATE TABLE IF NOT EXISTS `role_users` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`user_id`,`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `role_users`
--

INSERT INTO `role_users` (`user_id`, `role_id`, `created_at`, `updated_at`) VALUES
(1, 1, '2016-12-09 15:23:31', '2016-12-09 15:23:31'),
(2, 2, '2016-12-09 15:50:23', '2016-12-09 15:50:23');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
CREATE TABLE IF NOT EXISTS `settings` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `setting_value` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_setting_key_unique` (`setting_key`)
) ENGINE=MyISAM AUTO_INCREMENT=66 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`) VALUES
(1, 'company_name', 'Repair Shop'),
(2, 'company_website', 'http://www.webstudio.co.zw'),
(3, 'system_version', '1.2'),
(4, 'sms_enabled', '1'),
(5, 'active_sms', 'clickatell'),
(6, 'new_repair_sms', 'Hello {first_name} {last_name}, we have received your order (Order #{id}).You can check progress by login to portal or using order number to check.'),
(7, 'new_repair_email', 'Hello {first_name} {last_name}, we have received your order (Order #{id}).You can check progress by login to portal <a href="{portal_address}">Here</a> or using order number to check <a href="{portal_address}">Here</a>.'),
(8, 'portal_address', 'http://www.'),
(9, 'company_email', 'info@webstudio.co.zw'),
(10, 'new_repair_subject', 'New Repair'),
(11, 'repair_update_subject', 'Repair Update'),
(12, 'request_read_receipts', '1'),
(13, 'repair_status_sms', 'Hello {first_name} {last_name}, your repair  (Order #{id}) has changed status from {old_status} to {new_status}.You can check progress by login to portal.'),
(14, 'repair_status_email', 'Hello {first_name} {last_name}, your repair  (Order #{id}) has changed status from {old_status} to {new_status}.You can check progress by login to portal <a  href="{portal_address}">Here</a>'),
(15, 'status_subject', 'Repair Status Update'),
(16, 'currency_symbol', '$'),
(17, 'currency_position', 'left'),
(18, 'default_tax', '0.00'),
(19, 'company_logo', 'logo.jpg'),
(20, 'invoice_header', 'Webstudio Pvt Ltd'),
(21, 'invoice_footer', 'Bank: CABS'),
(22, 'invoice_template', 'Hello {first_name} {last_name}, A new invoice for  your repair  (Order #{id}) has been generated.<br>\r\nInvoice Amount:${invoice_amount}<br>\r\nPayments Made:${payment_made}<br>\r\nAmount Due: ${amount_due}<br>\r\nDue On:{due_on}<br>\r\n\r\nYou can view or pay invoice by login to portal <a  href="{portal_address}">Here</a> . See attached invoice for more details'),
(23, 'invoice_subject', 'New Invoice'),
(24, 'twilio_sid', ''),
(25, 'twilio_token', ''),
(26, 'twilio_phone_number', ''),
(27, 'routesms_host', ''),
(28, 'routesms_username', ''),
(29, 'routesms_password', ''),
(30, 'routesms_port', ''),
(31, 'sms_sender', ''),
(32, 'clickatell_username', ''),
(33, 'clickatell_password', ''),
(34, 'clickatell_api_id', ''),
(35, 'paypal_email', ''),
(36, 'currency', 'USD'),
(37, 'new_ticket_client_subject', 'New Ticket Opened'),
(38, 'new_ticket_client_template', '<p> </p>\r\n<p>{companyLogo}</p>\r\n<p>Dear {clientName},</p>\r\n<p>Thank you for contacting our support team. A support ticket has now been opened for your request. You will be notified when a response is made by email.<br>Ticket {ticketRef} <br>Status: {ticketStatus}.</p>\r\n<p> </p>\r\n<p>Click on the below link to see the ticket details and post replies:\r\n<a href="{ticketLink}">View Ticket</a></p>\r\n<p>Best Wishes, <br />Webstudio Support Team<br />The Web Specialists</p>'),
(39, 'new_ticket_staff_subject', 'New Ticket Opened'),
(40, 'new_ticket_staff_template', '<p> </p>\r\n<p>{companyLogo}</p>\r\n\r\n<p>New ticket has been opened by client: {clientName}.<br>Ticket {ticketRef} <br></p>\r\n<p> </p>\r\n<p>Click on the below link to see the ticket details and post replies:\r\n<a href="{ticketLink}">View Ticket</a></p>\r\n<p>Best Wishes, <br />Webstudio Support Team<br />The Web Specialists</p>'),
(41, 'new_ticket_reply_subject', 'Ticket Response'),
(42, 'new_ticket_reply_template', '<p> </p>\r\n<p>{companyLogo}</p>\r\n\r\n<p>A new response has been added to Ticket {ticketRef} <br></p>\r\n<p>Ticket Status:{ticketStatus}</p>\r\n<p>{ticketReply}</p>\r\n<p>Click on the below link to see the ticket details and post replies: <br>\r\n<a href="{ticketLink}">View Ticket</a></p>\r\n<p>Best Wishes, <br />Webstudio Support Team<br />The Web Specialists</p>'),
(43, 'ticket_status_subject', 'Ticket Status Update'),
(44, 'ticket_status_template', '<p> </p>\r\n<p>{companyLogo}</p>\r\n<p>Dear {clientName},</p>\r\n<p>Your Ticket {ticketRef} has been updated  <br>New Status: {ticketStatus}.</p>\r\n<p> </p>\r\n<p>Click on the below link to see the ticket details and post replies:\r\n<a href="{ticketLink}">View Ticket</a></p>\r\n<p>Best Wishes, <br />Webstudio Support Team<br />The Web Specialists</p>\r\n'),
(45, 'reparation_footer', 'This proves that you have left your device listed above'),
(46, 'password_reset_subject', 'Password reset instructions'),
(47, 'password_reset_template', 'Password reset instructions'),
(56, 'company_address', '<p><span class="fontstyle0">Office 608, 6th Floor<br />Batanai Gardens<br />Cnr Jason Moyo & 1st Street<br />Harare</span> </p>'),
(57, 'invoice_payment_subject', 'Invoice Payment Confirmation'),
(58, 'invoice_payment_template', '<p> </p>\r\n<p>{companyLogo}</p>\r\n<p>Dear {clientName},</p>\r\n<p>This is a payment receipt for Invoice {invoiceRef} sent on {invoiceDate}.</p>\r\n<p>Amount: {invoiceAmount}<br />Transaction: {transaction}<br />Total Paid: {totalPaid}<br />Remaining Balance: {invoiceAmountDue}<br />Status: {invoiceStatus}</p>\r\n<p> </p>\r\n<p>You may review your invoice history at any time by logging in to your client area here {portalAddress}.</p>\r\n<p>Note: This email will serve as an official receipt for this payment.</p>\r\n<p>Best Wishes, <br />Webstudio Support Team<br />The Web Specialists</p>\r\n<table id="templateFooter" border="0" width="100%" cellspacing="0" cellpadding="0">\r\n<tbody>\r\n<tr>\r\n<td class="footerContent" valign="top"><a href="http://webstudio.co.zw/">visit our website</a> <span class="hide-mobile">| </span><a href="http://clients.webstudio.co.zw/">log in to your account</a> <span class="hide-mobile">| </span><a href="http://clients.webstudio.co.zw/submitticket.php">get support</a> <br />Copyright © Webstudio Pvt Ltd, All rights reserved.</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<center></center>'),
(59, 'invoice_overdue_subject', 'Invoice Overdue Notice'),
(60, 'invoice_overdue_template', '<p> </p>\r\n<p>{companyLogo}</p>\r\n<p>Dear {clientName},</p>\r\n<p>This is a billing notice that your invoice no.{invoiceRef} which was generated on {invoiceDate} is now overdue.</p>\r\n<p>Invoice {invoiceRef}<br />Amount Due: {invoiceAmountDue}<br />Due Date: {invoiceDueDate}</p>\r\n<p> </p>\r\n<p>You can login to your client area to view and pay the invoice at {portalAddress}</p>\r\n<p>Best Wishes, <br />Webstudio Support Team<br />The Web Specialists</p>\r\n<table id="templateFooter" border="0" width="100%" cellspacing="0" cellpadding="0">\r\n<tbody>\r\n<tr>\r\n<td class="footerContent" valign="top"><a href="http://webstudio.co.zw/">visit our website</a> <span class="hide-mobile">| </span><a href="http://clients.webstudio.co.zw/">log in to your account</a> <span class="hide-mobile">| </span><a href="http://clients.webstudio.co.zw/submitticket.php">get support</a> <br />Copyright © Webstudio Pvt Ltd, All rights reserved.</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<center></center>'),
(61, 'invoice_reminder_subject', 'Invoice Payment Reminder'),
(62, 'invoice_reminder_template', '<p> </p>\r\n<p>{companyLogo}</p>\r\n<p>Dear {clientName},</p>\r\n<p>This is a friendly reminder to pay your invoice of no.{invoiceRef} which was generated on {invoiceDate}.</p>\r\n<p>Invoice {invoiceRef}<br />Amount Due: {invoiceAmountDue}<br />Due Date: {invoiceDueDate}</p>\r\n<p> </p>\r\n<p>You can login to your client area to view and pay the invoice at {portalAddress}</p>\r\n<p>Best Wishes, <br />Webstudio Support Team<br />The Web Specialists</p>\r\n<table id="templateFooter" border="0" width="100%" cellspacing="0" cellpadding="0">\r\n<tbody>\r\n<tr>\r\n<td class="footerContent" valign="top"><a href="http://webstudio.co.zw/">visit our website</a> <span class="hide-mobile">| </span><a href="http://clients.webstudio.co.zw/">log in to your account</a> <span class="hide-mobile">| </span><a href="http://clients.webstudio.co.zw/submitticket.php">get support</a> <br />Copyright © Webstudio Pvt Ltd, All rights reserved.</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<center></center>'),
(63, 'invoice_due_after', '10'),
(64, 'notify_payment', '1'),
(65, 'admin_payment_template', '<p>&nbsp;</p>\n<p>{companyLogo}</p>\n<p>This is to let you know that &nbsp;a payment for Invoice {invoiceRef} sent on {invoiceDate} has been received from {clientName}.</p>\n<p>Amount: {invoiceAmount}<br />Transaction: {transaction}<br />Total Paid: {totalPaid}<br />Remaining Balance: {invoiceAmountDue}<br />Status: {invoiceStatus}</p>\n<p>&nbsp;</p>\n\n<p>Best Wishes, <br />Webstudio Support Team<br />The Web Specialists</p>\n<table id="templateFooter" border="0" width="100%" cellspacing="0" cellpadding="0">\n<tbody>\n<tr>\n<td class="footerContent" valign="top"><a href="http://webstudio.co.zw/">visit our website</a> <span class="hide-mobile">| </span><a href="http://clients.webstudio.co.zw/">log in to your account</a> <span class="hide-mobile">| </span><a href="http://clients.webstudio.co.zw/submitticket.php">get support</a> <br />Copyright &copy; Webstudio Pvt Ltd, All rights reserved.</td>\n</tr>\n</tbody>\n</table>\n<center></center>');

-- --------------------------------------------------------

--
-- Table structure for table `sms`
--

DROP TABLE IF EXISTS `sms`;
CREATE TABLE IF NOT EXISTS `sms` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED NOT NULL,
  `message` text COLLATE utf8_unicode_ci,
  `recipients` int(10) UNSIGNED NOT NULL,
  `send_to` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

DROP TABLE IF EXISTS `tasks`;
CREATE TABLE IF NOT EXISTS `tasks` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED NOT NULL,
  `repair_id` int(10) UNSIGNED NOT NULL,
  `title` text COLLATE utf8_unicode_ci,
  `notes` text COLLATE utf8_unicode_ci,
  `file` text COLLATE utf8_unicode_ci,
  `task_start_date` date NOT NULL,
  `task_end_date` date NOT NULL,
  `timer_status` enum('on','off') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'off',
  `task_progress` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `task_status` tinyint(4) NOT NULL DEFAULT '0',
  `modified_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tax`
--

DROP TABLE IF EXISTS `tax`;
CREATE TABLE IF NOT EXISTS `tax` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `title` text COLLATE utf8_unicode_ci,
  `percentage` decimal(10,2) NOT NULL,
  `notes` text COLLATE utf8_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `tax`
--



-- --------------------------------------------------------

--
-- Table structure for table `throttle`
--

DROP TABLE IF EXISTS `throttle`;
CREATE TABLE IF NOT EXISTS `throttle` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ip` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `throttle_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tickets`
--

DROP TABLE IF EXISTS `tickets`;
CREATE TABLE IF NOT EXISTS `tickets` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `department_id` int(11) DEFAULT NULL,
  `reference_no` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `subject` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `body` text COLLATE utf8_unicode_ci,
  `status` enum('answered','in_progress','closed','open') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'open',
  `priority` enum('low','medium','high') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'low',
  `attachment` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ticket_replies`
--

DROP TABLE IF EXISTS `ticket_replies`;
CREATE TABLE IF NOT EXISTS `ticket_replies` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `body` text COLLATE utf8_unicode_ci,
  `attachment` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `permissions` text COLLATE utf8_unicode_ci,
  `last_login` timestamp NULL DEFAULT NULL,
  `first_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `last_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8_unicode_ci,
  `phone` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `gender` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `permissions`, `last_login`, `first_name`, `last_name`, `address`, `phone`, `city`, `gender`, `notes`, `created_at`, `updated_at`) VALUES
(1, 'admin@webstudio.co.zw', '$2y$10$l7HoCsgFMj20ZyR9IbhrbeUVJpoDb8Pry0q0rSxOE/tihHuukrnki', NULL, '2016-12-09 15:23:50', 'Admin', 'Admin', NULL, NULL, NULL, NULL, NULL, '2016-12-09 15:23:31', '2016-12-09 15:23:50'),



