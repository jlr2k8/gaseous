SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';


CREATE TABLE `changesets` (
    `uid` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
    `filename` text COLLATE utf8mb4_unicode_ci NOT NULL,
    `processed_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `archived` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
    `created_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `modified_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `archived_datetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
    UNIQUE KEY `uid` (`uid`,`archived_datetime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TRIGGER `changeset_uuid` BEFORE INSERT ON `changesets` FOR EACH ROW
BEGIN
    IF new.uid IS NULL THEN
        SET new.uid = UUID();
    END IF;
END;


CREATE TABLE `account` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lastname` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `archived` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `created_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `archived_datetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`,`archived_datetime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `account_password` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `archived` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `created_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `archived_datetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `account_username_archived_datetime` (`account_username`,`archived_datetime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `account_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `archived` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `created_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `archived_datetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `account_username_role_name_archived_datetime` (`account_username`,`role_name`,`archived_datetime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `archived` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `created_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `archived_datetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `category` (`category`,`archived_datetime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `css_iteration` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `css` text COLLATE utf8mb4_unicode_ci,
  `author` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_selected` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `archived` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `created_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `archived_datetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TRIGGER `before_insert_css` BEFORE INSERT ON `css_iteration` FOR EACH ROW
BEGIN
    IF new.uid IS NULL THEN
        SET new.uid = UUID();
    END IF;
END;


CREATE TABLE `js_iteration` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `js` text COLLATE utf8mb4_unicode_ci,
  `author` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_selected` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `archived` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `created_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `archived_datetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TRIGGER `before_insert_js` BEFORE INSERT ON `js_iteration` FOR EACH ROW
BEGIN
    IF new.uid IS NULL THEN
        SET new.uid = UUID();
    END IF;
END;


CREATE TABLE `current_page_iteration` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_master_uid` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `page_iteration_uid` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `archived` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `created_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `archived_datetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `current_page_iteration` (`page_master_uid`, `page_iteration_uid`) VALUES
('faea0c4f-9cd0-11e9-a1ac-0242ac190005','289e6afa10a5030d5f15b2aea8c10bce4402341c8de3ac7723f23b28f76365e4326bc1fdfc6eebab577ddb0eb740d1bf8280d1a869f3236f651bdcab647e33ce');

CREATE TABLE `login_session` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `uid` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `archived` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `created_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `archived_datetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `page` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_master_uid` varchar(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `uri_uid` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `archived` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `created_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `archived_datetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `page` (`page_master_uid`, `uri_uid`) VALUES
('faea0c4f-9cd0-11e9-a1ac-0242ac190005','ac41cf58-e82f-11e8-b856-0242ac120005');

CREATE TRIGGER `before_insert_page` BEFORE INSERT ON `page` FOR EACH ROW
IF new.page_master_uid IS NULL THEN
    SET new.page_master_uid = UUID();
END IF;


CREATE TABLE `page_iteration` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `page_title_seo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `page_title_h1` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `meta_desc` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `meta_robots` enum('index,follow','noindex,nofollow') COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `include_in_sitemap` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL,
  `archived` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `created_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `archived_datetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `page_iteration` (`uid`, `status`) VALUES
('289e6afa10a5030d5f15b2aea8c10bce4402341c8de3ac7723f23b28f76365e4326bc1fdfc6eebab577ddb0eb740d1bf8280d1a869f3236f651bdcab647e33ce','active');


CREATE TABLE `page_iteration_commits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_master_uid` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `page_iteration_uid` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `author` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `iteration_description` text COLLATE utf8mb4_unicode_ci,
  `archived` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `created_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `archived_datetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `page_iteration_uid_archived_datetime` (`page_iteration_uid`,`archived_datetime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `page_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_iteration_uid` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `archived` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `created_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `archived_datetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `page_iteration_uid_role_name_archived_datetime` (`page_iteration_uid`,`role_name`,`archived_datetime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `property` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `property` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `archived` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `created_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `archived_datetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `property` (`property`,`archived_datetime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `property` (`id`, `property`, `description`, `archived`, `created_datetime`, `modified_datetime`, `archived_datetime`) VALUES
(1,	'boolean',	'True or false',	'0',	'2018-06-21 04:49:00',	'2018-06-21 04:49:00',	'0000-00-00 00:00:00'),
(2,	'ckeditor',	'Uses CK Editor to manage value',	'0',	'2018-06-21 04:49:19',	'2018-06-24 22:25:57',	'0000-00-00 00:00:00'),
(4,	'codemirror',	'Uses CodeMirror to manage value',	'0',	'2018-06-24 22:26:09',	'2018-06-24 22:26:09',	'0000-00-00 00:00:00');


CREATE TABLE `role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `archived` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `created_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `archived_datetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `role_name_archived_datetime` (`role_name`,`archived_datetime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category_key` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role_based` enum('false','true') COLLATE utf8mb4_unicode_ci DEFAULT 'false',
  `description` text COLLATE utf8mb4_unicode_ci,
  `archived` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `created_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `archived_datetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `key` (`key`,`archived_datetime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `settings` (`id`, `key`, `display`, `category_key`, `role_based`, `description`, `archived`, `created_datetime`, `modified_datetime`, `archived_datetime`) VALUES
(1,	'web_url',	'Web URL',	'administrative',	'false',	'',	'0',	'2017-10-21 03:43:54',	'2018-06-25 03:53:31',	'0000-00-00 00:00:00'),
(2,	'cookie_domain',	'Cookie domain',	'cookie',	'false',	'',	'0',	'2017-10-21 03:43:54',	'2018-06-25 03:51:02',	'0000-00-00 00:00:00'),
(3,	'enable_template_caching',	'Enable template caching',	'administrative',	'false',	'',	'0',	'2017-10-21 03:43:54',	'2018-06-25 03:53:20',	'0000-00-00 00:00:00'),
(4,	'enable_ssl',	'Enable SSL',	'administrative',	'false',	'',	'0',	'2017-10-21 03:43:54',	'2018-06-25 03:53:13',	'0000-00-00 00:00:00'),
(5,	'recaptcha_public_key',	'ReCaptcha public key',	'recaptcha',	'false',	'',	'0',	'2017-10-21 03:43:54',	'2018-06-25 03:51:52',	'0000-00-00 00:00:00'),
(6,	'recaptcha_private_key',	'ReCaptcha private key',	'recaptcha',	'false',	'',	'0',	'2017-10-21 03:43:54',	'2018-06-25 03:51:59',	'0000-00-00 00:00:00'),
(7,	'show_debug',	'Show debug footer',	'development',	'true',	'',	'0',	'2017-10-21 03:43:54',	'2018-06-25 18:26:55',	'0000-00-00 00:00:00'),
(8,	'maintenance_mode',	'Maintenance mode',	'administrative',	'false',	'',	'0',	'2017-10-21 03:43:54',	'2018-06-25 03:53:50',	'0000-00-00 00:00:00'),
(9,	'require_recaptcha',	'Require ReCaptcha',	'recaptcha',	'false',	'',	'0',	'2017-10-21 03:43:54',	'2018-06-25 03:52:24',	'0000-00-00 00:00:00'),
(10,	'login_cookie_expire_days',	'Expiration for login cookies',	'cookie',	'false',	'',	'0',	'2017-10-21 03:43:54',	'2018-06-25 03:51:12',	'0000-00-00 00:00:00'),
(11,	'smtp_host',	'SMTP server hostname/IP address',	'email',	'false',	'',	'0',	'2017-10-21 03:43:54',	'2018-06-18 21:13:29',	'0000-00-00 00:00:00'),
(12,	'smtp_port',	'SMTP host\'s port',	'email',	'false',	'',	'0',	'2017-10-21 03:43:54',	'2018-06-18 21:13:29',	'0000-00-00 00:00:00'),
(13,	'smtp_user',	'SMTP user',	'email',	'false',	'',	'0',	'2017-10-21 03:43:54',	'2018-06-18 21:13:29',	'0000-00-00 00:00:00'),
(14,	'smtp_password',	'SMTP password',	'email',	'false',	'',	'0',	'2017-10-21 03:43:54',	'2018-06-18 21:13:29',	'0000-00-00 00:00:00'),
(19,	'dev_alert_email',	'Bug and code-level log email',	'development',	'false',	'',	'0',	'2017-10-21 03:43:54',	'2018-06-25 03:55:32',	'0000-00-00 00:00:00'),
(20,	'php_error_reporting_level',	'PHP Error Reporting level',	'development',	'false',	'http://php.net/manual/en/function.error-reporting.php',	'0',	'2017-11-12 21:37:50',	'2018-06-25 03:55:39',	'0000-00-00 00:00:00'),
(21,	'registration_access_code',	'Registration access code',	'administrative',	'false',	'',	'0',	'2018-06-09 17:23:39',	'2018-06-25 03:54:07',	'0000-00-00 00:00:00'),
(22,	'main_template',	'Main page template',	'templates',	'false',	'The global, site-wide template used for every page',	'0',	'2018-06-10 17:56:56',	'2018-06-24 22:32:05',	'0000-00-00 00:00:00'),
(24,	'http_error_template',	'HTTP error page template',	'templates',	'false',	'HTTP Error page content',	'0',	'2018-06-10 18:30:23',	'2018-06-24 22:32:22',	'0000-00-00 00:00:00'),
(25,	'archive_users',	'Archive users',	'administrative',	'true',	'Ability to archive users',	'0',	'2018-06-13 04:36:59',	'2018-06-25 18:26:55',	'0000-00-00 00:00:00'),
(26,	'edit_users',	'Edit users',	'administrative',	'true',	'Ability to edit users',	'0',	'2018-06-13 05:07:42',	'2018-06-25 18:26:55',	'0000-00-00 00:00:00'),
(27,	'archive_roles',	'Archive roles',	'administrative',	'true',	'Ability to archive roles',	'0',	'2018-06-18 03:53:56',	'2018-06-25 18:26:55',	'0000-00-00 00:00:00'),
(28,	'edit_roles',	'Edit roles',	'administrative',	'true',	'Ability to edit roles',	'0',	'2018-06-18 03:54:08',	'2018-06-25 18:26:55',	'0000-00-00 00:00:00'),
(29,	'add_roles',	'Add roles',	'administrative',	'true',	'Ability to add roles',	'0',	'2018-06-18 03:54:28',	'2018-06-25 18:26:55',	'0000-00-00 00:00:00'),
(30,	'edit_settings',	'Edit settings',	'administrative',	'true',	'Ability to modify configuration',	'0',	'2018-06-18 19:18:31',	'2018-07-02 02:04:18',	'0000-00-00 00:00:00'),
(31,	'debug_pdo',	'Debug PDO',	'development',	'false',	'Allows output of PDO debug. Sets PDO::ATTR_ERRMODE attribute (3) to value of PDO::ERRMODE_EXCEPTION (2)',	'0',	'2018-07-06 22:53:55',	'2018-07-06 22:53:55',	'0000-00-00 00:00:00'),
(32,	'add_pages',	'Add pages',	'cms',	'true',	'Ability to add CNS pages to the application',	'0',	'2018-08-19 19:16:45',	'2018-08-19 19:23:46',	'0000-00-00 00:00:00'),
(33,	'edit_pages',	'Edit pages',	'cms',	'true',	'Ability to modify existing CMS pages',	'0',	'2018-08-19 19:17:13',	'2018-08-19 19:23:46',	'0000-00-00 00:00:00'),
(34,	'archive_pages',	'Archive pages',	'cms',	'true',	'Ability to archive (effectively remove) CMS pages from the site. This is not a delete role.',	'0',	'2018-08-19 19:17:56',	'2018-08-19 19:23:46',	'0000-00-00 00:00:00'),
(35,	'upload_root',	'Upload root',	'cms',	'false',	'Filesystem directory for uploaded/pasted CMS images',	'0',	'2018-09-21 01:45:58',	'2018-09-21 02:22:00',	'0000-00-00 00:00:00'),
(36,	'upload_url_relative',	'Upload URL relative path (relative to site URL)',	'cms',	'false',	'Relative path (client-facing/browser) for images.',	'0',	'2018-09-21 01:46:33',	'2018-09-21 02:21:52',	'0000-00-00 00:00:00'),
(37,	'manage_css',	'Manage CSS',	'administrative',	'true',	'Allow site-wide management of custom CSS',	'0',	'2018-10-21 19:22:15',	'2018-10-21 19:24:13',	'0000-00-00 00:00:00'),
(38,	'manage_js',	'Manage JS',	'administrative',	'true',	'Allow site-wide management of custom JS',	'0',	'2018-10-21 19:22:15',	'2018-10-21 19:24:13',	'0000-00-00 00:00:00'),
(39,	'robots_txt_value',	'robots.txt value',	'administrative',	'false',	'The site\'s top level /robots.txt output',	'0',	'2018-11-03 19:16:41',	'2018-11-03 19:23:02',	'0000-00-00 00:00:00'),
(40,	'nav_template',	'Nav Template',	'templates',	'false',	'Template for navigation <nav /> area',	'0',	'2018-11-06 15:47:45',	'2018-11-06 15:47:45',	'0000-00-00 00:00:00'),
(41,	'footer_template',	'Footer Template',	'templates',	'false',	'Template for <footer /> region',	'0',	'2018-11-06 15:49:05',	'2018-11-06 15:49:05',	'0000-00-00 00:00:00'),
(42,	'add_redirects',	'Add Redirects',	'administrative',	'true',	'Ability to add URI redirects',	'0',	'2019-05-18 18:37:41',	'2019-05-18 18:41:53',	'0000-00-00 00:00:00'),
(43,	'edit_redirects',	'Edit Redirects',	'administrative',	'true',	'Ability to edit URI redirects',	'0',	'2019-05-18 18:38:10',	'2019-05-18 18:41:58',	'0000-00-00 00:00:00'),
(44,	'archive_redirects',	'Archive Redirects',	'administrative',	'true',	'Ability to archive URI redirects',	'0',	'2019-05-18 18:38:33',	'2019-05-18 18:42:04',	'0000-00-00 00:00:00'),
(45,	'log_file',	'Log File',	'administrative',	'false',	'Location of the system\'s log file. Use {{today}} as a variable in the filename. e.g. log-{{today}}.log will render a log file as log-2001-01-01.log on January 1st of 2001.',	'0',	'2019-05-19 22:12:37',	'2019-05-19 22:12:37',	'0000-00-00 00:00:00'),
(46,	'add_routes',	'Add Routes',	'administrative',	'true',	'Ability to add URI routes',	'0',	'2019-05-19 22:49:39',	'2019-05-19 22:49:39',	'0000-00-00 00:00:00'),
(47,	'edit_routes',	'Edit Routes',	'administrative',	'true',	'Ability to edit URI routes',	'0',	'2019-05-19 22:49:57',	'2019-05-19 22:49:57',	'0000-00-00 00:00:00'),
(48,	'archive_routes',	'Archive Routes',	'administrative',	'true',	'Ability to archive URI routes',	'0',	'2019-05-19 22:50:18',	'2019-05-19 22:50:18',	'0000-00-00 00:00:00');


CREATE TABLE `settings_properties` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `settings_key` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `property` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `archived` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `created_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `archived_datetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `config_key__config_property` (`settings_key`,`property`,`archived_datetime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `settings_properties` (`id`, `settings_key`, `property`, `archived`, `created_datetime`, `modified_datetime`, `archived_datetime`) VALUES
(1,	'main_template',	'codemirror',	'0',	'2018-06-21 05:25:46',	'2018-06-24 22:28:46',	'0000-00-00 00:00:00'),
(4,	'http_error_template',	'codemirror',	'0',	'2018-06-21 05:25:46',	'2018-06-24 22:29:11',	'0000-00-00 00:00:00'),
(5,	'enable_template_caching',	'boolean',	'0',	'2018-06-25 04:24:04',	'2018-06-25 04:24:04',	'0000-00-00 00:00:00'),
(6,	'enable_ssl',	'boolean',	'0',	'2018-06-25 04:24:22',	'2018-06-25 04:24:22',	'0000-00-00 00:00:00'),
(7,	'show_debug',	'boolean',	'0',	'2018-06-25 04:24:43',	'2018-06-25 04:24:43',	'0000-00-00 00:00:00'),
(8,	'maintenance_mode',	'boolean',	'0',	'2018-06-25 04:24:59',	'2018-06-25 04:24:59',	'0000-00-00 00:00:00'),
(9,	'archive_users',	'boolean',	'0',	'2018-06-25 04:25:25',	'2018-06-25 04:25:25',	'0000-00-00 00:00:00'),
(10,	'edit_users',	'boolean',	'0',	'2018-06-25 04:25:53',	'2018-06-25 04:25:53',	'0000-00-00 00:00:00'),
(11,	'archive_roles',	'boolean',	'0',	'2018-06-25 04:26:07',	'2018-06-25 04:26:07',	'0000-00-00 00:00:00'),
(12,	'edit_roles',	'boolean',	'0',	'2018-06-25 04:26:16',	'2018-06-25 04:26:16',	'0000-00-00 00:00:00'),
(13,	'add_roles',	'boolean',	'0',	'2018-06-25 04:26:35',	'2018-06-25 04:26:35',	'0000-00-00 00:00:00'),
(14,	'edit_settings',	'boolean',	'0',	'2018-06-25 04:26:45',	'2018-07-06 21:57:55',	'0000-00-00 00:00:00'),
(15,	'require_recaptcha',	'boolean',	'0',	'2018-07-04 22:51:02',	'2018-07-04 22:51:02',	'0000-00-00 00:00:00'),
(16,	'pdo_debug',	'boolean',	'0',	'2018-07-06 22:54:50',	'2018-07-06 22:54:50',	'0000-00-00 00:00:00'),
(17,	'add_pages',	'boolean',	'0',	'2018-08-19 19:21:58',	'2018-08-19 19:21:58',	'0000-00-00 00:00:00'),
(18,	'edit_pages',	'boolean',	'0',	'2018-08-19 19:22:07',	'2018-08-19 19:22:07',	'0000-00-00 00:00:00'),
(19,	'archive_pages',	'boolean',	'0',	'2018-08-19 19:22:18',	'2018-08-19 19:22:18',	'0000-00-00 00:00:00'),
(20,	'manage_css',	'boolean',	'0',	'2018-10-21 19:22:42',	'2018-10-21 19:22:42',	'0000-00-00 00:00:00'),
(21,	'robots_txt_value',	'codemirror',	'0',	'2018-11-03 19:17:05',	'2018-11-03 19:22:48',	'0000-00-00 00:00:00'),
(23,	'nav_template',	'codemirror',	'0',	'2018-11-06 15:49:30',	'2018-11-06 15:49:48',	'0000-00-00 00:00:00'),
(24,	'footer_template',	'codemirror',	'0',	'2018-11-06 15:49:30',	'2018-11-06 15:49:48',	'0000-00-00 00:00:00'),
(25,	'add_redirects',	'boolean',	'0',	'2019-05-18 18:41:09',	'2019-05-18 18:41:09',	'0000-00-00 00:00:00'),
(26,	'edit_redirects',	'boolean',	'0',	'2019-05-18 18:43:10',	'2019-05-18 18:43:10',	'0000-00-00 00:00:00'),
(27,	'archive_redirects',	'boolean',	'0',	'2019-05-18 18:43:20',	'2019-05-18 18:43:20',	'0000-00-00 00:00:00'),
(28,	'add_routes',	'boolean',	'0',	'2019-05-19 22:50:33',	'2019-05-19 22:50:33',	'0000-00-00 00:00:00'),
(29,	'edit_routes',	'boolean',	'0',	'2019-05-19 22:50:39',	'2019-05-19 22:50:39',	'0000-00-00 00:00:00'),
(30,	'archive_routes',	'boolean',	'0',	'2019-05-19 22:50:46',	'2019-05-19 22:50:46',	'0000-00-00 00:00:00'),
(31,	'manage_js',	'boolean',	'0',	'2018-10-21 19:22:42',	'2018-10-21 19:22:42',	'0000-00-00 00:00:00');


CREATE TABLE `settings_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `settings_key` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `archived` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `created_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `archived_datetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `config_key_role_name_archived_datetime` (`settings_key`,`role_name`,`archived_datetime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `settings_values` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `settings_key` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `archived` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `created_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `archived_datetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_key_archived_datetime` (`settings_key`,`archived_datetime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `token_email` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `token` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `enable_safe_mode` enum('false','true') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'false',
  `archived` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `created_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `archived_datetime` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `uri` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `uri` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `archived` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `created_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `archived_datetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uid_archived_datetime` (`uid`,`archived_datetime`),
  UNIQUE KEY `uri_archived_datetime` (`uri`(100),`archived_datetime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `uri` (`id`, `uid`, `uri`, `archived`, `created_datetime`, `modified_datetime`, `archived_datetime`) VALUES
(210,	'ac41cf58-e82f-11e8-b856-0242ac120005',	'/home',	'0',	'2018-11-14 17:06:48',	'2019-05-12 15:47:48',	'0000-00-00 00:00:00');

CREATE TRIGGER `before_insert_uri` BEFORE INSERT ON `uri` FOR EACH ROW
BEGIN
    IF new.uid IS NULL THEN
        SET new.uid = UUID();
    END IF;
END;


CREATE TABLE `uri_redirects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uri_uid` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `destination_url` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `http_status_code` int(3) NOT NULL DEFAULT '301',
  `description` text COLLATE utf8mb4_unicode_ci,
  `archived` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `created_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `archived_datetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uri_uid` (`uri_uid`,`archived_datetime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `uri_routes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` varchar(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `regex_pattern` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `destination_controller` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `priority_order` int(11) NOT NULL,
  `archived` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `created_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `archived_datetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `regex_pattern_archived_datetime` (`regex_pattern`(100),`archived_datetime`),
  UNIQUE KEY `uid_archived_datetime` (`uid`,`archived_datetime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `uri_routes` (`id`, `uid`, `regex_pattern`, `destination_controller`, `description`, `priority_order`, `archived`, `created_datetime`, `modified_datetime`, `archived_datetime`) VALUES
(689,	'0bee2c78-7a8e-11e9-8141-0242ac120005',	'/register/?',	'controllers/user/register.php',	'',	0,	'0',	'2019-05-26 14:12:13',	'2019-05-26 14:12:13',	'0000-00-00 00:00:00'),
(690,	'fd7b16b5-7a8d-11e9-8141-0242ac120005',	'/login/?',	'controllers/user/login.php',	'',	1,	'0',	'2019-05-26 14:12:13',	'2019-05-26 14:12:13',	'0000-00-00 00:00:00'),
(691,	'ffb9f9fc-7a8d-11e9-8141-0242ac120005',	'/admin/settings/?',	'controllers/admin/settings.php',	'',	2,	'0',	'2019-05-26 14:12:13',	'2019-05-26 14:12:13',	'0000-00-00 00:00:00'),
(692,	'0ee45c7c-7653-11e9-8141-0242ac120005',	'/sitemap.xml',	'controllers/services/sitemap_output.php',	'',	3,	'0',	'2019-05-26 14:12:13',	'2019-05-26 14:12:13',	'0000-00-00 00:00:00'),
(693,	'2041287d-7653-11e9-8141-0242ac120005',	'/styles.gz.css',	'controllers/services/css_output.php',	'',	4,	'0',	'2019-05-26 14:12:13',	'2019-05-26 14:12:13',	'0000-00-00 00:00:00'),
(694,	'2e7c4569-7653-11e9-8141-0242ac120005',	'/js.gz.js',	'controllers/services/js_output.php',	'',	5,	'0',	'2019-05-26 14:12:13',	'2019-05-26 14:12:13',	'0000-00-00 00:00:00'),
(695,	'3ad40bda-7653-11e9-8141-0242ac120005',	'/robots.txt',	'controllers/services/robots.txt.php',	'',	6,	'0',	'2019-05-26 14:12:13',	'2019-05-26 14:12:13',	'0000-00-00 00:00:00'),
(696,	'04f247bf-7a8e-11e9-8141-0242ac120005',	'/css-preview-check/?',	'controllers/services/css_preview_check.php',	'',	7,	'0',	'2019-05-26 14:12:13',	'2019-05-26 14:12:13',	'0000-00-00 00:00:00'),
(697,	'074ead26-7a8e-11e9-8141-0242ac120005',	'/js-preview-check/?',	'controllers/services/js_preview_check.php',	'',	8,	'0',	'2019-05-26 14:12:13',	'2019-05-26 14:12:13',	'0000-00-00 00:00:00'),
(698,	'0c6b51b9-7a8e-11e9-8141-0242ac120005',	'/logout/?',	'controllers/user/logout.php',	'',	9,	'0',	'2019-05-26 14:12:13',	'2019-05-26 14:12:13',	'0000-00-00 00:00:00'),
(699,	'6988ef66-7a91-11e9-8141-0242ac120005',	'/img/(.*)',	'controllers/services/images.php?src=$1',	'Images rendered with headers and compression...',	10,	'0',	'2019-05-26 14:12:13',	'2019-05-26 14:12:13',	'0000-00-00 00:00:00'),
(700,	'fd0f636c-7a8d-11e9-8141-0242ac120005',	'/register/([\\w]+)/?',	'controllers/user/register.php?access_code=$1',	'',	11,	'0',	'2019-05-26 14:12:13',	'2019-05-26 14:12:13',	'0000-00-00 00:00:00'),
(701,	'fdf9b64c-7a8d-11e9-8141-0242ac120005',	'/admin/?',	'controllers/admin/index.php',	'',	12,	'0',	'2019-05-26 14:12:13',	'2019-05-26 14:12:13',	'0000-00-00 00:00:00'),
(702,	'00b7ee46-7a8e-11e9-8141-0242ac120005',	'/admin/css/?',	'controllers/admin/css.php',	'',	13,	'0',	'2019-05-26 14:12:13',	'2019-05-26 14:12:13',	'0000-00-00 00:00:00'),
(703,	'fe7bc799-7a8d-11e9-8141-0242ac120005',	'/admin/roles/?',	'controllers/admin/roles.php',	'',	14,	'0',	'2019-05-26 14:12:13',	'2019-05-26 14:12:13',	'0000-00-00 00:00:00'),
(705,	'0ac885ff-7a8e-11e9-8141-0242ac120005',	'/admin/routes/?',	'controllers/admin/routes.php',	'',	16,	'0',	'2019-05-26 14:12:13',	'2019-05-26 14:12:13',	'0000-00-00 00:00:00'),
(706,	'000f4947-7a8e-11e9-8141-0242ac120005',	'/admin/pages/?',	'controllers/admin/pages.php',	'',	17,	'0',	'2019-05-26 14:12:13',	'2019-05-26 14:12:13',	'0000-00-00 00:00:00'),
(707,	'fef61358-7a8d-11e9-8141-0242ac120005',	'/admin/users/?',	'controllers/admin/users.php',	'',	18,	'0',	'2019-05-26 14:12:13',	'2019-05-26 14:12:13',	'0000-00-00 00:00:00'),
(709,	'dc66798a-7a8d-11e9-8141-0242ac120005',	'/admin/redirects/?',	'controllers/admin/redirects.php',	'',	20,	'0',	'2019-05-26 14:12:13',	'2019-05-26 14:12:13',	'0000-00-00 00:00:00'),
(710,	'da9605dc-7c44-11e9-8141-0242ac120005',	'/([\\w\\/\\-]+(\\.html)?)?',	'controllers/cms/index.php?page=$1',	'CMS pages',	21,	'0',	'2019-05-26 14:12:13',	'2019-05-26 14:12:13',	'0000-00-00 00:00:00');

CREATE TRIGGER `before_insert_uri_routes` BEFORE INSERT ON `uri_routes` FOR EACH ROW
BEGIN
    IF new.uid IS NULL THEN
        SET new.uid = UUID();
    END IF;
END;