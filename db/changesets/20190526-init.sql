SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

CREATE DATABASE `gaseous-dev` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `gaseous-dev`;

CREATE TABLE `account` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `lastname` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `username` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `archived` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `archived_datetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`,`archived_datetime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `account` (`id`, `firstname`, `lastname`, `username`, `email`, `archived`, `created`, `modified`, `archived_datetime`) VALUES
(20,	'josh',	'rogers',	'josh',	'mail@joshlrogers.com',	'0',	'2018-06-15 16:32:04',	'2018-06-18 19:09:20',	'0000-00-00 00:00:00'),
(21,	'scooby',	'rogers',	'scooby',	'jlr2k8@gmail.com',	'1',	'2018-10-14 17:15:05',	'2018-10-14 17:31:36',	'2018-10-14 17:31:36'),
(22,	'John',	'Doe',	'jdoe',	'mail@jrog.io',	'0',	'2018-11-04 19:05:49',	'2018-11-04 19:05:49',	'0000-00-00 00:00:00');

CREATE TABLE `account_password` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_username` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `password` text COLLATE utf8_unicode_ci NOT NULL,
  `archived` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `archived_datetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `account_username_archived_datetime` (`account_username`,`archived_datetime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `account_password` (`id`, `account_username`, `password`, `archived`, `created`, `modified`, `archived_datetime`) VALUES
(5,	'josh',	'$2y$10$ubPvUhXxDemO5AjYN8Y/HuuMZkxgennHiaqb3YuCvWALsdJQbP57y',	'0',	'2018-06-15 16:32:04',	'2018-10-12 16:03:35',	'0000-00-00 00:00:00'),
(6,	'scooby',	'$2y$10$hZlp/iVRkAJErmUdWTORseq7RlNrWj3mFRB2Ly4.pcEWTeLRVXYEO',	'0',	'2018-10-14 17:15:05',	'2018-10-14 17:15:05',	'0000-00-00 00:00:00'),
(7,	'jdoe',	'$2y$10$0kKzDPys6Swaem.x/8ZWyeyFm8USQx3tQkJwiZfEWTbHuKQWM/PCm',	'0',	'2018-11-04 19:05:49',	'2018-11-04 19:05:49',	'0000-00-00 00:00:00');

CREATE TABLE `account_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_username` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `role_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `archived` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `archived_datetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `account_username_role_name_archived_datetime` (`account_username`,`role_name`,`archived_datetime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `account_roles` (`id`, `account_username`, `role_name`, `archived`, `created`, `modified`, `archived_datetime`) VALUES
(323,	'josh',	'admin',	'1',	'2018-06-18 19:09:20',	'2018-10-11 21:21:59',	'2018-10-11 21:21:59'),
(324,	'josh',	'developer',	'1',	'2018-06-18 19:09:20',	'2018-10-11 21:21:59',	'2018-10-11 21:21:59'),
(325,	'josh',	'basic',	'1',	'2018-06-18 19:09:20',	'2018-10-11 21:21:59',	'2018-10-11 21:21:59'),
(326,	'josh',	'admin',	'1',	'2018-10-11 21:21:59',	'2018-10-21 19:54:10',	'2018-10-21 19:54:10'),
(327,	'josh',	'developer',	'1',	'2018-10-11 21:21:59',	'2018-10-21 19:54:10',	'2018-10-21 19:54:10'),
(328,	'josh',	'basic',	'1',	'2018-10-11 21:21:59',	'2018-10-21 19:54:10',	'2018-10-21 19:54:10'),
(329,	'josh',	'admin',	'1',	'2018-10-21 19:54:10',	'2018-10-21 19:54:20',	'2018-10-21 19:54:20'),
(330,	'josh',	'developer',	'1',	'2018-10-21 19:54:10',	'2018-10-21 19:54:20',	'2018-10-21 19:54:20'),
(331,	'josh',	'admin',	'1',	'2018-10-21 19:54:20',	'2018-11-02 19:23:11',	'2018-11-02 19:23:11'),
(332,	'josh',	'developer',	'1',	'2018-10-21 19:54:20',	'2018-11-02 19:23:11',	'2018-11-02 19:23:11'),
(333,	'josh',	'basic',	'1',	'2018-10-21 19:54:20',	'2018-11-02 19:23:11',	'2018-11-02 19:23:11'),
(334,	'josh',	'admin',	'1',	'2018-11-02 19:23:11',	'2018-11-03 18:40:39',	'2018-11-03 18:40:39'),
(335,	'josh',	'developer',	'1',	'2018-11-02 19:23:11',	'2018-11-03 18:40:39',	'2018-11-03 18:40:39'),
(336,	'josh',	'admin',	'1',	'2018-11-03 18:40:39',	'2018-11-15 17:55:22',	'2018-11-15 17:55:22'),
(337,	'josh',	'developer',	'1',	'2018-11-03 18:40:39',	'2018-11-15 17:55:22',	'2018-11-15 17:55:22'),
(338,	'josh',	'basic',	'1',	'2018-11-03 18:40:39',	'2018-11-15 17:55:22',	'2018-11-15 17:55:22'),
(339,	'jdoe',	'admin',	'1',	'2018-11-04 19:08:08',	'2018-11-04 19:08:51',	'2018-11-04 19:08:51'),
(340,	'jdoe',	'developer',	'1',	'2018-11-04 19:08:08',	'2018-11-04 19:08:51',	'2018-11-04 19:08:51'),
(341,	'jdoe',	'basic',	'1',	'2018-11-04 19:08:08',	'2018-11-04 19:08:51',	'2018-11-04 19:08:51'),
(342,	'jdoe',	'basic',	'1',	'2018-11-04 19:08:51',	'2018-11-15 17:56:15',	'2018-11-15 17:56:15'),
(343,	'josh',	'admin',	'1',	'2018-11-15 17:55:22',	'2018-11-15 17:58:32',	'2018-11-15 17:58:32'),
(344,	'josh',	'developer',	'1',	'2018-11-15 17:55:22',	'2018-11-15 17:58:32',	'2018-11-15 17:58:32'),
(345,	'jdoe',	'developer',	'0',	'2018-11-15 17:56:15',	'2018-11-15 17:56:15',	'0000-00-00 00:00:00'),
(346,	'jdoe',	'basic',	'0',	'2018-11-15 17:56:15',	'2018-11-15 17:56:15',	'0000-00-00 00:00:00'),
(347,	'josh',	'admin',	'1',	'2018-11-15 17:58:32',	'2018-11-15 17:58:38',	'2018-11-15 17:58:38'),
(348,	'josh',	'admin',	'0',	'2018-11-15 17:58:38',	'2018-11-15 17:58:38',	'0000-00-00 00:00:00'),
(349,	'josh',	'developer',	'0',	'2018-11-15 17:58:38',	'2018-11-15 17:58:38',	'0000-00-00 00:00:00'),
(350,	'josh',	'basic',	'0',	'2018-11-15 17:58:38',	'2018-11-15 17:58:38',	'0000-00-00 00:00:00');

CREATE TABLE `category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `category` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `archived` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `archived_datetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `category` (`category`,`archived_datetime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `category` (`id`, `key`, `category`, `description`, `archived`, `created`, `modified`, `archived_datetime`) VALUES
(1,	'email',	'Email',	'Email Configuration',	'0',	'2018-06-18 21:18:23',	'2018-06-18 21:18:23',	'0000-00-00 00:00:00'),
(2,	'recaptcha',	'ReCaptcha',	'Recaptcha keys',	'0',	'2018-06-18 21:19:37',	'2018-06-18 21:19:37',	'0000-00-00 00:00:00'),
(3,	'administrative',	'Administrative',	'Site functionality and configuration settings',	'0',	'2018-06-18 21:29:41',	'2018-06-18 21:29:41',	'0000-00-00 00:00:00'),
(4,	'templates',	'Templates',	'Configuration involving template snippets',	'0',	'2018-06-24 22:31:25',	'2018-06-24 22:31:25',	'0000-00-00 00:00:00'),
(5,	'cookie',	'Cookies',	'Site cookie management',	'0',	'2018-06-25 03:45:59',	'2018-06-25 03:45:59',	'0000-00-00 00:00:00'),
(6,	'development',	'Development and Debugging',	NULL,	'0',	'2018-06-25 03:54:43',	'2018-06-25 03:54:43',	'0000-00-00 00:00:00'),
(7,	'cms',	'CMS',	'Content Management System for site pages.',	'0',	'2018-08-19 19:18:45',	'2018-08-19 19:18:45',	'0000-00-00 00:00:00');

CREATE TABLE `css_iteration` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `css` text COLLATE utf8_unicode_ci,
  `author` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `is_selected` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `preview_only` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `archived` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `archived_datetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `css_iteration` (`id`, `uid`, `css`, `author`, `description`, `is_selected`, `preview_only`, `archived`, `created`, `modified`, `archived_datetime`) VALUES
(30,	'c8a48e9694620d831f78959a7b05f23b',	'body,html {\r\n	font-family: \'Titillium Web\', \'sans-serif\';\r\n}\r\n.page#banner,\r\n.page#content {\r\n    box-shadow: -2px 2px 4px #646464\r\n}\r\n\r\n.page#banner {\r\n    position: fixed;\r\n    width: 100%;\r\n}\r\n\r\n.page#container {\r\n    position: relative;\r\n    width: 100%\r\n}\r\n\r\n.blog #teaser,\r\n.italic {\r\n    font-style: italic\r\n}\r\n\r\nbody,\r\nhtml {\r\n    height: 100%;\r\n    width: 100%;\r\n    margin: 0;\r\n    padding: 0;\r\n    font-size: 1em;\r\n    color: #325032;\r\n    -webkit-font-smoothing: antialiased;\r\n    text-shadow: 0 0 0;\r\n    font-family: \'Titillium Web\', \'sans-serif\';\r\n}\r\n.page#banner {\r\n    top: 0;\r\n    left: 0;\r\n    z-index: 10;\r\n    background: url(https://www.joshlrogers.com/assets/img/banner_desktop-v4.png) center no-repeat #fff;\r\n    height: 85px\r\n}\r\n\r\n.page.reduced#banner {\r\n    height: 35px;\r\n}\r\n\r\n.page#static_title {\r\n    margin-left: 2%;\r\n    padding-top: 22px;\r\n}\r\n\r\n.page.reduced .page#static_title {\r\n    font-size: 1.25em;\r\n    padding-top: 2px;\r\n}\r\n\r\n.page#static_title a {\r\n    text-decoration: none;\r\n    color: black;\r\n}\r\n\r\n.page#container {\r\n    background: url(https://www.joshlrogers.com/assets/img/squares.png);\r\n    float: left\r\n}\r\n\r\n.page#content {\r\n    padding: 100px 2% 2%;\r\n    background-color: #fff;\r\n    position: relative;\r\n    width: 80%;\r\n    margin: 0 auto;\r\n    min-height: 800px;\r\n    height: 100%;\r\n    display: block\r\n}\r\n\r\n.page#footer {\r\n    background-color: #969696;\r\n    position: relative;\r\n}\r\n\r\n.slider#slider {\r\n    border: solid 2px #e6e6e6;\r\n    position: relative\r\n}\r\n\r\n.page#menu_container {\r\n    position: absolute;\r\n    width: 500px;\r\n    right: 0;\r\n    top: 0;\r\n    z-index: 10;\r\n}\r\n\r\n.page#menu {\r\n    position: relative;\r\n    display: table;\r\n    margin: auto;\r\n}\r\n\r\n.page#menu_container_mobile {\r\n    display: none;\r\n    position: absolute;\r\n    right: 24px;\r\n    width: 50%;\r\n    min-width: 250px;\r\n    top: 70px;\r\n    background-color: #e6e6e6;\r\n    box-shadow: -4px 4px 8px #646464;\r\n    border: solid 1px #646464;\r\n}\r\n\r\n.page.reduced .page#menu_container_mobile {\r\n    top: 30px;\r\n}\r\n\r\n.page#breadcrumbs {\r\n    width: 80%;\r\n    font-size: smaller;\r\n    margin: 10px 0\r\n}\r\n\r\n.page#menu_container #menu li,\r\n.page#menu_container_mobile #menu li {\r\n    text-align: center;\r\n    list-style-type: none;\r\n    position: relative;\r\n    margin: 2px;\r\n    min-width: 100px;\r\n    display: table-cell;\r\n    text-transform: uppercase;\r\n    border-top: 1px solid transparent;\r\n    float: left;\r\n    text-decoration: none\r\n}\r\n\r\n.page#menu_container #menu li {\r\n    height: 1em;\r\n    padding: 2px 0;\r\n}\r\n\r\n.page#menu_container #menu li a {\r\n    text-decoration: none;\r\n    color: inherit\r\n}\r\n\r\n.page#menu_container_mobile #menu li {\r\n    height: auto;\r\n    padding: 16px 0;\r\n    font-size: 1.5em;\r\n    color: #646464\r\n}\r\n\r\n.page#menu_container_mobile #menu li a {\r\n    text-decoration: none;\r\n    color: inherit\r\n}\r\n\r\n.page#menu_icon {\r\n    position: absolute;\r\n    z-index: 1000;\r\n    right: 25px;\r\n    top: 20px;\r\n    display: none\r\n}\r\n\r\n.page.reduced .page#menu_icon {\r\n    top: 5px;\r\n}\r\n\r\n.page#menu_icon img {\r\n    display: inline;\r\n    cursor: pointer;\r\n}\r\n\r\n.page#footer {\r\n    height: 100px;\r\n    float: left;\r\n    width: 100%\r\n}\r\n\r\n.page#footer p {\r\n    margin: 2%\r\n}\r\n\r\n.page .one-third_left {\r\n    margin-right: 1%;\r\n    padding: 1%;\r\n    width: 30%;\r\n    float: left\r\n}\r\n\r\n.page .two-thirds_left {\r\n    margin-right: 1%;\r\n    padding: 1%;\r\n    width: 64%;\r\n    float: left\r\n}\r\n\r\n.page .one-third_right {\r\n    margin-left: 1%;\r\n    padding: 1%;\r\n    width: 30%;\r\n    float: right\r\n}\r\n\r\n.page .two-thirds_right {\r\n    margin-left: 1%;\r\n    padding: 1%;\r\n    width: 64%;\r\n    float: right\r\n}\r\n\r\n.page .desktop_and_tablet,\r\n.page .desktop_only {\r\n    display: inherit\r\n}\r\n\r\n.page .mobile_only,\r\n.page .tablet_and_mobile,\r\n.page .tablet_only {\r\n    display: none\r\n}\r\n\r\n.page .table {\r\n    display: table;\r\n    border: 1px solid black;\r\n}\r\n\r\n.page .thead {\r\n    display: table-header-group;\r\n    font-weight: bold;\r\n    text-align: center;\r\n}\r\n\r\n.page .tbody {\r\n    display: table-row-group;\r\n}\r\n\r\n.page .tr {\r\n    display: table-row;\r\n}\r\n\r\n.page .td, .page .th {\r\n    display: table-cell;\r\n    border: 1px solid black;\r\n    padding: 1%;\r\n}\r\n\r\n.page .th {\r\n    font-weight: bold;\r\n    text-align: center;\r\n}\r\n\r\n.page .tr:nth-child(even) {\r\n    background-color: #e3e3e3;\r\n}\r\n\r\n.home #slider_content_mobile {\r\n    width: 96%;\r\n    margin: 30px auto 0 auto;\r\n}\r\n\r\n.home #slider_content_mobile .blog_result_container {\r\n    margin: 10px 0;\r\n    width: 100%;\r\n    padding-top: 10px\r\n}\r\n\r\n.home #slider_content_mobile .blog_result_container:first-child {\r\n    float: left;\r\n    margin: 0 0 10px 0;\r\n    width: 100%;\r\n    padding-top: 0;\r\n}\r\n\r\n.home #slider_content_mobile li {\r\n    margin-bottom: 20px\r\n}\r\n\r\n.slider#slider {\r\n    width: 600px;\r\n    max-width: 96%;\r\n    height: 230px;\r\n    clear: both;\r\n}\r\n\r\n.slider .slider_title {\r\n    line-height: 1.25em;\r\n    font-size: 1.5em\r\n}\r\n\r\n.slider .slider_container {\r\n    position: relative;\r\n    clear: both\r\n}\r\n\r\n.slider #slider_nav_left {\r\n    left: 0\r\n}\r\n\r\n.slider #slider_nav_right {\r\n    right: 0\r\n}\r\n\r\n.slider #slider_nav div {\r\n    top: 105px;\r\n    cursor: pointer;\r\n   /* z-index: 1000;*/\r\n    position: absolute\r\n}\r\n\r\n.slider .slider_image_container {\r\n    width: 200px;\r\n    max-width: 30%;\r\n    height: 200px;\r\n    margin: 10px 25px;\r\n    overflow: hidden;\r\n    position: relative;\r\n    float: left\r\n}\r\n\r\n.slider .slider_images {\r\n    max-width: 200px;\r\n    width: 100%;\r\n    position: absolute;\r\n    top: 0;\r\n    bottom: 0;\r\n    margin: auto\r\n}\r\n\r\n.blog #blog_hero,\r\n.blog .small_blog_image {\r\n    max-height: 200px;\r\n    max-width: 30%;\r\n    float: left\r\n}\r\n\r\n.slider .slider_text_container {\r\n    width: 285px;\r\n    max-width: 50%;\r\n    height: 200px;\r\n    margin: 10px 5px;\r\n    overflow: hidden;\r\n    position: relative;\r\n    float: left\r\n}\r\n\r\n.blog .blog_result_container {\r\n    float: left;\r\n    margin: 20px 0;\r\n    width: 100%;\r\n    /*border-top:3px solid gray;*/\r\n    padding-top: 20px\r\n}\r\n\r\n.blog .blog_result_container:first-child {\r\n    float: left;\r\n    margin: 0 0 20px 0;\r\n    width: 100%;\r\n    padding-top: 0;\r\n}\r\n\r\n.blog .blog_result_container .teaser em,\r\n.blog .blog_result_container h4 em{\r\n    font-weight: bold;\r\n}\r\n\r\n.blog .blog_result_container .teaser em:after,\r\n.blog .blog_result_container h4 em:after{\r\n    content: \' \';\r\n}\r\n\r\n.blog #blog_hero {\r\n    margin: 10px 2%\r\n}\r\n\r\n.blog #blog_top_text {\r\n    margin: 10px 0;\r\n    width: 100%\r\n}\r\n\r\n.blog #teaser {\r\n    margin: 10px 0;\r\n    font-size: larger\r\n}\r\n\r\n.blog .view_blog#blog_content {\r\n    clear: both;\r\n    border-top: 3px solid #646464;\r\n    margin: 10px 0;\r\n    padding-top: 10px\r\n}\r\n\r\n.blog .small_blog_image {\r\n    margin: 10px 2%\r\n}\r\n\r\n.blog ol,\r\n.blog ul {\r\n    margin-left: 1em;\r\n    padding: inherit\r\n}\r\n\r\n.blog li {\r\n    display: list-item;\r\n    margin-bottom: 1em;\r\n    margin-left: 1em\r\n}\r\n\r\n.blog table li {\r\n    margin-bottom: inherit\r\n}\r\n\r\n.blog table th {\r\n    background: #e6e6e6;\r\n    font-weight: bold;\r\n    text-align: center;\r\n}\r\n\r\n.blog table th, .blog table td {\r\n    padding: 5px;\r\n}\r\n\r\n.blog #blog_replies {\r\n    margin-top: 50px;\r\n    border-top: 3px solid #646464;\r\n}\r\n\r\n.blog #bottom_container {\r\n    text-align: center;\r\n    clear: both\r\n}\r\n\r\n#blog_replies textarea {\r\n    height: 200px\r\n}\r\n\r\n.seo_sidebar,\r\n.seo_bottom {\r\n    background-color: #e6e6e6;\r\n    padding: 1%;\r\n}\r\n\r\n.seo_bottom ul {\r\n    padding-left: 2.5em;\r\n}\r\n\r\n\r\n.seo_sidebar {\r\n    max-width: 250px\r\n}\r\n\r\n.seo_sidebar h3 {\r\n    margin: 0 0 .5em 0;\r\n    line-height: 1em;\r\n}\r\n\r\n.seo_sidebar ol,\r\n.seo_sidebar ul {\r\n    margin-left: inherit;\r\n    padding: inherit\r\n}\r\n\r\n.seo_sidebar li {\r\n    display: inherit;\r\n    margin: 0;\r\n}\r\n\r\n.page #profile_left.two-thirds_left {\r\n    margin-bottom: 100px;\r\n}\r\n\r\n#profile_right.one-third_right {\r\n    background: #e6e6e6;\r\n    min-width: 30%;\r\n}\r\n\r\n#profile_right.one-third_right img {\r\n    max-width: 100%;\r\n}\r\n\r\n.page #profile_right.two-thirds_right {\r\n    width: 74%;\r\n}\r\n\r\n.profile #profile_right_text h3 {\r\n    margin-top: 30px\r\n}\r\n\r\n.profile #profile_right li,\r\n#profile_alt li {\r\n    display: block;\r\n}\r\n\r\n.profile #profile_right_text ul li img {\r\n    margin-right: 10px;\r\n    float: left;\r\n    clear: both\r\n}\r\n\r\n#profile_alt h3 {\r\n    margin-top: 30px\r\n}\r\n\r\n#profile_alt ul li img {\r\n    margin-right: 10px;\r\n    float: left;\r\n    clear: both\r\n}\r\n\r\n#profile_alt {\r\n    margin-bottom: 50px;\r\n}\r\n\r\n#profile_alt .margin_auto img {\r\n    max-width: 200px\r\n}\r\n\r\n#profile_message_errors {\r\n    float: left;\r\n    display: block;\r\n    clear: both;\r\n    margin-bottom: 20px;\r\n}\r\n\r\n#profile_message_errors h3 {\r\n    color: red;\r\n}\r\n\r\n#profile_message_errors ul, #profile_message_errors ul li {\r\n    margin-left: 1em;\r\n    padding-left: 1em;\r\n}\r\n\r\n#profile_message_errors ul li {\r\n    display: list-item;\r\n}\r\n\r\ninput[type=\'text\'],\r\ninput[type=\'password\'],\r\ninput[type=\'email\'],\r\ninput[type=\'tel\'],\r\ninput[type=&quot;url&quot;],\r\ntextarea,\r\nselect {\r\n    border: 1px solid gray;\r\n    padding: 10px 2%;\r\n    margin: 10px;\r\n    width: 250px;\r\n    display: block;\r\n    background: white;\r\n}\r\n\r\n.floating_form_label_and_input {\r\n    display: inline-block;\r\n    margin: 1%;\r\n}\r\n\r\n.floating_form_label_and_input label {\r\n    margin: 10px;\r\n}\r\n\r\n.floating_form {\r\n    margin: 1% 0;\r\n    clear: both;\r\n    position: relative;\r\n}\r\n\r\n.floating_form {\r\n    background-color: #e6e6e6;\r\n    padding: 10px 0;\r\n    max-width: 700px;\r\n}\r\n\r\n.floating_form #textarea_wrapper {\r\n    margin: 1%;\r\n}\r\n\r\n.floating_form #textarea_wrapper label {\r\n    margin: 10px;\r\n}\r\n\r\n.floating_form #textarea_wrapper textarea {\r\n    width: 540px;\r\n    height: 200px;\r\n    font: inherit;\r\n    font-size: smaller;\r\n}\r\n\r\n.floating_form input[type=\'text\'], .floating_form input[type=\'email\'], .floating_form input[type=\'tel\'] {\r\n    background-color: white;\r\n}\r\n\r\n.floating_form#short_url_container .floating_form_label_and_input {\r\n    width: 100%;\r\n}\r\n\r\n.floating_form#short_url_container .floating_form_label_and_input input[type=\'url\'] {\r\n    width: 80%;\r\n}\r\n\r\n#bottom_container {\r\n    margin: 20px;\r\n    text-align: center;\r\n    padding: 5px;\r\n    line-height: 30px;\r\n}\r\n\r\n.pagination {\r\n    margin: 5px;\r\n    background-color: rgb(236, 236, 236);\r\n    border-radius: 3px;\r\n    padding: 1px 5px;\r\n}\r\n\r\n.page_cur {\r\n    font-weight: bold;\r\n    border: 1px solid black;\r\n}\r\n\r\na.pagination {\r\n    text-decoration: none;\r\n    color: black;\r\n}\r\n\r\na.page_cur {\r\n    font-weight: bold;\r\n    border: 2px solid black;\r\n    cursor: default;\r\n}\r\n\r\n.breadcrumbs ol {\r\n    padding: 0;\r\n}\r\n\r\n.breadcrumbs ol li {\r\n    display: inline;\r\n}\r\n\r\n.portfolio_item_container {\r\n    display: inline-block;\r\n    margin: 1%;\r\n    vertical-align: top;\r\n    padding: 1%;\r\n    width: 250px;\r\n}\r\n\r\n.portfolio_image_container {\r\n    margin: auto;\r\n    width: 100%;\r\n    height: 250px;\r\n    position: relative;\r\n}\r\n\r\n.portfolio_item_container .portfolio_image_container {\r\n    overflow-y: hidden;\r\n}\r\n\r\n.portfolio_image_container img {\r\n    max-width: 200px;\r\n    margin: auto;\r\n    position: absolute;\r\n    top: 0;\r\n    bottom: 0;\r\n    left: 0;\r\n    right: 0;\r\n}\r\n\r\n.portfolio_text_container {\r\n    text-align: center;\r\n}\r\n\r\n.portfolio li,\r\n.portfolio p {\r\n    margin-bottom: 1em\r\n}\r\n\r\n.portfolio ol,\r\n.portfolio ul {\r\n    margin-left: 1em;\r\n    padding: inherit\r\n}\r\n\r\n.portfolio li {\r\n    display: list-item;\r\n    margin-left: 1em;\r\n}\r\n\r\n.portfolio table li {\r\n    margin-bottom: inherit\r\n}\r\n\r\n.portfolio #portfolio_side, .portfolio #portfolio_side_tablet_and_mobile {\r\n    word-break: break-all;\r\n    background: #e6e6e6;\r\n}\r\n\r\n.portfolio #portfolio_side_tablet_and_mobile {\r\n    word-break: break-all;\r\n    background: #e6e6e6;\r\n    padding: 25px 0 5px 0; /* seems like a hacky combo - TODO check up on the content in this container and the css thats being used */\r\n}\r\n\r\n.portfolio a,\r\n.portfolio a:visited {\r\n    color: inherit;\r\n    text-decoration: underline;\r\n}\r\n\r\n.policies p {\r\n    margin-bottom: 1em;\r\n}\r\n\r\n.search_form {\r\n    clear: both;\r\n}\r\n\r\n.search_form label {\r\n    font-size: 1.75em;\r\n    margin: 0 10px;\r\n}\r\n\r\n.search_form input[type=\'submit\'] {\r\n    margin: 10px;\r\n}\r\n\r\n.home #about_and_portfolio_container.two-thirds_right #about_home,\r\n.home #about_and_portfolio_container.two-thirds_right #portfolio_home {\r\n    margin-top: 30px;\r\n}\r\n\r\n.admin #content_category_table {\r\n    width: 100%;\r\n}\r\n\r\n@media screen and (max-width: 800px) {\r\n    .page#content {\r\n        width: 96%\r\n    }\r\n\r\n    .page .one-third_left,\r\n    .page .one-third_right,\r\n    .page .two-thirds_left,\r\n    .page .two-thirds_right {\r\n        width: 96%;\r\n        float: none;\r\n        clear: both;\r\n        margin-left: 0;\r\n        margin-right: 0;\r\n        padding-left: 0;\r\n        padding-right: 0;\r\n    }\r\n\r\n    .page .desktop_only {\r\n        display: none\r\n    }\r\n\r\n    .page .desktop_and_tablet,\r\n    .page .tablet_and_mobile,\r\n    .page .tablet_only {\r\n        display: inherit\r\n    }\r\n\r\n    .page .mobile_only {\r\n        display: none\r\n    }\r\n\r\n    .home .one-third_left,\r\n    .home .two-thirds_right {\r\n        margin: auto;\r\n    }\r\n\r\n    .home #about_and_portfolio_container.two-thirds_right {\r\n        background-color: #e6e6e6;\r\n        margin-top: 30px;\r\n    }\r\n\r\n    .home #about_and_portfolio_container.two-thirds_right #about_home,\r\n    .home #about_and_portfolio_container.two-thirds_right #portfolio_home {\r\n        margin: 0 10px;\r\n    }\r\n\r\n    .home #about_and_portfolio_container.two-thirds_right #about_home h3,\r\n    .home #about_and_portfolio_container.two-thirds_right #portfolio_home h3 {\r\n        margin-top: 10px;\r\n    }\r\n\r\n    .blog .one-third_left,\r\n    .blog .two-thirds_right {\r\n        margin: auto;\r\n    }\r\n\r\n    #blog_content img {\r\n        float: none !important;\r\n        margin: 2% auto !important;\r\n        display: block !important;\r\n        max-width: 90% !important\r\n    }\r\n\r\n    .portfolio_item_container {\r\n        margin: auto;\r\n        display: block;\r\n    }\r\n\r\n    #profile_alt.tablet_and_mobile.margin_on_bottom {\r\n        background-color: #e6e6e6;\r\n        padding: 10px;\r\n    }\r\n\r\n    .slider {\r\n        display: none\r\n    }\r\n\r\n    .page #profile_right.one-third_right {\r\n        width: inherit;\r\n    }\r\n\r\n    .floating_form #textarea_wrapper textarea {\r\n        width: 250px\r\n    }\r\n\r\n    .one-third_left .seo_sidebar, .one-third_right .seo_sidebar {\r\n        max-width: 100%;\r\n        clear: both;\r\n    }\r\n\r\n    .page .table {\r\n        display: block;\r\n        border: none;\r\n    }\r\n\r\n    .page .thead {\r\n        display: none;\r\n    }\r\n\r\n    .page .tbody {\r\n        display: block;\r\n    }\r\n\r\n    .page .tr {\r\n        display: block;\r\n        margin-bottom: 20px;\r\n        border: 1px solid black;\r\n    }\r\n\r\n    .page .td, .page .th {\r\n        display: block;\r\n        border: none;\r\n    }\r\n\r\n    .page .th {\r\n        display: block;\r\n    }\r\n}\r\n\r\n@media screen and (max-width: 600px) {\r\n\r\n    .page#menu_container {\r\n        display: none\r\n    }\r\n\r\n    .page#menu li {\r\n        width: 96%;\r\n        margin: 2px 2%\r\n    }\r\n\r\n    .page#menu_icon {\r\n        display: inherit\r\n    }\r\n\r\n    .page .desktop_and_tablet,\r\n    .page .desktop_only,\r\n    .page .tablet_only {\r\n        display: none\r\n    }\r\n\r\n    .page .mobile_only,\r\n    .page .tablet_and_mobile {\r\n        display: inherit\r\n    }\r\n}\r\n',	'josh',	'',	'0',	'0',	'0',	'2018-10-25 23:25:59',	'2018-10-25 23:25:59',	'0000-00-00 00:00:00'),
(31,	'7c30142ebc271f78b005a6e1679be535',	'body,html {\r\n	font-family: \'Open Sans\', \'sans-serif\';\r\n}\r\n.page#banner,\r\n.page#content {\r\n    box-shadow: -2px 2px 4px #646464\r\n}\r\n\r\n.page#banner {\r\n    position: fixed;\r\n    width: 100%;\r\n}\r\n\r\n.page#container {\r\n    position: relative;\r\n    width: 100%\r\n}\r\n\r\n.blog #teaser,\r\n.italic {\r\n    font-style: italic\r\n}\r\n\r\nbody,\r\nhtml {\r\n    height: 100%;\r\n    width: 100%;\r\n    margin: 0;\r\n    padding: 0;\r\n    font-size: 1em;\r\n    color: #325032;\r\n    -webkit-font-smoothing: antialiased;\r\n    text-shadow: 0 0 0;\r\n    font-family: \'Titillium Web\', \'sans-serif\';\r\n}\r\n.page#banner {\r\n    top: 0;\r\n    left: 0;\r\n    z-index: 10;\r\n    background: url(https://www.joshlrogers.com/assets/img/banner_desktop-v4.png) center no-repeat #fff;\r\n    height: 85px\r\n}\r\n\r\n.page.reduced#banner {\r\n    height: 35px;\r\n}\r\n\r\n.page#static_title {\r\n    margin-left: 2%;\r\n    padding-top: 22px;\r\n}\r\n\r\n.page.reduced .page#static_title {\r\n    font-size: 1.25em;\r\n    padding-top: 2px;\r\n}\r\n\r\n.page#static_title a {\r\n    text-decoration: none;\r\n    color: black;\r\n}\r\n\r\n.page#container {\r\n    background: url(https://www.joshlrogers.com/assets/img/squares.png);\r\n    float: left\r\n}\r\n\r\n.page#content {\r\n    padding: 100px 2% 2%;\r\n    background-color: #fff;\r\n    position: relative;\r\n    width: 80%;\r\n    margin: 0 auto;\r\n    min-height: 800px;\r\n    height: 100%;\r\n    display: block\r\n}\r\n\r\n.page#footer {\r\n    background-color: #969696;\r\n    position: relative;\r\n}\r\n\r\n.slider#slider {\r\n    border: solid 2px #e6e6e6;\r\n    position: relative\r\n}\r\n\r\n.page#menu_container {\r\n    position: absolute;\r\n    width: 500px;\r\n    right: 0;\r\n    top: 0;\r\n    z-index: 10;\r\n}\r\n\r\n.page#menu {\r\n    position: relative;\r\n    display: table;\r\n    margin: auto;\r\n}\r\n\r\n.page#menu_container_mobile {\r\n    display: none;\r\n    position: absolute;\r\n    right: 24px;\r\n    width: 50%;\r\n    min-width: 250px;\r\n    top: 70px;\r\n    background-color: #e6e6e6;\r\n    box-shadow: -4px 4px 8px #646464;\r\n    border: solid 1px #646464;\r\n}\r\n\r\n.page.reduced .page#menu_container_mobile {\r\n    top: 30px;\r\n}\r\n\r\n.page#breadcrumbs {\r\n    width: 80%;\r\n    font-size: smaller;\r\n    margin: 10px 0\r\n}\r\n\r\n.page#menu_container #menu li,\r\n.page#menu_container_mobile #menu li {\r\n    text-align: center;\r\n    list-style-type: none;\r\n    position: relative;\r\n    margin: 2px;\r\n    min-width: 100px;\r\n    display: table-cell;\r\n    text-transform: uppercase;\r\n    border-top: 1px solid transparent;\r\n    float: left;\r\n    text-decoration: none\r\n}\r\n\r\n.page#menu_container #menu li {\r\n    height: 1em;\r\n    padding: 2px 0;\r\n}\r\n\r\n.page#menu_container #menu li a {\r\n    text-decoration: none;\r\n    color: inherit\r\n}\r\n\r\n.page#menu_container_mobile #menu li {\r\n    height: auto;\r\n    padding: 16px 0;\r\n    font-size: 1.5em;\r\n    color: #646464\r\n}\r\n\r\n.page#menu_container_mobile #menu li a {\r\n    text-decoration: none;\r\n    color: inherit\r\n}\r\n\r\n.page#menu_icon {\r\n    position: absolute;\r\n    z-index: 1000;\r\n    right: 25px;\r\n    top: 20px;\r\n    display: none\r\n}\r\n\r\n.page.reduced .page#menu_icon {\r\n    top: 5px;\r\n}\r\n\r\n.page#menu_icon img {\r\n    display: inline;\r\n    cursor: pointer;\r\n}\r\n\r\n.page#footer {\r\n    height: 100px;\r\n    float: left;\r\n    width: 100%\r\n}\r\n\r\n.page#footer p {\r\n    margin: 2%\r\n}\r\n\r\n.page .one-third_left {\r\n    margin-right: 1%;\r\n    padding: 1%;\r\n    width: 30%;\r\n    float: left\r\n}\r\n\r\n.page .two-thirds_left {\r\n    margin-right: 1%;\r\n    padding: 1%;\r\n    width: 64%;\r\n    float: left\r\n}\r\n\r\n.page .one-third_right {\r\n    margin-left: 1%;\r\n    padding: 1%;\r\n    width: 30%;\r\n    float: right\r\n}\r\n\r\n.page .two-thirds_right {\r\n    margin-left: 1%;\r\n    padding: 1%;\r\n    width: 64%;\r\n    float: right\r\n}\r\n\r\n.page .desktop_and_tablet,\r\n.page .desktop_only {\r\n    display: inherit\r\n}\r\n\r\n.page .mobile_only,\r\n.page .tablet_and_mobile,\r\n.page .tablet_only {\r\n    display: none\r\n}\r\n\r\n.page .table {\r\n    display: table;\r\n    border: 1px solid black;\r\n}\r\n\r\n.page .thead {\r\n    display: table-header-group;\r\n    font-weight: bold;\r\n    text-align: center;\r\n}\r\n\r\n.page .tbody {\r\n    display: table-row-group;\r\n}\r\n\r\n.page .tr {\r\n    display: table-row;\r\n}\r\n\r\n.page .td, .page .th {\r\n    display: table-cell;\r\n    border: 1px solid black;\r\n    padding: 1%;\r\n}\r\n\r\n.page .th {\r\n    font-weight: bold;\r\n    text-align: center;\r\n}\r\n\r\n.page .tr:nth-child(even) {\r\n    background-color: #e3e3e3;\r\n}\r\n\r\n.home #slider_content_mobile {\r\n    width: 96%;\r\n    margin: 30px auto 0 auto;\r\n}\r\n\r\n.home #slider_content_mobile .blog_result_container {\r\n    margin: 10px 0;\r\n    width: 100%;\r\n    padding-top: 10px\r\n}\r\n\r\n.home #slider_content_mobile .blog_result_container:first-child {\r\n    float: left;\r\n    margin: 0 0 10px 0;\r\n    width: 100%;\r\n    padding-top: 0;\r\n}\r\n\r\n.home #slider_content_mobile li {\r\n    margin-bottom: 20px\r\n}\r\n\r\n.slider#slider {\r\n    width: 600px;\r\n    max-width: 96%;\r\n    height: 230px;\r\n    clear: both;\r\n}\r\n\r\n.slider .slider_title {\r\n    line-height: 1.25em;\r\n    font-size: 1.5em\r\n}\r\n\r\n.slider .slider_container {\r\n    position: relative;\r\n    clear: both\r\n}\r\n\r\n.slider #slider_nav_left {\r\n    left: 0\r\n}\r\n\r\n.slider #slider_nav_right {\r\n    right: 0\r\n}\r\n\r\n.slider #slider_nav div {\r\n    top: 105px;\r\n    cursor: pointer;\r\n   /* z-index: 1000;*/\r\n    position: absolute\r\n}\r\n\r\n.slider .slider_image_container {\r\n    width: 200px;\r\n    max-width: 30%;\r\n    height: 200px;\r\n    margin: 10px 25px;\r\n    overflow: hidden;\r\n    position: relative;\r\n    float: left\r\n}\r\n\r\n.slider .slider_images {\r\n    max-width: 200px;\r\n    width: 100%;\r\n    position: absolute;\r\n    top: 0;\r\n    bottom: 0;\r\n    margin: auto\r\n}\r\n\r\n.blog #blog_hero,\r\n.blog .small_blog_image {\r\n    max-height: 200px;\r\n    max-width: 30%;\r\n    float: left\r\n}\r\n\r\n.slider .slider_text_container {\r\n    width: 285px;\r\n    max-width: 50%;\r\n    height: 200px;\r\n    margin: 10px 5px;\r\n    overflow: hidden;\r\n    position: relative;\r\n    float: left\r\n}\r\n\r\n.blog .blog_result_container {\r\n    float: left;\r\n    margin: 20px 0;\r\n    width: 100%;\r\n    /*border-top:3px solid gray;*/\r\n    padding-top: 20px\r\n}\r\n\r\n.blog .blog_result_container:first-child {\r\n    float: left;\r\n    margin: 0 0 20px 0;\r\n    width: 100%;\r\n    padding-top: 0;\r\n}\r\n\r\n.blog .blog_result_container .teaser em,\r\n.blog .blog_result_container h4 em{\r\n    font-weight: bold;\r\n}\r\n\r\n.blog .blog_result_container .teaser em:after,\r\n.blog .blog_result_container h4 em:after{\r\n    content: \' \';\r\n}\r\n\r\n.blog #blog_hero {\r\n    margin: 10px 2%\r\n}\r\n\r\n.blog #blog_top_text {\r\n    margin: 10px 0;\r\n    width: 100%\r\n}\r\n\r\n.blog #teaser {\r\n    margin: 10px 0;\r\n    font-size: larger\r\n}\r\n\r\n.blog .view_blog#blog_content {\r\n    clear: both;\r\n    border-top: 3px solid #646464;\r\n    margin: 10px 0;\r\n    padding-top: 10px\r\n}\r\n\r\n.blog .small_blog_image {\r\n    margin: 10px 2%\r\n}\r\n\r\n.blog ol,\r\n.blog ul {\r\n    margin-left: 1em;\r\n    padding: inherit\r\n}\r\n\r\n.blog li {\r\n    display: list-item;\r\n    margin-bottom: 1em;\r\n    margin-left: 1em\r\n}\r\n\r\n.blog table li {\r\n    margin-bottom: inherit\r\n}\r\n\r\n.blog table th {\r\n    background: #e6e6e6;\r\n    font-weight: bold;\r\n    text-align: center;\r\n}\r\n\r\n.blog table th, .blog table td {\r\n    padding: 5px;\r\n}\r\n\r\n.blog #blog_replies {\r\n    margin-top: 50px;\r\n    border-top: 3px solid #646464;\r\n}\r\n\r\n.blog #bottom_container {\r\n    text-align: center;\r\n    clear: both\r\n}\r\n\r\n#blog_replies textarea {\r\n    height: 200px\r\n}\r\n\r\n.seo_sidebar,\r\n.seo_bottom {\r\n    background-color: #e6e6e6;\r\n    padding: 1%;\r\n}\r\n\r\n.seo_bottom ul {\r\n    padding-left: 2.5em;\r\n}\r\n\r\n\r\n.seo_sidebar {\r\n    max-width: 250px\r\n}\r\n\r\n.seo_sidebar h3 {\r\n    margin: 0 0 .5em 0;\r\n    line-height: 1em;\r\n}\r\n\r\n.seo_sidebar ol,\r\n.seo_sidebar ul {\r\n    margin-left: inherit;\r\n    padding: inherit\r\n}\r\n\r\n.seo_sidebar li {\r\n    display: inherit;\r\n    margin: 0;\r\n}\r\n\r\n.page #profile_left.two-thirds_left {\r\n    margin-bottom: 100px;\r\n}\r\n\r\n#profile_right.one-third_right {\r\n    background: #e6e6e6;\r\n    min-width: 30%;\r\n}\r\n\r\n#profile_right.one-third_right img {\r\n    max-width: 100%;\r\n}\r\n\r\n.page #profile_right.two-thirds_right {\r\n    width: 74%;\r\n}\r\n\r\n.profile #profile_right_text h3 {\r\n    margin-top: 30px\r\n}\r\n\r\n.profile #profile_right li,\r\n#profile_alt li {\r\n    display: block;\r\n}\r\n\r\n.profile #profile_right_text ul li img {\r\n    margin-right: 10px;\r\n    float: left;\r\n    clear: both\r\n}\r\n\r\n#profile_alt h3 {\r\n    margin-top: 30px\r\n}\r\n\r\n#profile_alt ul li img {\r\n    margin-right: 10px;\r\n    float: left;\r\n    clear: both\r\n}\r\n\r\n#profile_alt {\r\n    margin-bottom: 50px;\r\n}\r\n\r\n#profile_alt .margin_auto img {\r\n    max-width: 200px\r\n}\r\n\r\n#profile_message_errors {\r\n    float: left;\r\n    display: block;\r\n    clear: both;\r\n    margin-bottom: 20px;\r\n}\r\n\r\n#profile_message_errors h3 {\r\n    color: red;\r\n}\r\n\r\n#profile_message_errors ul, #profile_message_errors ul li {\r\n    margin-left: 1em;\r\n    padding-left: 1em;\r\n}\r\n\r\n#profile_message_errors ul li {\r\n    display: list-item;\r\n}\r\n\r\ninput[type=\'text\'],\r\ninput[type=\'password\'],\r\ninput[type=\'email\'],\r\ninput[type=\'tel\'],\r\ninput[type=&quot;url&quot;],\r\ntextarea,\r\nselect {\r\n    border: 1px solid gray;\r\n    padding: 10px 2%;\r\n    margin: 10px;\r\n    width: 250px;\r\n    display: block;\r\n    background: white;\r\n}\r\n\r\n.floating_form_label_and_input {\r\n    display: inline-block;\r\n    margin: 1%;\r\n}\r\n\r\n.floating_form_label_and_input label {\r\n    margin: 10px;\r\n}\r\n\r\n.floating_form {\r\n    margin: 1% 0;\r\n    clear: both;\r\n    position: relative;\r\n}\r\n\r\n.floating_form {\r\n    background-color: #e6e6e6;\r\n    padding: 10px 0;\r\n    max-width: 700px;\r\n}\r\n\r\n.floating_form #textarea_wrapper {\r\n    margin: 1%;\r\n}\r\n\r\n.floating_form #textarea_wrapper label {\r\n    margin: 10px;\r\n}\r\n\r\n.floating_form #textarea_wrapper textarea {\r\n    width: 540px;\r\n    height: 200px;\r\n    font: inherit;\r\n    font-size: smaller;\r\n}\r\n\r\n.floating_form input[type=\'text\'], .floating_form input[type=\'email\'], .floating_form input[type=\'tel\'] {\r\n    background-color: white;\r\n}\r\n\r\n.floating_form#short_url_container .floating_form_label_and_input {\r\n    width: 100%;\r\n}\r\n\r\n.floating_form#short_url_container .floating_form_label_and_input input[type=\'url\'] {\r\n    width: 80%;\r\n}\r\n\r\n#bottom_container {\r\n    margin: 20px;\r\n    text-align: center;\r\n    padding: 5px;\r\n    line-height: 30px;\r\n}\r\n\r\n.pagination {\r\n    margin: 5px;\r\n    background-color: rgb(236, 236, 236);\r\n    border-radius: 3px;\r\n    padding: 1px 5px;\r\n}\r\n\r\n.page_cur {\r\n    font-weight: bold;\r\n    border: 1px solid black;\r\n}\r\n\r\na.pagination {\r\n    text-decoration: none;\r\n    color: black;\r\n}\r\n\r\na.page_cur {\r\n    font-weight: bold;\r\n    border: 2px solid black;\r\n    cursor: default;\r\n}\r\n\r\n.breadcrumbs ol {\r\n    padding: 0;\r\n}\r\n\r\n.breadcrumbs ol li {\r\n    display: inline;\r\n}\r\n\r\n.portfolio_item_container {\r\n    display: inline-block;\r\n    margin: 1%;\r\n    vertical-align: top;\r\n    padding: 1%;\r\n    width: 250px;\r\n}\r\n\r\n.portfolio_image_container {\r\n    margin: auto;\r\n    width: 100%;\r\n    height: 250px;\r\n    position: relative;\r\n}\r\n\r\n.portfolio_item_container .portfolio_image_container {\r\n    overflow-y: hidden;\r\n}\r\n\r\n.portfolio_image_container img {\r\n    max-width: 200px;\r\n    margin: auto;\r\n    position: absolute;\r\n    top: 0;\r\n    bottom: 0;\r\n    left: 0;\r\n    right: 0;\r\n}\r\n\r\n.portfolio_text_container {\r\n    text-align: center;\r\n}\r\n\r\n.portfolio li,\r\n.portfolio p {\r\n    margin-bottom: 1em\r\n}\r\n\r\n.portfolio ol,\r\n.portfolio ul {\r\n    margin-left: 1em;\r\n    padding: inherit\r\n}\r\n\r\n.portfolio li {\r\n    display: list-item;\r\n    margin-left: 1em;\r\n}\r\n\r\n.portfolio table li {\r\n    margin-bottom: inherit\r\n}\r\n\r\n.portfolio #portfolio_side, .portfolio #portfolio_side_tablet_and_mobile {\r\n    word-break: break-all;\r\n    background: #e6e6e6;\r\n}\r\n\r\n.portfolio #portfolio_side_tablet_and_mobile {\r\n    word-break: break-all;\r\n    background: #e6e6e6;\r\n    padding: 25px 0 5px 0; /* seems like a hacky combo - TODO check up on the content in this container and the css thats being used */\r\n}\r\n\r\n.portfolio a,\r\n.portfolio a:visited {\r\n    color: inherit;\r\n    text-decoration: underline;\r\n}\r\n\r\n.policies p {\r\n    margin-bottom: 1em;\r\n}\r\n\r\n.search_form {\r\n    clear: both;\r\n}\r\n\r\n.search_form label {\r\n    font-size: 1.75em;\r\n    margin: 0 10px;\r\n}\r\n\r\n.search_form input[type=\'submit\'] {\r\n    margin: 10px;\r\n}\r\n\r\n.home #about_and_portfolio_container.two-thirds_right #about_home,\r\n.home #about_and_portfolio_container.two-thirds_right #portfolio_home {\r\n    margin-top: 30px;\r\n}\r\n\r\n.admin #content_category_table {\r\n    width: 100%;\r\n}\r\n\r\n@media screen and (max-width: 800px) {\r\n    .page#content {\r\n        width: 96%\r\n    }\r\n\r\n    .page .one-third_left,\r\n    .page .one-third_right,\r\n    .page .two-thirds_left,\r\n    .page .two-thirds_right {\r\n        width: 96%;\r\n        float: none;\r\n        clear: both;\r\n        margin-left: 0;\r\n        margin-right: 0;\r\n        padding-left: 0;\r\n        padding-right: 0;\r\n    }\r\n\r\n    .page .desktop_only {\r\n        display: none\r\n    }\r\n\r\n    .page .desktop_and_tablet,\r\n    .page .tablet_and_mobile,\r\n    .page .tablet_only {\r\n        display: inherit\r\n    }\r\n\r\n    .page .mobile_only {\r\n        display: none\r\n    }\r\n\r\n    .home .one-third_left,\r\n    .home .two-thirds_right {\r\n        margin: auto;\r\n    }\r\n\r\n    .home #about_and_portfolio_container.two-thirds_right {\r\n        background-color: #e6e6e6;\r\n        margin-top: 30px;\r\n    }\r\n\r\n    .home #about_and_portfolio_container.two-thirds_right #about_home,\r\n    .home #about_and_portfolio_container.two-thirds_right #portfolio_home {\r\n        margin: 0 10px;\r\n    }\r\n\r\n    .home #about_and_portfolio_container.two-thirds_right #about_home h3,\r\n    .home #about_and_portfolio_container.two-thirds_right #portfolio_home h3 {\r\n        margin-top: 10px;\r\n    }\r\n\r\n    .blog .one-third_left,\r\n    .blog .two-thirds_right {\r\n        margin: auto;\r\n    }\r\n\r\n    #blog_content img {\r\n        float: none !important;\r\n        margin: 2% auto !important;\r\n        display: block !important;\r\n        max-width: 90% !important\r\n    }\r\n\r\n    .portfolio_item_container {\r\n        margin: auto;\r\n        display: block;\r\n    }\r\n\r\n    #profile_alt.tablet_and_mobile.margin_on_bottom {\r\n        background-color: #e6e6e6;\r\n        padding: 10px;\r\n    }\r\n\r\n    .slider {\r\n        display: none\r\n    }\r\n\r\n    .page #profile_right.one-third_right {\r\n        width: inherit;\r\n    }\r\n\r\n    .floating_form #textarea_wrapper textarea {\r\n        width: 250px\r\n    }\r\n\r\n    .one-third_left .seo_sidebar, .one-third_right .seo_sidebar {\r\n        max-width: 100%;\r\n        clear: both;\r\n    }\r\n\r\n    .page .table {\r\n        display: block;\r\n        border: none;\r\n    }\r\n\r\n    .page .thead {\r\n        display: none;\r\n    }\r\n\r\n    .page .tbody {\r\n        display: block;\r\n    }\r\n\r\n    .page .tr {\r\n        display: block;\r\n        margin-bottom: 20px;\r\n        border: 1px solid black;\r\n    }\r\n\r\n    .page .td, .page .th {\r\n        display: block;\r\n        border: none;\r\n    }\r\n\r\n    .page .th {\r\n        display: block;\r\n    }\r\n}\r\n\r\n@media screen and (max-width: 600px) {\r\n\r\n    .page#menu_container {\r\n        display: none\r\n    }\r\n\r\n    .page#menu li {\r\n        width: 96%;\r\n        margin: 2px 2%\r\n    }\r\n\r\n    .page#menu_icon {\r\n        display: inherit\r\n    }\r\n\r\n    .page .desktop_and_tablet,\r\n    .page .desktop_only,\r\n    .page .tablet_only {\r\n        display: none\r\n    }\r\n\r\n    .page .mobile_only,\r\n    .page .tablet_and_mobile {\r\n        display: inherit\r\n    }\r\n}\r\n',	'josh',	'',	'0',	'1',	'0',	'2018-10-25 23:27:09',	'2018-10-25 23:27:09',	'0000-00-00 00:00:00'),
(32,	'ff9be9b8e16be75eb7e193e77f4f17fe',	'body,html {\r\n	font-family: \'Open Sans\', \'sans-serif\';\r\n}\r\n.page#banner,\r\n.page#content {\r\n    box-shadow: -2px 2px 4px #646464\r\n}\r\n\r\n.page#banner {\r\n    position: fixed;\r\n    width: 100%;\r\n}\r\n\r\n.page#container {\r\n    position: relative;\r\n    width: 100%\r\n}\r\n\r\n.blog #teaser,\r\n.italic {\r\n    font-style: italic\r\n}\r\n\r\nbody,\r\nhtml {\r\n    height: 100%;\r\n    width: 100%;\r\n    margin: 0;\r\n    padding: 0;\r\n    font-size: 1em;\r\n    color: #1e331e;\r\n    -webkit-font-smoothing: antialiased;\r\n    text-shadow: 0 0 0;\r\n    font-family: \'Titillium Web\', \'sans-serif\';\r\n}\r\n.page#banner {\r\n    top: 0;\r\n    left: 0;\r\n    z-index: 10;\r\n    background: url(https://www.joshlrogers.com/assets/img/banner_desktop-v4.png) center no-repeat #fff;\r\n    height: 85px\r\n}\r\n\r\n.page.reduced#banner {\r\n    height: 35px;\r\n}\r\n\r\n.page#static_title {\r\n    margin-left: 2%;\r\n    padding-top: 22px;\r\n}\r\n\r\n.page.reduced .page#static_title {\r\n    font-size: 1.25em;\r\n    padding-top: 2px;\r\n}\r\n\r\n.page#static_title a {\r\n    text-decoration: none;\r\n    color: black;\r\n}\r\n\r\n.page#container {\r\n    background: url(https://www.joshlrogers.com/assets/img/squares.png);\r\n    float: left\r\n}\r\n\r\n.page#content {\r\n    padding: 100px 2% 2%;\r\n    background-color: #fff;\r\n    position: relative;\r\n    width: 80%;\r\n    margin: 0 auto;\r\n    min-height: 800px;\r\n    height: 100%;\r\n    display: block\r\n}\r\n\r\n.page#footer {\r\n    background-color: #969696;\r\n    position: relative;\r\n}\r\n\r\n.slider#slider {\r\n    border: solid 2px #e6e6e6;\r\n    position: relative\r\n}\r\n\r\n.page#menu_container {\r\n    position: absolute;\r\n    width: 500px;\r\n    right: 0;\r\n    top: 0;\r\n    z-index: 10;\r\n}\r\n\r\n.page#menu {\r\n    position: relative;\r\n    display: table;\r\n    margin: auto;\r\n}\r\n\r\n.page#menu_container_mobile {\r\n    display: none;\r\n    position: absolute;\r\n    right: 24px;\r\n    width: 50%;\r\n    min-width: 250px;\r\n    top: 70px;\r\n    background-color: #e6e6e6;\r\n    box-shadow: -4px 4px 8px #646464;\r\n    border: solid 1px #646464;\r\n}\r\n\r\n.page.reduced .page#menu_container_mobile {\r\n    top: 30px;\r\n}\r\n\r\n.page#breadcrumbs {\r\n    width: 80%;\r\n    font-size: smaller;\r\n    margin: 10px 0\r\n}\r\n\r\n.page#menu_container #menu li,\r\n.page#menu_container_mobile #menu li {\r\n    text-align: center;\r\n    list-style-type: none;\r\n    position: relative;\r\n    margin: 2px;\r\n    min-width: 100px;\r\n    display: table-cell;\r\n    text-transform: uppercase;\r\n    border-top: 1px solid transparent;\r\n    float: left;\r\n    text-decoration: none\r\n}\r\n\r\n.page#menu_container #menu li {\r\n    height: 1em;\r\n    padding: 2px 0;\r\n}\r\n\r\n.page#menu_container #menu li a {\r\n    text-decoration: none;\r\n    color: inherit\r\n}\r\n\r\n.page#menu_container_mobile #menu li {\r\n    height: auto;\r\n    padding: 16px 0;\r\n    font-size: 1.5em;\r\n    color: #646464\r\n}\r\n\r\n.page#menu_container_mobile #menu li a {\r\n    text-decoration: none;\r\n    color: inherit\r\n}\r\n\r\n.page#menu_icon {\r\n    position: absolute;\r\n    z-index: 1000;\r\n    right: 25px;\r\n    top: 20px;\r\n    display: none\r\n}\r\n\r\n.page.reduced .page#menu_icon {\r\n    top: 5px;\r\n}\r\n\r\n.page#menu_icon img {\r\n    display: inline;\r\n    cursor: pointer;\r\n}\r\n\r\n.page#footer {\r\n    height: 100px;\r\n    float: left;\r\n    width: 100%\r\n}\r\n\r\n.page#footer p {\r\n    margin: 2%\r\n}\r\n\r\n.page .one-third_left {\r\n    margin-right: 1%;\r\n    padding: 1%;\r\n    width: 30%;\r\n    float: left\r\n}\r\n\r\n.page .two-thirds_left {\r\n    margin-right: 1%;\r\n    padding: 1%;\r\n    width: 64%;\r\n    float: left\r\n}\r\n\r\n.page .one-third_right {\r\n    margin-left: 1%;\r\n    padding: 1%;\r\n    width: 30%;\r\n    float: right\r\n}\r\n\r\n.page .two-thirds_right {\r\n    margin-left: 1%;\r\n    padding: 1%;\r\n    width: 64%;\r\n    float: right\r\n}\r\n\r\n.page .desktop_and_tablet,\r\n.page .desktop_only {\r\n    display: inherit\r\n}\r\n\r\n.page .mobile_only,\r\n.page .tablet_and_mobile,\r\n.page .tablet_only {\r\n    display: none\r\n}\r\n\r\n.page .table {\r\n    display: table;\r\n    border: 1px solid black;\r\n}\r\n\r\n.page .thead {\r\n    display: table-header-group;\r\n    font-weight: bold;\r\n    text-align: center;\r\n}\r\n\r\n.page .tbody {\r\n    display: table-row-group;\r\n}\r\n\r\n.page .tr {\r\n    display: table-row;\r\n}\r\n\r\n.page .td, .page .th {\r\n    display: table-cell;\r\n    border: 1px solid black;\r\n    padding: 1%;\r\n}\r\n\r\n.page .th {\r\n    font-weight: bold;\r\n    text-align: center;\r\n}\r\n\r\n.page .tr:nth-child(even) {\r\n    background-color: #e3e3e3;\r\n}\r\n\r\n.home #slider_content_mobile {\r\n    width: 96%;\r\n    margin: 30px auto 0 auto;\r\n}\r\n\r\n.home #slider_content_mobile .blog_result_container {\r\n    margin: 10px 0;\r\n    width: 100%;\r\n    padding-top: 10px\r\n}\r\n\r\n.home #slider_content_mobile .blog_result_container:first-child {\r\n    float: left;\r\n    margin: 0 0 10px 0;\r\n    width: 100%;\r\n    padding-top: 0;\r\n}\r\n\r\n.home #slider_content_mobile li {\r\n    margin-bottom: 20px\r\n}\r\n\r\n.slider#slider {\r\n    width: 600px;\r\n    max-width: 96%;\r\n    height: 230px;\r\n    clear: both;\r\n}\r\n\r\n.slider .slider_title {\r\n    line-height: 1.25em;\r\n    font-size: 1.5em\r\n}\r\n\r\n.slider .slider_container {\r\n    position: relative;\r\n    clear: both\r\n}\r\n\r\n.slider #slider_nav_left {\r\n    left: 0\r\n}\r\n\r\n.slider #slider_nav_right {\r\n    right: 0\r\n}\r\n\r\n.slider #slider_nav div {\r\n    top: 105px;\r\n    cursor: pointer;\r\n   /* z-index: 1000;*/\r\n    position: absolute\r\n}\r\n\r\n.slider .slider_image_container {\r\n    width: 200px;\r\n    max-width: 30%;\r\n    height: 200px;\r\n    margin: 10px 25px;\r\n    overflow: hidden;\r\n    position: relative;\r\n    float: left\r\n}\r\n\r\n.slider .slider_images {\r\n    max-width: 200px;\r\n    width: 100%;\r\n    position: absolute;\r\n    top: 0;\r\n    bottom: 0;\r\n    margin: auto\r\n}\r\n\r\n.blog #blog_hero,\r\n.blog .small_blog_image {\r\n    max-height: 200px;\r\n    max-width: 30%;\r\n    float: left\r\n}\r\n\r\n.slider .slider_text_container {\r\n    width: 285px;\r\n    max-width: 50%;\r\n    height: 200px;\r\n    margin: 10px 5px;\r\n    overflow: hidden;\r\n    position: relative;\r\n    float: left\r\n}\r\n\r\n.blog .blog_result_container {\r\n    float: left;\r\n    margin: 20px 0;\r\n    width: 100%;\r\n    /*border-top:3px solid gray;*/\r\n    padding-top: 20px\r\n}\r\n\r\n.blog .blog_result_container:first-child {\r\n    float: left;\r\n    margin: 0 0 20px 0;\r\n    width: 100%;\r\n    padding-top: 0;\r\n}\r\n\r\n.blog .blog_result_container .teaser em,\r\n.blog .blog_result_container h4 em{\r\n    font-weight: bold;\r\n}\r\n\r\n.blog .blog_result_container .teaser em:after,\r\n.blog .blog_result_container h4 em:after{\r\n    content: \' \';\r\n}\r\n\r\n.blog #blog_hero {\r\n    margin: 10px 2%\r\n}\r\n\r\n.blog #blog_top_text {\r\n    margin: 10px 0;\r\n    width: 100%\r\n}\r\n\r\n.blog #teaser {\r\n    margin: 10px 0;\r\n    font-size: larger\r\n}\r\n\r\n.blog .view_blog#blog_content {\r\n    clear: both;\r\n    border-top: 3px solid #646464;\r\n    margin: 10px 0;\r\n    padding-top: 10px\r\n}\r\n\r\n.blog .small_blog_image {\r\n    margin: 10px 2%\r\n}\r\n\r\n.blog ol,\r\n.blog ul {\r\n    margin-left: 1em;\r\n    padding: inherit\r\n}\r\n\r\n.blog li {\r\n    display: list-item;\r\n    margin-bottom: 1em;\r\n    margin-left: 1em\r\n}\r\n\r\n.blog table li {\r\n    margin-bottom: inherit\r\n}\r\n\r\n.blog table th {\r\n    background: #e6e6e6;\r\n    font-weight: bold;\r\n    text-align: center;\r\n}\r\n\r\n.blog table th, .blog table td {\r\n    padding: 5px;\r\n}\r\n\r\n.blog #blog_replies {\r\n    margin-top: 50px;\r\n    border-top: 3px solid #646464;\r\n}\r\n\r\n.blog #bottom_container {\r\n    text-align: center;\r\n    clear: both\r\n}\r\n\r\n#blog_replies textarea {\r\n    height: 200px\r\n}\r\n\r\n.seo_sidebar,\r\n.seo_bottom {\r\n    background-color: #e6e6e6;\r\n    padding: 1%;\r\n}\r\n\r\n.seo_bottom ul {\r\n    padding-left: 2.5em;\r\n}\r\n\r\n\r\n.seo_sidebar {\r\n    max-width: 250px\r\n}\r\n\r\n.seo_sidebar h3 {\r\n    margin: 0 0 .5em 0;\r\n    line-height: 1em;\r\n}\r\n\r\n.seo_sidebar ol,\r\n.seo_sidebar ul {\r\n    margin-left: inherit;\r\n    padding: inherit\r\n}\r\n\r\n.seo_sidebar li {\r\n    display: inherit;\r\n    margin: 0;\r\n}\r\n\r\n.page #profile_left.two-thirds_left {\r\n    margin-bottom: 100px;\r\n}\r\n\r\n#profile_right.one-third_right {\r\n    background: #e6e6e6;\r\n    min-width: 30%;\r\n}\r\n\r\n#profile_right.one-third_right img {\r\n    max-width: 100%;\r\n}\r\n\r\n.page #profile_right.two-thirds_right {\r\n    width: 74%;\r\n}\r\n\r\n.profile #profile_right_text h3 {\r\n    margin-top: 30px\r\n}\r\n\r\n.profile #profile_right li,\r\n#profile_alt li {\r\n    display: block;\r\n}\r\n\r\n.profile #profile_right_text ul li img {\r\n    margin-right: 10px;\r\n    float: left;\r\n    clear: both\r\n}\r\n\r\n#profile_alt h3 {\r\n    margin-top: 30px\r\n}\r\n\r\n#profile_alt ul li img {\r\n    margin-right: 10px;\r\n    float: left;\r\n    clear: both\r\n}\r\n\r\n#profile_alt {\r\n    margin-bottom: 50px;\r\n}\r\n\r\n#profile_alt .margin_auto img {\r\n    max-width: 200px\r\n}\r\n\r\n#profile_message_errors {\r\n    float: left;\r\n    display: block;\r\n    clear: both;\r\n    margin-bottom: 20px;\r\n}\r\n\r\n#profile_message_errors h3 {\r\n    color: red;\r\n}\r\n\r\n#profile_message_errors ul, #profile_message_errors ul li {\r\n    margin-left: 1em;\r\n    padding-left: 1em;\r\n}\r\n\r\n#profile_message_errors ul li {\r\n    display: list-item;\r\n}\r\n\r\ninput[type=\'text\'],\r\ninput[type=\'password\'],\r\ninput[type=\'email\'],\r\ninput[type=\'tel\'],\r\ninput[type=&quot;url&quot;],\r\ntextarea,\r\nselect {\r\n    border: 1px solid gray;\r\n    padding: 10px 2%;\r\n    margin: 10px;\r\n    width: 250px;\r\n    display: block;\r\n    background: white;\r\n}\r\n\r\n.floating_form_label_and_input {\r\n    display: inline-block;\r\n    margin: 1%;\r\n}\r\n\r\n.floating_form_label_and_input label {\r\n    margin: 10px;\r\n}\r\n\r\n.floating_form {\r\n    margin: 1% 0;\r\n    clear: both;\r\n    position: relative;\r\n}\r\n\r\n.floating_form {\r\n    background-color: #e6e6e6;\r\n    padding: 10px 0;\r\n    max-width: 700px;\r\n}\r\n\r\n.floating_form #textarea_wrapper {\r\n    margin: 1%;\r\n}\r\n\r\n.floating_form #textarea_wrapper label {\r\n    margin: 10px;\r\n}\r\n\r\n.floating_form #textarea_wrapper textarea {\r\n    width: 540px;\r\n    height: 200px;\r\n    font: inherit;\r\n    font-size: smaller;\r\n}\r\n\r\n.floating_form input[type=\'text\'], .floating_form input[type=\'email\'], .floating_form input[type=\'tel\'] {\r\n    background-color: white;\r\n}\r\n\r\n.floating_form#short_url_container .floating_form_label_and_input {\r\n    width: 100%;\r\n}\r\n\r\n.floating_form#short_url_container .floating_form_label_and_input input[type=\'url\'] {\r\n    width: 80%;\r\n}\r\n\r\n#bottom_container {\r\n    margin: 20px;\r\n    text-align: center;\r\n    padding: 5px;\r\n    line-height: 30px;\r\n}\r\n\r\n.pagination {\r\n    margin: 5px;\r\n    background-color: rgb(236, 236, 236);\r\n    border-radius: 3px;\r\n    padding: 1px 5px;\r\n}\r\n\r\n.page_cur {\r\n    font-weight: bold;\r\n    border: 1px solid black;\r\n}\r\n\r\na.pagination {\r\n    text-decoration: none;\r\n    color: black;\r\n}\r\n\r\na.page_cur {\r\n    font-weight: bold;\r\n    border: 2px solid black;\r\n    cursor: default;\r\n}\r\n\r\n.breadcrumbs ol {\r\n    padding: 0;\r\n}\r\n\r\n.breadcrumbs ol li {\r\n    display: inline;\r\n}\r\n\r\n.portfolio_item_container {\r\n    display: inline-block;\r\n    margin: 1%;\r\n    vertical-align: top;\r\n    padding: 1%;\r\n    width: 250px;\r\n}\r\n\r\n.portfolio_image_container {\r\n    margin: auto;\r\n    width: 100%;\r\n    height: 250px;\r\n    position: relative;\r\n}\r\n\r\n.portfolio_item_container .portfolio_image_container {\r\n    overflow-y: hidden;\r\n}\r\n\r\n.portfolio_image_container img {\r\n    max-width: 200px;\r\n    margin: auto;\r\n    position: absolute;\r\n    top: 0;\r\n    bottom: 0;\r\n    left: 0;\r\n    right: 0;\r\n}\r\n\r\n.portfolio_text_container {\r\n    text-align: center;\r\n}\r\n\r\n.portfolio li,\r\n.portfolio p {\r\n    margin-bottom: 1em\r\n}\r\n\r\n.portfolio ol,\r\n.portfolio ul {\r\n    margin-left: 1em;\r\n    padding: inherit\r\n}\r\n\r\n.portfolio li {\r\n    display: list-item;\r\n    margin-left: 1em;\r\n}\r\n\r\n.portfolio table li {\r\n    margin-bottom: inherit\r\n}\r\n\r\n.portfolio #portfolio_side, .portfolio #portfolio_side_tablet_and_mobile {\r\n    word-break: break-all;\r\n    background: #e6e6e6;\r\n}\r\n\r\n.portfolio #portfolio_side_tablet_and_mobile {\r\n    word-break: break-all;\r\n    background: #e6e6e6;\r\n    padding: 25px 0 5px 0; /* seems like a hacky combo - TODO check up on the content in this container and the css thats being used */\r\n}\r\n\r\n.portfolio a,\r\n.portfolio a:visited {\r\n    color: inherit;\r\n    text-decoration: underline;\r\n}\r\n\r\n.policies p {\r\n    margin-bottom: 1em;\r\n}\r\n\r\n.search_form {\r\n    clear: both;\r\n}\r\n\r\n.search_form label {\r\n    font-size: 1.75em;\r\n    margin: 0 10px;\r\n}\r\n\r\n.search_form input[type=\'submit\'] {\r\n    margin: 10px;\r\n}\r\n\r\n.home #about_and_portfolio_container.two-thirds_right #about_home,\r\n.home #about_and_portfolio_container.two-thirds_right #portfolio_home {\r\n    margin-top: 30px;\r\n}\r\n\r\n.admin #content_category_table {\r\n    width: 100%;\r\n}\r\n\r\n@media screen and (max-width: 800px) {\r\n    .page#content {\r\n        width: 96%\r\n    }\r\n\r\n    .page .one-third_left,\r\n    .page .one-third_right,\r\n    .page .two-thirds_left,\r\n    .page .two-thirds_right {\r\n        width: 96%;\r\n        float: none;\r\n        clear: both;\r\n        margin-left: 0;\r\n        margin-right: 0;\r\n        padding-left: 0;\r\n        padding-right: 0;\r\n    }\r\n\r\n    .page .desktop_only {\r\n        display: none\r\n    }\r\n\r\n    .page .desktop_and_tablet,\r\n    .page .tablet_and_mobile,\r\n    .page .tablet_only {\r\n        display: inherit\r\n    }\r\n\r\n    .page .mobile_only {\r\n        display: none\r\n    }\r\n\r\n    .home .one-third_left,\r\n    .home .two-thirds_right {\r\n        margin: auto;\r\n    }\r\n\r\n    .home #about_and_portfolio_container.two-thirds_right {\r\n        background-color: #e6e6e6;\r\n        margin-top: 30px;\r\n    }\r\n\r\n    .home #about_and_portfolio_container.two-thirds_right #about_home,\r\n    .home #about_and_portfolio_container.two-thirds_right #portfolio_home {\r\n        margin: 0 10px;\r\n    }\r\n\r\n    .home #about_and_portfolio_container.two-thirds_right #about_home h3,\r\n    .home #about_and_portfolio_container.two-thirds_right #portfolio_home h3 {\r\n        margin-top: 10px;\r\n    }\r\n\r\n    .blog .one-third_left,\r\n    .blog .two-thirds_right {\r\n        margin: auto;\r\n    }\r\n\r\n    #blog_content img {\r\n        float: none !important;\r\n        margin: 2% auto !important;\r\n        display: block !important;\r\n        max-width: 90% !important\r\n    }\r\n\r\n    .portfolio_item_container {\r\n        margin: auto;\r\n        display: block;\r\n    }\r\n\r\n    #profile_alt.tablet_and_mobile.margin_on_bottom {\r\n        background-color: #e6e6e6;\r\n        padding: 10px;\r\n    }\r\n\r\n    .slider {\r\n        display: none\r\n    }\r\n\r\n    .page #profile_right.one-third_right {\r\n        width: inherit;\r\n    }\r\n\r\n    .floating_form #textarea_wrapper textarea {\r\n        width: 250px\r\n    }\r\n\r\n    .one-third_left .seo_sidebar, .one-third_right .seo_sidebar {\r\n        max-width: 100%;\r\n        clear: both;\r\n    }\r\n\r\n    .page .table {\r\n        display: block;\r\n        border: none;\r\n    }\r\n\r\n    .page .thead {\r\n        display: none;\r\n    }\r\n\r\n    .page .tbody {\r\n        display: block;\r\n    }\r\n\r\n    .page .tr {\r\n        display: block;\r\n        margin-bottom: 20px;\r\n        border: 1px solid black;\r\n    }\r\n\r\n    .page .td, .page .th {\r\n        display: block;\r\n        border: none;\r\n    }\r\n\r\n    .page .th {\r\n        display: block;\r\n    }\r\n}\r\n\r\n@media screen and (max-width: 600px) {\r\n\r\n    .page#menu_container {\r\n        display: none\r\n    }\r\n\r\n    .page#menu li {\r\n        width: 96%;\r\n        margin: 2px 2%\r\n    }\r\n\r\n    .page#menu_icon {\r\n        display: inherit\r\n    }\r\n\r\n    .page .desktop_and_tablet,\r\n    .page .desktop_only,\r\n    .page .tablet_only {\r\n        display: none\r\n    }\r\n\r\n    .page .mobile_only,\r\n    .page .tablet_and_mobile {\r\n        display: inherit\r\n    }\r\n}\r\n',	'josh',	'',	'1',	'0',	'0',	'2018-11-13 17:32:44',	'2018-11-13 17:32:44',	'0000-00-00 00:00:00');

DELIMITER ;;

CREATE TRIGGER `before_insert_css` BEFORE INSERT ON `css_iteration` FOR EACH ROW
IF new.uid IS NULL THEN
    SET new.uid = MD5(new.css);
  END IF;;

DELIMITER ;

CREATE TABLE `current_page_iteration` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_master_uid` varchar(36) COLLATE utf8_unicode_ci NOT NULL,
  `page_iteration_uid` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `archived` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `archived_datetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `current_page_iteration` (`id`, `page_master_uid`, `page_iteration_uid`, `archived`, `created`, `modified`, `archived_datetime`) VALUES
(77,	'ac424fec-e82f-11e8-b856-0242ac120005',	'e849d1af003e47dbb627d992852acab14be70d7dc40bb4982476eb8088f4789ca372a2c6420f383097bcf746b8935825cd48948eeb5298109728b60b89ed1b48',	'0',	'2019-05-18 17:45:14',	'2019-05-18 17:45:14',	'0000-00-00 00:00:00'),
(78,	'c083a12c-e830-11e8-b856-0242ac120005',	'c1f753601dd24169f5320a95549d1eb8a96b4b75f1af78cb67314b054c10887cfbf671f1f67da00c6e23e7ea98d5bd1575984d2eb5fd49d61c46e04960e33baf',	'0',	'2019-05-19 22:35:27',	'2019-05-19 22:35:27',	'0000-00-00 00:00:00'),
(79,	'a79012ec-e83a-11e8-b856-0242ac120005',	'06479cbc013624dc32e9f4a32d8fd1cb9ea06e736b58066ddf639406ea0d10aadf0f219f2e97950dd53e23c2f4cae5df1b72fb27a5cf530742365dab42c54f84',	'0',	'2018-12-16 21:18:21',	'2018-12-16 21:18:21',	'0000-00-00 00:00:00'),
(80,	'fddd2cc2-74cc-11e9-8141-0242ac120005',	'44b72f6ff41efe0ddc71e5ba624c5d8e1348d85d62250f473e086f234a0c18ffa89c1a511d29266d435ab637ada1c0b718e483c90d635358f132b05c158060d3',	'0',	'2019-05-18 18:18:16',	'2019-05-18 18:18:16',	'0000-00-00 00:00:00');

CREATE TABLE `login_session` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_username` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `uid` varchar(36) COLLATE utf8_unicode_ci NOT NULL,
  `expiration` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `archived` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `archived_datetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `login_session` (`id`, `account_username`, `uid`, `expiration`, `archived`, `created`, `modified`, `archived_datetime`) VALUES
(28,	'josh',	'7a7244d8-df96-11e8-a854-0242ac120005',	'2018-11-10 00:00:00',	'1',	'2018-11-03 18:30:02',	'2019-05-26 13:40:37',	'2019-05-26 13:40:37'),
(29,	'josh',	'dd6e9501-dfc1-11e8-a854-0242ac120005',	'2018-11-10 00:00:00',	'1',	'2018-11-03 23:40:37',	'2019-05-26 13:40:37',	'2019-05-26 13:40:37'),
(30,	'jdoe',	'cb41216c-e064-11e8-a854-0242ac120005',	'2018-11-11 00:00:00',	'1',	'2018-11-04 19:06:54',	'2018-11-04 19:09:07',	'2018-11-04 19:09:07'),
(31,	'jdoe',	'de96214e-e064-11e8-a854-0242ac120005',	'2018-11-11 00:00:00',	'1',	'2018-11-04 19:07:27',	'2018-11-04 19:09:07',	'2018-11-04 19:09:07'),
(32,	'josh',	'f01ab81f-e064-11e8-a854-0242ac120005',	'2018-11-11 00:00:00',	'1',	'2018-11-04 19:07:56',	'2019-05-26 13:40:37',	'2019-05-26 13:40:37'),
(33,	'jdoe',	'0829d728-e065-11e8-a854-0242ac120005',	'2018-11-11 00:00:00',	'1',	'2018-11-04 19:08:36',	'2018-11-04 19:09:07',	'2018-11-04 19:09:07'),
(34,	'jdoe',	'1a7e3b7b-e065-11e8-a854-0242ac120005',	'2018-11-11 00:00:00',	'0',	'2018-11-04 19:09:07',	'2018-11-04 19:09:07',	'0000-00-00 00:00:00'),
(35,	'josh',	'19e70de0-e07b-11e8-a854-0242ac120005',	'2018-11-11 00:00:00',	'1',	'2018-11-04 21:46:35',	'2019-05-26 13:40:37',	'2019-05-26 13:40:37'),
(36,	'josh',	'1bcb510d-e1d8-11e8-b856-0242ac120005',	'2018-11-13 00:00:00',	'1',	'2018-11-06 15:24:53',	'2019-05-26 13:40:37',	'2019-05-26 13:40:37'),
(37,	'josh',	'd7f6b933-e2ba-11e8-b856-0242ac120005',	'2018-11-14 00:00:00',	'1',	'2018-11-07 18:27:54',	'2019-05-26 13:40:37',	'2019-05-26 13:40:37'),
(38,	'josh',	'a3d6e3bf-e373-11e8-b856-0242ac120005',	'2018-11-15 00:00:00',	'1',	'2018-11-08 16:30:44',	'2019-05-26 13:40:37',	'2019-05-26 13:40:37'),
(39,	'josh',	'9a3d0142-e82f-11e8-b856-0242ac120005',	'2018-11-21 00:00:00',	'1',	'2018-11-14 17:06:18',	'2019-05-26 13:40:37',	'2019-05-26 13:40:37'),
(40,	'josh',	'7eb8cc3b-e8eb-11e8-8496-0242ac120005',	'2018-11-22 00:00:00',	'1',	'2018-11-15 15:31:17',	'2019-05-26 13:40:37',	'2019-05-26 13:40:37'),
(41,	'josh',	'7cf0399f-e8ff-11e8-8496-0242ac120005',	'2018-11-22 00:00:00',	'1',	'2018-11-15 17:54:24',	'2019-05-26 13:40:37',	'2019-05-26 13:40:37'),
(42,	'josh',	'f542c906-ea8e-11e8-8496-0242ac120005',	'2018-11-24 00:00:00',	'1',	'2018-11-17 17:33:55',	'2019-05-26 13:40:37',	'2019-05-26 13:40:37'),
(43,	'josh',	'2c12cc10-015d-11e9-b3b3-0242ac120004',	'2018-12-23 00:00:00',	'1',	'2018-12-16 18:05:29',	'2019-05-26 13:40:37',	'2019-05-26 13:40:37'),
(44,	'josh',	'6383bc8d-0624-11e9-ad67-0242ac120005',	'2018-12-29 00:00:00',	'1',	'2018-12-22 20:01:36',	'2019-05-26 13:40:37',	'2019-05-26 13:40:37'),
(45,	'josh',	'1157e125-1622-11e9-a513-0242ac120005',	'2019-01-18 00:00:00',	'1',	'2019-01-12 04:25:18',	'2019-05-26 13:40:37',	'2019-05-26 13:40:37'),
(46,	'josh',	'b7f89846-4208-11e9-a8a2-0242ac120005',	'2019-03-15 00:00:00',	'1',	'2019-03-09 01:14:42',	'2019-05-26 13:40:37',	'2019-05-26 13:40:37'),
(47,	'josh',	'61ea62f1-4ffb-11e9-94af-0242ac120005',	'2019-04-02 00:00:00',	'1',	'2019-03-26 19:14:30',	'2019-05-26 13:40:37',	'2019-05-26 13:40:37'),
(48,	'josh',	'c8d86f0d-74bd-11e9-8141-0242ac120005',	'2019-05-19 00:00:00',	'1',	'2019-05-12 13:56:47',	'2019-05-26 13:40:37',	'2019-05-26 13:40:37'),
(49,	'josh',	'd4e15593-7655-11e9-8141-0242ac120005',	'2019-05-21 00:00:00',	'1',	'2019-05-14 14:37:42',	'2019-05-26 13:40:37',	'2019-05-26 13:40:37'),
(50,	'josh',	'ba85e6bc-7990-11e9-8141-0242ac120005',	'2019-05-25 00:00:00',	'1',	'2019-05-18 17:16:52',	'2019-05-26 13:40:37',	'2019-05-26 13:40:37'),
(51,	'josh',	'd873b10b-7fbb-11e9-8a2d-0242ac120005',	'2019-06-02 00:00:00',	'0',	'2019-05-26 13:40:37',	'2019-05-26 13:40:37',	'0000-00-00 00:00:00');

CREATE TABLE `page` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_master_uid` varchar(36) COLLATE utf8_unicode_ci DEFAULT NULL,
  `uri_uid` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `archived` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `archived_datetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `page` (`id`, `page_master_uid`, `uri_uid`, `archived`, `created`, `modified`, `archived_datetime`) VALUES
(102,	'ac424fec-e82f-11e8-b856-0242ac120005',	'ac41cf58-e82f-11e8-b856-0242ac120005',	'0',	'2018-11-14 17:06:48',	'2018-11-14 17:06:48',	'0000-00-00 00:00:00'),
(103,	'c083a12c-e830-11e8-b856-0242ac120005',	'c082ee45-e830-11e8-b856-0242ac120005',	'0',	'2018-11-14 17:14:32',	'2018-11-14 17:14:32',	'0000-00-00 00:00:00'),
(104,	'a79012ec-e83a-11e8-b856-0242ac120005',	'a78f3c9c-e83a-11e8-b856-0242ac120005',	'0',	'2018-11-14 18:25:25',	'2018-11-14 18:25:25',	'0000-00-00 00:00:00'),
(105,	'fddd2cc2-74cc-11e9-8141-0242ac120005',	'4e8107e7-7999-11e9-8141-0242ac120005',	'0',	'2019-05-18 18:18:16',	'2019-05-18 18:18:16',	'0000-00-00 00:00:00');

DELIMITER ;;

CREATE TRIGGER `before_insert_page` BEFORE INSERT ON `page` FOR EACH ROW
IF new.page_master_uid IS NULL THEN
    SET new.page_master_uid = UUID();
  END IF;;

DELIMITER ;

CREATE TABLE `page_iteration` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `page_title_seo` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `page_title_h1` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `meta_desc` varchar(160) COLLATE utf8_unicode_ci NOT NULL,
  `meta_robots` enum('index,follow','noindex,nofollow') COLLATE utf8_unicode_ci NOT NULL,
  `content` text COLLATE utf8_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `include_in_sitemap` enum('0','1') COLLATE utf8_unicode_ci NOT NULL,
  `archived` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `archived_datetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `page_iteration` (`id`, `uid`, `page_title_seo`, `page_title_h1`, `meta_desc`, `meta_robots`, `content`, `status`, `include_in_sitemap`, `archived`, `created`, `modified`, `archived_datetime`) VALUES
(415,	'289e6afa10a5030d5f15b2aea8c10bce4402341c8de3ac7723f23b28f76365e4326bc1fdfc6eebab577ddb0eb740d1bf8280d1a869f3236f651bdcab647e33ce',	'',	'',	'',	'index,follow',	'&lt;p&gt;Test Home Page&lt;/p&gt;\n',	'active',	'0',	'0',	'2018-11-14 17:06:48',	'2018-11-14 17:06:48',	'0000-00-00 00:00:00'),
(416,	'98e31ccef25f9a84ae4fe5206601329bf43c1002116a65304dbb4a554db049d482e48a41926374cc59460235ebe09c1395d60f6a5c20bb9a14f30d2d5dc08ced',	'',	'',	'',	'index,follow',	'&lt;p&gt;asdf&lt;/p&gt;\n',	'active',	'0',	'0',	'2018-11-14 17:14:32',	'2018-11-14 17:14:32',	'0000-00-00 00:00:00'),
(417,	'651c0f332737272f78a946d994d16bb8886e2bbdfee9c03a32d8c005f1b9995d764b40bc85e83e51aae9e3b482ba7a81f96d53db52ad275bfad6a53a0f5865dd',	'',	'',	'',	'index,follow',	'&lt;p&gt;asdf&lt;/p&gt;\n',	'active',	'0',	'0',	'2018-11-14 17:15:09',	'2018-11-14 17:15:09',	'0000-00-00 00:00:00'),
(418,	'c73ed26a0074491eab9f889e3b3d4f4ffa1140fb72c27b58abe5aa6df9ccc4b55085d0cbe9ae8992d2e0ee15a9e2b174a02295f17f7ebfb8f468d09515d7c262',	'',	'',	'',	'index,follow',	'&lt;p&gt;Test Home Page r2&lt;/p&gt;\n',	'active',	'0',	'0',	'2018-11-14 17:16:42',	'2018-11-14 17:16:42',	'0000-00-00 00:00:00'),
(419,	'd575f2dcfac4bc67d981ec654a7749a7974c3b2cde62269800880a9acfa42e2955ede3375c396c5baaab41e12b763d1e348f7338965c01e74316ea68702032f2',	'',	'',	'',	'index,follow',	'&lt;p&gt;Test Home Page r3&lt;/p&gt;\n',	'active',	'0',	'0',	'2018-11-14 17:16:53',	'2018-11-14 17:16:53',	'0000-00-00 00:00:00'),
(420,	'2436b16bfa0a530d9d9b35376d94742ea94ac7eba00de5cd180c7cf28af0d1bb5576758b0ebae8045dd22c0b64787942b3665b5bf053b973b2d0e175b539858b',	'',	'',	'',	'index,follow',	'&lt;p&gt;&lt;span style=&quot;font-family:Courier New,Courier,monospace;&quot;&gt;&lt;span style=&quot;font-size:22px;&quot;&gt;&lt;span style=&quot;color:#2ecc71;&quot;&gt;&lt;em&gt;&lt;strong&gt;&lt;span style=&quot;background-color:#dddddd;&quot;&gt;Test Home Page r4&lt;/span&gt;&lt;/strong&gt;&lt;/em&gt;&lt;/span&gt;&lt;/span&gt;&lt;/span&gt;&lt;img alt=&quot;&quot; src=&quot;/img/tf2-engie.png&quot; style=&quot;width: 72px; height: 72px;&quot; /&gt;&lt;/p&gt;\n',	'active',	'0',	'0',	'2018-11-14 18:07:08',	'2018-11-14 18:07:08',	'0000-00-00 00:00:00'),
(421,	'52fdf161be17118782de20df88575d2aa15d2b1843307690916faf727be7661383db05729653a945c65cb3e3182edf3bb709f5b84bf43c2f4af712160532d7c6',	'',	'',	'',	'index,follow',	'&lt;p&gt;&lt;span style=&quot;font-family:Courier New,Courier,monospace;&quot;&gt;&lt;span style=&quot;font-size:22px;&quot;&gt;&lt;span style=&quot;color:#2ecc71;&quot;&gt;&lt;em&gt;&lt;strong&gt;&lt;span style=&quot;background-color:#dddddd;&quot;&gt;Test Home Page r4&lt;/span&gt;&lt;/strong&gt;&lt;/em&gt;&lt;/span&gt;&lt;/span&gt;&lt;/span&gt;&lt;img alt=&quot;&quot; src=&quot;/img/tf2-engie-mrmoJfDY.png&quot; style=&quot;width: 72px; height: 72px;&quot; /&gt;&lt;/p&gt;\n',	'active',	'0',	'0',	'2018-11-14 18:16:07',	'2018-11-14 18:16:07',	'0000-00-00 00:00:00'),
(422,	'd4c9f0895442fcc5cbf0feefaa5c4065178fa660ededcc2ca10c3d78c81a6d4acbc9f9aaf7b743ff642a1c576e06d6561e57d58a6e5666dd162a79248c3cf8ad',	'',	'',	'',	'index,follow',	'&lt;p&gt;{$email = new \\Email();} {$email-&gt;sendEmail(\'Testing with SES\', \'Josh R.\', \'mail@joshlrogers.com\', [], [\'jlr2k8@gmail.com\'], [], [], \'Just testing yo!\');}&lt;/p&gt;\n',	'active',	'0',	'0',	'2018-11-14 18:25:25',	'2018-11-14 18:25:25',	'0000-00-00 00:00:00'),
(423,	'64f8a35ed9022476a773ade8a15a70b58c62e3d9b50270a40c82aee8c8d1c00fab5e21e03ec3157b0a6861ab016144ac7917d57b15a81d391eabde8a220d2136',	'',	'',	'',	'index,follow',	'&lt;p&gt;{var_dump($_SERVER)}&lt;/p&gt;\n',	'active',	'0',	'0',	'2018-11-14 18:26:30',	'2018-11-14 18:26:30',	'0000-00-00 00:00:00'),
(424,	'51526400db23a01f96c9ee5d3d370249d24a742323a1af195fb71c56f904b1b926b6ec173fbaef8b87e00f6b962a6d34ddb80d96fda13dc3fff1efff9541c939',	'',	'',	'',	'index,follow',	'&lt;p&gt;{date(\'Y-m-d H:i:s\')}&lt;/p&gt;\n',	'active',	'0',	'0',	'2018-11-14 18:27:19',	'2018-11-14 18:27:19',	'0000-00-00 00:00:00'),
(425,	'9202f1882e84c8fb5f8845403e1852ef520a6c1f1e634c0827216e59e92866f53e9f368980f156f36f878e4ede165b2b302b50df5ac606acf909a94b9ec06ee8',	'',	'',	'',	'index,follow',	'&lt;p&gt;{var_dump($_SERVER)}&lt;/p&gt;\n',	'active',	'0',	'0',	'2018-11-14 18:27:56',	'2018-11-14 18:27:56',	'0000-00-00 00:00:00'),
(426,	'4b4bb350b1d9a96995fbfccda3182a7f408c01de8d38b68afb561050f9541ab8dc763e9ff1151f26cd4b2cd3322e787c1236a9c25145a04a0b47329544c0db09',	'',	'',	'',	'index,follow',	'&lt;p&gt;{var_dump($smarty.SERVER)}&lt;/p&gt;\n',	'active',	'0',	'0',	'2018-11-14 18:28:43',	'2018-11-14 18:28:43',	'0000-00-00 00:00:00'),
(427,	'48bfb5b4b1a8bbbe04523433482286b186a800cf4607fec5e0112e268f99f60428d44694aeecbd6edad4b198acc5d60470884182283eac20c1d663b81de6cacf',	'',	'',	'',	'index,follow',	'&lt;p&gt;{$css}&lt;/p&gt;\n',	'active',	'0',	'0',	'2018-11-14 19:01:35',	'2018-11-14 19:01:35',	'0000-00-00 00:00:00'),
(428,	'8bae3060bd01a03d5ac0e8d1f39bf81ab288ef9d0fe5976644824b53bcfbf41c6d7162f6a022032bf342a6755d22e6bdb5de3058f5e7807196dd912aec8bf145',	'',	'',	'',	'index,follow',	'&lt;p&gt;{date(\'Y-m-d\')}&lt;/p&gt;\n\n&lt;p&gt;Â &lt;/p&gt;\n\n&lt;p&gt;{$css}&lt;/p&gt;\n',	'active',	'0',	'0',	'2018-11-14 19:02:50',	'2018-11-14 19:02:50',	'0000-00-00 00:00:00'),
(429,	'f9976247b1bc6f327dd71bacbe58183d9f8959716d6ce0511dc891dece812b00d8c750096decd9c455ae7953c0d15404a3e6cb69212176479d6bf7c9dc75c0c1',	'',	'',	'',	'index,follow',	'&lt;p&gt;{$css}&lt;/p&gt;\n',	'active',	'0',	'0',	'2018-11-14 19:03:18',	'2018-11-14 19:03:18',	'0000-00-00 00:00:00'),
(430,	'a672c2947da2fff3a36c4987ef98bd856f8413253659d7ad1d3ced871a2360e847ac764039a6b8c39215ec590a0a0efba8a44ef4dcf15bdb94cd271e8a7768f7',	'',	'',	'',	'index,follow',	'&lt;p&gt;{$css}&lt;/p&gt;\n\n&lt;p&gt;Â &lt;/p&gt;\n\n&lt;p&gt;{date(\'Y-m-d\')}&lt;/p&gt;\n',	'active',	'0',	'0',	'2018-11-14 19:03:39',	'2018-11-14 19:03:39',	'0000-00-00 00:00:00'),
(431,	'44538942a83dbb8485935d713dd3de330ec8fc2478e807a4fd27b23d04cb3662e694de25469b23c094161b24964c206049470e543eb9a0304419e3da10c67f70',	'',	'',	'',	'index,follow',	'&lt;p&gt;{$smarty.SERVER}&lt;/p&gt;\n',	'active',	'0',	'0',	'2018-11-14 19:05:37',	'2018-11-14 19:05:37',	'0000-00-00 00:00:00'),
(432,	'f598e44341ff2e02587ab44743ab63bea7fbdd2444199b39a286d51aa7c0ee1f7e96a428a86242f6ec8eb783e0040caa51b5b3bac2aa49269308438426f8ae28',	'',	'',	'',	'index,follow',	'&lt;p&gt;{$smarty.server.SERVER_NAME}&lt;/p&gt;\n',	'active',	'0',	'0',	'2018-11-14 19:06:19',	'2018-11-14 19:06:19',	'0000-00-00 00:00:00'),
(433,	'fff21bf12fd272d62cb9988bd6322d4e56e413e8997bfd3ca6910e93dbbf8c6e7fcdd2c0e4a9a9ec730af0779107b326d7bad5940ef9200d4667278158ff4da7',	'',	'',	'',	'index,follow',	'&lt;p&gt;{$HOSTNAME}&lt;/p&gt;\n',	'active',	'0',	'0',	'2018-11-14 19:07:57',	'2018-11-14 19:07:57',	'0000-00-00 00:00:00'),
(434,	'3adf6077c5408f22ee0fc17ceca5975c4cdd357ea01be2f81cf7cf5ec229ddec4643c9e78ed8834da6731057db84c1fbff3e4759d452c6cccafd4a1c9d35603d',	'',	'',	'',	'index,follow',	'&lt;p&gt;{time()}&lt;/p&gt;\n',	'active',	'0',	'0',	'2018-11-14 19:15:31',	'2018-11-14 19:15:31',	'0000-00-00 00:00:00'),
(435,	'6a08b2f3ca184cdba0c3fcfa7a415423e1b913384078b52aedcba6e890be1e9f52e399bf8619ac15004a99d81db02133770c792fba617151c99b735d07c80a15',	'',	'',	'',	'index,follow',	'&lt;p&gt;{!empty(time())}&lt;/p&gt;\n',	'active',	'0',	'0',	'2018-11-14 19:19:32',	'2018-11-14 19:19:32',	'0000-00-00 00:00:00'),
(436,	'a8e453944821e5fd7fc8baade4b4c9d672699add97153e8632431571f7ebbceb7106cedc2903df82f4f1b76101cd2162885b5798361de7b021e68e87ea735c29',	'',	'',	'',	'index,follow',	'&lt;p&gt;{time()}&lt;/p&gt;\n',	'active',	'0',	'0',	'2018-11-14 19:20:14',	'2018-11-14 19:20:14',	'0000-00-00 00:00:00'),
(437,	'a2fbaf625b0ffdca537dc4f68bd2da3c14296950c2c9851cef0b7cafc0cb620f935442b3c5c28c074aaee932cb6e0923059c3c2520d68948a64ce437de0a9724',	'',	'',	'',	'index,follow',	'&lt;p&gt;{$css}&lt;/p&gt;\n',	'active',	'0',	'0',	'2018-11-14 19:21:29',	'2018-11-14 19:21:29',	'0000-00-00 00:00:00'),
(438,	'9184848f3e43f072b910b778ea52ead26cb69b28f95fa64ba11fb77d36eb263e72e6dd428fd373d0a15209986c47a9059845f534031ed7105ef7759ffff3f9c9',	'',	'',	'',	'index,follow',	'&lt;p&gt;{$foo}&lt;/p&gt;\n',	'active',	'0',	'0',	'2018-11-14 19:34:29',	'2018-11-14 19:34:29',	'0000-00-00 00:00:00'),
(439,	'065fdc058f5d50c1efe0dac9fe66897e57a9ecfec8551c8201dcd3a092bd7ff293e5fffe07ff57aa2e2ceaa4cafb53fa79bbcd865c39f993068a65f2dfdc1746',	'',	'',	'',	'index,follow',	'&lt;p&gt;{$css}&lt;/p&gt;\n',	'active',	'0',	'0',	'2018-11-14 19:34:58',	'2018-11-14 19:34:58',	'0000-00-00 00:00:00'),
(440,	'5ecb7a7b6ebfaae9ca1e76d318d550fcdea8164652a351fc0329913f24798c4d73561822be09a4bbdd0ebfccadfd1e26d47e066d31b5a50ca6ab16461156b8de',	'',	'',	'',	'index,follow',	'&lt;p&gt;{$foo}&lt;/p&gt;\n',	'active',	'0',	'0',	'2018-11-14 19:37:25',	'2018-11-14 19:37:25',	'0000-00-00 00:00:00'),
(441,	'52bc006856a47d06e8d2f1eb87a6435e0d4810a1ae3e7c953706e60d8dcb7e3fa3ec8d1116fbe6e9105e7df572248b8bb30edf6e35790437c37d1c84d264ed4e',	'',	'',	'',	'index,follow',	'&lt;p&gt;{$debug_footer}&lt;/p&gt;\n',	'active',	'0',	'0',	'2018-11-14 19:37:41',	'2018-11-14 19:37:41',	'0000-00-00 00:00:00'),
(442,	'd33c75bb17ba7c63bbc60d660f818c89939e6e7542bf102d0e48b6a78945534d16dce4143c833f8f420a43ed83daf356263cb44aac28b2a97600f943c684b8f4',	'',	'',	'',	'index,follow',	'&lt;p&gt;{$foo}&lt;/p&gt;\n',	'active',	'0',	'0',	'2018-11-14 19:41:17',	'2018-11-14 19:41:17',	'0000-00-00 00:00:00'),
(443,	'd320bdfb2013f20a2d412b17926701fb04793e358edbab76878c8c7676d74f9829e853363589d23647d7360a1a31fa43fe1c9f6f0a55a8e38ae9e2fe505ecc8e',	'',	'',	'',	'index,follow',	'&lt;p&gt;{$foo = [1,2,3]} {foreach from=$foo item=f}&lt;/p&gt;\n\n&lt;p&gt;{$f}&lt;/p&gt;\n\n&lt;p&gt;{/foreach}&lt;/p&gt;\n',	'active',	'0',	'0',	'2018-11-14 23:10:15',	'2018-11-14 23:10:15',	'0000-00-00 00:00:00'),
(444,	'2517dce6e1b0d0a24d3d02db0753cac1f3c8a411cb41c1dd21c4c4ee76c905b5ffeb8998a3eab3ec578ca18f52762332aab578af9888a63a6111f7c0c25f37a0',	'',	'',	'',	'index,follow',	'&lt;p&gt;{$foo}&lt;/p&gt;\n',	'inactive',	'0',	'0',	'2018-11-14 23:33:43',	'2018-11-14 23:33:43',	'0000-00-00 00:00:00'),
(445,	'7cce4416f34e2b823034867b22de57a4cecfce2e4a6bc8466413819fe5c288dd86b79a2954ed3fee37fb05835a70af365e8584158748fc4698134aeb917efefd',	'',	'',	'',	'index,follow',	'&lt;p&gt;{$foo}&lt;/p&gt;\n',	'active',	'0',	'0',	'2018-11-14 23:35:27',	'2018-11-14 23:35:27',	'0000-00-00 00:00:00'),
(446,	'c5b76148957d4c99c092416ae6da26e4c5b46b3a0b6ec1e960e09110f82519a63ff5d0c8d401cb9c1eaf4cb0607baf1f15f0149217639b4ef531ee0f2a897cd1',	'',	'',	'',	'index,follow',	'&lt;p&gt;{$foo = [1,2,3]} {foreach from=$foo item=f}&lt;/p&gt;\n\n&lt;p&gt;{$f time()}&lt;/p&gt;\n\n&lt;p&gt;{/foreach}&lt;/p&gt;\n',	'active',	'0',	'0',	'2018-11-14 23:36:13',	'2018-11-14 23:36:13',	'0000-00-00 00:00:00'),
(447,	'7248a17791ffe338f8a674b2354bd9f666a3319ddcce79eb9f26f4ff5df4cad64a5a5c6585701d73f07ce7be1c5dc81cd8a0be275867f3f604a867bd42fd47b6',	'',	'',	'',	'index,follow',	'&lt;p&gt;{$foo = [1,2,3]} {foreach from=$foo item=f}&lt;/p&gt;\n\n&lt;p&gt;{$f}&lt;/p&gt;\n\n&lt;p&gt;{/foreach}&lt;/p&gt;\n',	'active',	'0',	'0',	'2018-11-15 00:14:54',	'2018-11-15 00:14:54',	'0000-00-00 00:00:00'),
(448,	'88901cfcf9bcb5efd2baaf36cdf44b75031681589b83334f358d1236ce4d627e14f634ae26d5e20196e1a2ef383c22be1a8664447df284f62e6b5fb829db7c47',	'',	'',	'',	'index,follow',	'&lt;p&gt;{assign foo=&quot;bar&quot;}&lt;/p&gt;\n\n&lt;p&gt;{$foo}&lt;/p&gt;\n',	'active',	'0',	'0',	'2018-12-16 18:06:32',	'2018-12-16 18:06:32',	'0000-00-00 00:00:00'),
(449,	'96cbe33d1f4c0779255da8f13063085fc25407ff5e2532437526789d8f8a518a78fb4e1c285dbd7f50ef021cc7eacea509f7aa4f5416e2e24510a92411923084',	'',	'',	'',	'index,follow',	'&lt;p&gt;{assign &quot;foo&quot; &quot;bar&quot;}&lt;/p&gt;\n\n&lt;p&gt;{$foo}&lt;/p&gt;\n',	'active',	'0',	'0',	'2018-12-16 18:07:43',	'2018-12-16 18:07:43',	'0000-00-00 00:00:00'),
(450,	'3dd079490ee0b163215008c0405ac43127216c808c0ae00b506e3e5b19410f05c6e264f4805d6a0d72f37c4ce356c72138d2669caa76d8011662295890f51bea',	'',	'',	'',	'index,follow',	'&lt;p&gt;{date(\'Y-m-d\')}&lt;/p&gt;\n',	'active',	'0',	'0',	'2018-12-16 18:08:13',	'2018-12-16 18:08:13',	'0000-00-00 00:00:00'),
(451,	'ddf9178d4029050d48acc5fad6e2a875fb2b92c0d617597a359c41d3e5f566d46a01ef5b5b1760743678a01830f1c4ff20957a1e21798fe0cc9738495427bade',	'',	'',	'',	'index,follow',	'&lt;p&gt;{$smarty.server.SERVER_NAME}&lt;/p&gt;\n',	'active',	'0',	'0',	'2018-12-16 21:00:58',	'2018-12-16 21:00:58',	'0000-00-00 00:00:00'),
(452,	'2d9ff688ab0181ba14f7a790acd1be72116350132d1c4d2f9e7d4d638ffcf482f36fd68c5186e7229a1061208ab6a64cf91ff9cbc0be7d9142522b8c3db411be',	'',	'',	'',	'index,follow',	'&lt;p&gt;{$css}&lt;/p&gt;\n',	'active',	'0',	'0',	'2018-12-16 21:01:24',	'2018-12-16 21:01:24',	'0000-00-00 00:00:00'),
(453,	'bebfd8c26c778f362d3cc7a3fb7665a06351363c4f601cdfa74bef7bef5ccf87558170bb9c55b18878bf61a57783c7c4508bd9af68b3f324f64897a482c018c3',	'',	'',	'',	'index,follow',	'&lt;p&gt;{$js}&lt;/p&gt;\n',	'active',	'0',	'0',	'2018-12-16 21:01:37',	'2018-12-16 21:01:37',	'0000-00-00 00:00:00'),
(454,	'c1b5f3325b7797c96b89256ea402c60cc601b5b3a65497c93cb4cee092774082c2910fbb1c68c4a92e82eaa822646bca1cd450f11523d90676ee1d913f78fcea',	'',	'',	'',	'index,follow',	'&lt;p&gt;{$foo}&lt;/p&gt;\n',	'active',	'0',	'0',	'2018-12-16 21:03:20',	'2018-12-16 21:03:20',	'0000-00-00 00:00:00'),
(455,	'75841854585a97ba743e83eea20c48d16b98d4d9bb67f9daa0e3c641026d39838ff1508fc13d2f38f24e58cedcaaf01eef8ad6a34c0d66d9042d8f2dc4f7e7ab',	'',	'',	'',	'index,follow',	'&lt;p&gt;{$hello}&lt;/p&gt;\n',	'active',	'0',	'0',	'2018-12-16 21:06:00',	'2018-12-16 21:06:00',	'0000-00-00 00:00:00'),
(456,	'5cb36e1cc0815d332a8feb1ca24da6a3c55f7a85043a5bcc06ae6896705e995382403d5600a1be98f10743375b180a1a217eeffe62af2bede96adc44cefd07a5',	'',	'IM A TITLE!',	'',	'index,follow',	'&lt;p&gt;{$hello}&lt;/p&gt;\n',	'active',	'0',	'0',	'2018-12-16 21:06:16',	'2018-12-16 21:06:16',	'0000-00-00 00:00:00'),
(457,	'06479cbc013624dc32e9f4a32d8fd1cb9ea06e736b58066ddf639406ea0d10aadf0f219f2e97950dd53e23c2f4cae5df1b72fb27a5cf530742365dab42c54f84',	'',	'IM A TITLE!',	'',	'index,follow',	'&lt;p&gt;asdasdsad&lt;/p&gt;\n',	'active',	'0',	'0',	'2018-12-16 21:18:21',	'2018-12-16 21:18:21',	'0000-00-00 00:00:00'),
(458,	'10a34fea5b4fb76100f2fc5754e464e2c1f008651bbf765a3874c4709cb2e8d0b7aa7f02b7c23107e5715254f98dc23f25f2ab41898f46ab9acd6dc7dfc69a87',	'',	'',	'',	'index,follow',	'&lt;p&gt;&lt;span style=&quot;font-family:Courier New,Courier,monospace;&quot;&gt;&lt;span style=&quot;font-size:22px;&quot;&gt;&lt;span style=&quot;color:#2ecc71;&quot;&gt;&lt;em&gt;&lt;strong&gt;&lt;span style=&quot;background-color:#dddddd;&quot;&gt;Test Home Page r4&lt;/span&gt;&lt;/strong&gt;&lt;/em&gt;&lt;/span&gt;&lt;/span&gt;&lt;/span&gt;&lt;img alt=&quot;&quot; src=&quot;/img/tf2-engie-5z1GLRRp.png&quot; style=&quot;width: 72px; height: 72px;&quot; /&gt;&lt;/p&gt;\n',	'active',	'0',	'0',	'2019-03-26 21:22:15',	'2019-03-26 21:22:15',	'0000-00-00 00:00:00'),
(459,	'a3a8dd27635e9346a689629dfcdf1c5242736451d194d74080bd16cdaeb17874b682ad0de56fbaf00ecc5be570979c6f1961e3f1d9f8d8f4751aa0dd0c2d75a6',	'',	'',	'',	'index,follow',	'&lt;p&gt;&lt;span style=&quot;font-family:Courier New,Courier,monospace;&quot;&gt;&lt;span style=&quot;font-size:22px;&quot;&gt;&lt;span style=&quot;color:#2ecc71;&quot;&gt;&lt;em&gt;&lt;strong&gt;&lt;span style=&quot;background-color:#dddddd;&quot;&gt;Test Home Page r4&lt;/span&gt;&lt;/strong&gt;&lt;/em&gt;&lt;/span&gt;&lt;/span&gt;&lt;/span&gt;&lt;img alt=&quot;&quot; src=&quot;/img/tf2-engie-5z1GLRRp.png&quot; style=&quot;width: 72px; height: 72px;&quot; /&gt;&lt;img alt=&quot;&quot; src=&quot;/img/sbsp-DALuE9ts.png&quot; style=&quot;width: 280px; height: 387px;&quot; /&gt;&lt;/p&gt;\n',	'active',	'0',	'0',	'2019-05-12 14:01:51',	'2019-05-12 14:01:51',	'0000-00-00 00:00:00'),
(460,	'12f399564d3635c7b447387e12aae1195ec73dd56e14731bd73d6fc724bd4071a8d48f2d7b6a05abe07686c1a9a41d06be3940d2aa3c6c763c52c997c9716948',	'Test',	'Test',	'Nah',	'index,follow',	'&lt;p&gt;testtesttest&lt;/p&gt;\n',	'active',	'0',	'0',	'2019-05-12 15:45:39',	'2019-05-12 15:45:39',	'0000-00-00 00:00:00'),
(461,	'6a4ad0173a12a575f34e88e2ed2e96c7981625182648b6a0979293771d88644534e3b6dcdffc28a818b19228de5bb3ed86f8f8cedc8936c53564dfecf37459cb',	'Test',	'Test',	'Nah',	'index,follow',	'&lt;p&gt;testtesttest&lt;/p&gt;\n',	'active',	'0',	'0',	'2019-05-12 16:06:37',	'2019-05-12 16:06:37',	'0000-00-00 00:00:00'),
(462,	'e849d1af003e47dbb627d992852acab14be70d7dc40bb4982476eb8088f4789ca372a2c6420f383097bcf746b8935825cd48948eeb5298109728b60b89ed1b48',	'',	'',	'',	'index,follow',	'&lt;p&gt;&lt;span style=&quot;font-family:Courier New,Courier,monospace;&quot;&gt;&lt;span style=&quot;font-size:22px;&quot;&gt;&lt;span style=&quot;color:#2ecc71;&quot;&gt;&lt;em&gt;&lt;strong&gt;&lt;span style=&quot;background-color:#dddddd;&quot;&gt;Test Home Page r4&lt;/span&gt;&lt;/strong&gt;&lt;/em&gt;&lt;/span&gt;&lt;/span&gt;&lt;/span&gt;&lt;img alt=&quot;&quot; src=&quot;/img/tf2-engie-5z1GLRRp.png&quot; style=&quot;width: 72px; height: 72px;&quot; /&gt;&lt;img alt=&quot;&quot; src=&quot;/img/sbsp-DALuE9ts.png&quot; style=&quot;width: 280px; height: 387px;&quot; /&gt;&lt;/p&gt;\n',	'active',	'1',	'0',	'2019-05-18 17:45:14',	'2019-05-18 17:45:14',	'0000-00-00 00:00:00'),
(463,	'2cd505955bb2aa41a64f12c5f249aaea9c9317fc407f450a16a4f42d840a4f46321348278aa8a536010684c73c5e5d181934923b2c1b512567afa1a620e6197f',	'Test',	'Test',	'Nah',	'index,follow',	'&lt;p&gt;testtesttest&lt;/p&gt;\n',	'active',	'1',	'0',	'2019-05-18 17:46:03',	'2019-05-18 17:46:03',	'0000-00-00 00:00:00'),
(464,	'c8107aba662196fa875eff43f97bf94b89d4609d874443524c9d54db09ba287d43ab9385bbc2a70cd000ec220f81c2122fbfbce42e6125ca2b788e0cc8b28c44',	'Test',	'Test',	'Nah',	'index,follow',	'&lt;p&gt;testtesttest&lt;/p&gt;\n',	'active',	'1',	'0',	'2019-05-18 17:53:33',	'2019-05-18 17:53:33',	'0000-00-00 00:00:00'),
(465,	'3e6d3767ef0ea4929f159f1b629086ce7aaa16b6623695555a56c51eb3b2b0183cbd7d7e014aa37f06064eacfd0b0b10177add33a93ac5aaf5a2c0cc5f50f3c9',	'Test',	'Test',	'Nah',	'index,follow',	'&lt;p&gt;testtesttest&lt;/p&gt;\n',	'active',	'1',	'0',	'2019-05-18 17:56:48',	'2019-05-18 17:56:48',	'0000-00-00 00:00:00'),
(466,	'44b72f6ff41efe0ddc71e5ba624c5d8e1348d85d62250f473e086f234a0c18ffa89c1a511d29266d435ab637ada1c0b718e483c90d635358f132b05c158060d3',	'Test',	'Test',	'Nah',	'index,follow',	'&lt;p&gt;testtesttest&lt;/p&gt;\n',	'active',	'1',	'0',	'2019-05-18 18:18:16',	'2019-05-18 18:18:16',	'0000-00-00 00:00:00'),
(467,	'c1f753601dd24169f5320a95549d1eb8a96b4b75f1af78cb67314b054c10887cfbf671f1f67da00c6e23e7ea98d5bd1575984d2eb5fd49d61c46e04960e33baf',	'',	'',	'',	'index,follow',	'&lt;p&gt;{$foo = [1,2,3]} {foreach from=$foo item=f}&lt;/p&gt;\n\n&lt;p&gt;{$f}&lt;/p&gt;\n\n&lt;p&gt;{/foreach}&lt;/p&gt;\n\n&lt;p&gt;Â &lt;/p&gt;\n\n&lt;p&gt;{$date}&lt;/p&gt;\n',	'active',	'0',	'0',	'2019-05-19 22:35:27',	'2019-05-19 22:35:27',	'0000-00-00 00:00:00');

CREATE TABLE `page_iteration_commits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_master_uid` varchar(36) COLLATE utf8_unicode_ci NOT NULL,
  `page_iteration_uid` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `author` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `iteration_description` text COLLATE utf8_unicode_ci,
  `archived` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `archived_datetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `page_iteration_uid_archived_datetime` (`page_iteration_uid`,`archived_datetime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `page_iteration_commits` (`id`, `page_master_uid`, `page_iteration_uid`, `author`, `iteration_description`, `archived`, `created`, `modified`, `archived_datetime`) VALUES
(327,	'ac424fec-e82f-11e8-b856-0242ac120005',	'289e6afa10a5030d5f15b2aea8c10bce4402341c8de3ac7723f23b28f76365e4326bc1fdfc6eebab577ddb0eb740d1bf8280d1a869f3236f651bdcab647e33ce',	'josh',	'',	'0',	'2018-11-14 17:06:48',	'2018-11-14 17:06:48',	'0000-00-00 00:00:00'),
(328,	'c083a12c-e830-11e8-b856-0242ac120005',	'98e31ccef25f9a84ae4fe5206601329bf43c1002116a65304dbb4a554db049d482e48a41926374cc59460235ebe09c1395d60f6a5c20bb9a14f30d2d5dc08ced',	'josh',	'',	'0',	'2018-11-14 17:14:32',	'2018-11-14 17:14:32',	'0000-00-00 00:00:00'),
(329,	'c083a12c-e830-11e8-b856-0242ac120005',	'651c0f332737272f78a946d994d16bb8886e2bbdfee9c03a32d8c005f1b9995d764b40bc85e83e51aae9e3b482ba7a81f96d53db52ad275bfad6a53a0f5865dd',	'josh',	'',	'0',	'2018-11-14 17:15:09',	'2018-11-14 17:15:09',	'0000-00-00 00:00:00'),
(330,	'ac424fec-e82f-11e8-b856-0242ac120005',	'c73ed26a0074491eab9f889e3b3d4f4ffa1140fb72c27b58abe5aa6df9ccc4b55085d0cbe9ae8992d2e0ee15a9e2b174a02295f17f7ebfb8f468d09515d7c262',	'josh',	'',	'0',	'2018-11-14 17:16:42',	'2018-11-14 17:16:42',	'0000-00-00 00:00:00'),
(331,	'ac424fec-e82f-11e8-b856-0242ac120005',	'd575f2dcfac4bc67d981ec654a7749a7974c3b2cde62269800880a9acfa42e2955ede3375c396c5baaab41e12b763d1e348f7338965c01e74316ea68702032f2',	'josh',	'rev 3!',	'0',	'2018-11-14 17:16:53',	'2018-11-14 17:16:53',	'0000-00-00 00:00:00'),
(332,	'ac424fec-e82f-11e8-b856-0242ac120005',	'2436b16bfa0a530d9d9b35376d94742ea94ac7eba00de5cd180c7cf28af0d1bb5576758b0ebae8045dd22c0b64787942b3665b5bf053b973b2d0e175b539858b',	'josh',	'new style, rev updated in cms, new image',	'0',	'2018-11-14 18:07:08',	'2018-11-14 18:07:08',	'0000-00-00 00:00:00'),
(333,	'ac424fec-e82f-11e8-b856-0242ac120005',	'52fdf161be17118782de20df88575d2aa15d2b1843307690916faf727be7661383db05729653a945c65cb3e3182edf3bb709f5b84bf43c2f4af712160532d7c6',	'josh',	'tokenized file upload test',	'0',	'2018-11-14 18:16:07',	'2018-11-14 18:16:07',	'0000-00-00 00:00:00'),
(334,	'a79012ec-e83a-11e8-b856-0242ac120005',	'd4c9f0895442fcc5cbf0feefaa5c4065178fa660ededcc2ca10c3d78c81a6d4acbc9f9aaf7b743ff642a1c576e06d6561e57d58a6e5666dd162a79248c3cf8ad',	'josh',	'',	'0',	'2018-11-14 18:25:25',	'2018-11-14 18:25:25',	'0000-00-00 00:00:00'),
(335,	'a79012ec-e83a-11e8-b856-0242ac120005',	'64f8a35ed9022476a773ade8a15a70b58c62e3d9b50270a40c82aee8c8d1c00fab5e21e03ec3157b0a6861ab016144ac7917d57b15a81d391eabde8a220d2136',	'josh',	'',	'0',	'2018-11-14 18:26:30',	'2018-11-14 18:26:30',	'0000-00-00 00:00:00'),
(336,	'a79012ec-e83a-11e8-b856-0242ac120005',	'51526400db23a01f96c9ee5d3d370249d24a742323a1af195fb71c56f904b1b926b6ec173fbaef8b87e00f6b962a6d34ddb80d96fda13dc3fff1efff9541c939',	'josh',	'',	'0',	'2018-11-14 18:27:19',	'2018-11-14 18:27:19',	'0000-00-00 00:00:00'),
(337,	'a79012ec-e83a-11e8-b856-0242ac120005',	'9202f1882e84c8fb5f8845403e1852ef520a6c1f1e634c0827216e59e92866f53e9f368980f156f36f878e4ede165b2b302b50df5ac606acf909a94b9ec06ee8',	'josh',	'',	'0',	'2018-11-14 18:27:56',	'2018-11-14 18:27:56',	'0000-00-00 00:00:00'),
(338,	'a79012ec-e83a-11e8-b856-0242ac120005',	'4b4bb350b1d9a96995fbfccda3182a7f408c01de8d38b68afb561050f9541ab8dc763e9ff1151f26cd4b2cd3322e787c1236a9c25145a04a0b47329544c0db09',	'josh',	'',	'0',	'2018-11-14 18:28:43',	'2018-11-14 18:28:43',	'0000-00-00 00:00:00'),
(339,	'a79012ec-e83a-11e8-b856-0242ac120005',	'48bfb5b4b1a8bbbe04523433482286b186a800cf4607fec5e0112e268f99f60428d44694aeecbd6edad4b198acc5d60470884182283eac20c1d663b81de6cacf',	'josh',	'',	'0',	'2018-11-14 19:01:35',	'2018-11-14 19:01:35',	'0000-00-00 00:00:00'),
(340,	'a79012ec-e83a-11e8-b856-0242ac120005',	'8bae3060bd01a03d5ac0e8d1f39bf81ab288ef9d0fe5976644824b53bcfbf41c6d7162f6a022032bf342a6755d22e6bdb5de3058f5e7807196dd912aec8bf145',	'josh',	'',	'0',	'2018-11-14 19:02:50',	'2018-11-14 19:02:50',	'0000-00-00 00:00:00'),
(341,	'a79012ec-e83a-11e8-b856-0242ac120005',	'f9976247b1bc6f327dd71bacbe58183d9f8959716d6ce0511dc891dece812b00d8c750096decd9c455ae7953c0d15404a3e6cb69212176479d6bf7c9dc75c0c1',	'josh',	'',	'0',	'2018-11-14 19:03:18',	'2018-11-14 19:03:18',	'0000-00-00 00:00:00'),
(342,	'a79012ec-e83a-11e8-b856-0242ac120005',	'a672c2947da2fff3a36c4987ef98bd856f8413253659d7ad1d3ced871a2360e847ac764039a6b8c39215ec590a0a0efba8a44ef4dcf15bdb94cd271e8a7768f7',	'josh',	'',	'0',	'2018-11-14 19:03:39',	'2018-11-14 19:03:39',	'0000-00-00 00:00:00'),
(343,	'a79012ec-e83a-11e8-b856-0242ac120005',	'44538942a83dbb8485935d713dd3de330ec8fc2478e807a4fd27b23d04cb3662e694de25469b23c094161b24964c206049470e543eb9a0304419e3da10c67f70',	'josh',	'',	'0',	'2018-11-14 19:05:37',	'2018-11-14 19:05:37',	'0000-00-00 00:00:00'),
(344,	'a79012ec-e83a-11e8-b856-0242ac120005',	'f598e44341ff2e02587ab44743ab63bea7fbdd2444199b39a286d51aa7c0ee1f7e96a428a86242f6ec8eb783e0040caa51b5b3bac2aa49269308438426f8ae28',	'josh',	'',	'0',	'2018-11-14 19:06:19',	'2018-11-14 19:06:19',	'0000-00-00 00:00:00'),
(345,	'a79012ec-e83a-11e8-b856-0242ac120005',	'fff21bf12fd272d62cb9988bd6322d4e56e413e8997bfd3ca6910e93dbbf8c6e7fcdd2c0e4a9a9ec730af0779107b326d7bad5940ef9200d4667278158ff4da7',	'josh',	'',	'0',	'2018-11-14 19:07:57',	'2018-11-14 19:07:57',	'0000-00-00 00:00:00'),
(346,	'a79012ec-e83a-11e8-b856-0242ac120005',	'3adf6077c5408f22ee0fc17ceca5975c4cdd357ea01be2f81cf7cf5ec229ddec4643c9e78ed8834da6731057db84c1fbff3e4759d452c6cccafd4a1c9d35603d',	'josh',	'',	'0',	'2018-11-14 19:15:31',	'2018-11-14 19:15:31',	'0000-00-00 00:00:00'),
(347,	'a79012ec-e83a-11e8-b856-0242ac120005',	'6a08b2f3ca184cdba0c3fcfa7a415423e1b913384078b52aedcba6e890be1e9f52e399bf8619ac15004a99d81db02133770c792fba617151c99b735d07c80a15',	'josh',	'',	'0',	'2018-11-14 19:19:32',	'2018-11-14 19:19:32',	'0000-00-00 00:00:00'),
(348,	'a79012ec-e83a-11e8-b856-0242ac120005',	'a8e453944821e5fd7fc8baade4b4c9d672699add97153e8632431571f7ebbceb7106cedc2903df82f4f1b76101cd2162885b5798361de7b021e68e87ea735c29',	'josh',	'',	'0',	'2018-11-14 19:20:14',	'2018-11-14 19:20:14',	'0000-00-00 00:00:00'),
(349,	'a79012ec-e83a-11e8-b856-0242ac120005',	'a2fbaf625b0ffdca537dc4f68bd2da3c14296950c2c9851cef0b7cafc0cb620f935442b3c5c28c074aaee932cb6e0923059c3c2520d68948a64ce437de0a9724',	'josh',	'',	'0',	'2018-11-14 19:21:29',	'2018-11-14 19:21:29',	'0000-00-00 00:00:00'),
(350,	'a79012ec-e83a-11e8-b856-0242ac120005',	'9184848f3e43f072b910b778ea52ead26cb69b28f95fa64ba11fb77d36eb263e72e6dd428fd373d0a15209986c47a9059845f534031ed7105ef7759ffff3f9c9',	'josh',	'',	'0',	'2018-11-14 19:34:29',	'2018-11-14 19:34:29',	'0000-00-00 00:00:00'),
(351,	'a79012ec-e83a-11e8-b856-0242ac120005',	'065fdc058f5d50c1efe0dac9fe66897e57a9ecfec8551c8201dcd3a092bd7ff293e5fffe07ff57aa2e2ceaa4cafb53fa79bbcd865c39f993068a65f2dfdc1746',	'josh',	'',	'0',	'2018-11-14 19:34:58',	'2018-11-14 19:34:58',	'0000-00-00 00:00:00'),
(352,	'a79012ec-e83a-11e8-b856-0242ac120005',	'5ecb7a7b6ebfaae9ca1e76d318d550fcdea8164652a351fc0329913f24798c4d73561822be09a4bbdd0ebfccadfd1e26d47e066d31b5a50ca6ab16461156b8de',	'josh',	'',	'0',	'2018-11-14 19:37:25',	'2018-11-14 19:37:25',	'0000-00-00 00:00:00'),
(353,	'a79012ec-e83a-11e8-b856-0242ac120005',	'52bc006856a47d06e8d2f1eb87a6435e0d4810a1ae3e7c953706e60d8dcb7e3fa3ec8d1116fbe6e9105e7df572248b8bb30edf6e35790437c37d1c84d264ed4e',	'josh',	'',	'0',	'2018-11-14 19:37:41',	'2018-11-14 19:37:41',	'0000-00-00 00:00:00'),
(354,	'a79012ec-e83a-11e8-b856-0242ac120005',	'd33c75bb17ba7c63bbc60d660f818c89939e6e7542bf102d0e48b6a78945534d16dce4143c833f8f420a43ed83daf356263cb44aac28b2a97600f943c684b8f4',	'josh',	'',	'0',	'2018-11-14 19:41:17',	'2018-11-14 19:41:17',	'0000-00-00 00:00:00'),
(355,	'c083a12c-e830-11e8-b856-0242ac120005',	'd320bdfb2013f20a2d412b17926701fb04793e358edbab76878c8c7676d74f9829e853363589d23647d7360a1a31fa43fe1c9f6f0a55a8e38ae9e2fe505ecc8e',	'josh',	'',	'0',	'2018-11-14 23:10:15',	'2018-11-14 23:10:15',	'0000-00-00 00:00:00'),
(356,	'a79012ec-e83a-11e8-b856-0242ac120005',	'2517dce6e1b0d0a24d3d02db0753cac1f3c8a411cb41c1dd21c4c4ee76c905b5ffeb8998a3eab3ec578ca18f52762332aab578af9888a63a6111f7c0c25f37a0',	'josh',	'',	'0',	'2018-11-14 23:33:43',	'2018-11-14 23:33:43',	'0000-00-00 00:00:00'),
(357,	'a79012ec-e83a-11e8-b856-0242ac120005',	'7cce4416f34e2b823034867b22de57a4cecfce2e4a6bc8466413819fe5c288dd86b79a2954ed3fee37fb05835a70af365e8584158748fc4698134aeb917efefd',	'josh',	'',	'0',	'2018-11-14 23:35:27',	'2018-11-14 23:35:27',	'0000-00-00 00:00:00'),
(358,	'c083a12c-e830-11e8-b856-0242ac120005',	'c5b76148957d4c99c092416ae6da26e4c5b46b3a0b6ec1e960e09110f82519a63ff5d0c8d401cb9c1eaf4cb0607baf1f15f0149217639b4ef531ee0f2a897cd1',	'josh',	'',	'0',	'2018-11-14 23:36:13',	'2018-11-14 23:36:13',	'0000-00-00 00:00:00'),
(359,	'c083a12c-e830-11e8-b856-0242ac120005',	'7248a17791ffe338f8a674b2354bd9f666a3319ddcce79eb9f26f4ff5df4cad64a5a5c6585701d73f07ce7be1c5dc81cd8a0be275867f3f604a867bd42fd47b6',	'josh',	'',	'0',	'2018-11-15 00:14:54',	'2018-11-15 00:14:54',	'0000-00-00 00:00:00'),
(360,	'a79012ec-e83a-11e8-b856-0242ac120005',	'88901cfcf9bcb5efd2baaf36cdf44b75031681589b83334f358d1236ce4d627e14f634ae26d5e20196e1a2ef383c22be1a8664447df284f62e6b5fb829db7c47',	'josh',	'',	'0',	'2018-12-16 18:06:32',	'2018-12-16 18:06:32',	'0000-00-00 00:00:00'),
(361,	'a79012ec-e83a-11e8-b856-0242ac120005',	'96cbe33d1f4c0779255da8f13063085fc25407ff5e2532437526789d8f8a518a78fb4e1c285dbd7f50ef021cc7eacea509f7aa4f5416e2e24510a92411923084',	'josh',	'',	'0',	'2018-12-16 18:07:43',	'2018-12-16 18:07:43',	'0000-00-00 00:00:00'),
(362,	'a79012ec-e83a-11e8-b856-0242ac120005',	'3dd079490ee0b163215008c0405ac43127216c808c0ae00b506e3e5b19410f05c6e264f4805d6a0d72f37c4ce356c72138d2669caa76d8011662295890f51bea',	'josh',	'',	'0',	'2018-12-16 18:08:13',	'2018-12-16 18:08:13',	'0000-00-00 00:00:00'),
(363,	'a79012ec-e83a-11e8-b856-0242ac120005',	'ddf9178d4029050d48acc5fad6e2a875fb2b92c0d617597a359c41d3e5f566d46a01ef5b5b1760743678a01830f1c4ff20957a1e21798fe0cc9738495427bade',	'josh',	'',	'0',	'2018-12-16 21:00:58',	'2018-12-16 21:00:58',	'0000-00-00 00:00:00'),
(364,	'a79012ec-e83a-11e8-b856-0242ac120005',	'2d9ff688ab0181ba14f7a790acd1be72116350132d1c4d2f9e7d4d638ffcf482f36fd68c5186e7229a1061208ab6a64cf91ff9cbc0be7d9142522b8c3db411be',	'josh',	'',	'0',	'2018-12-16 21:01:24',	'2018-12-16 21:01:24',	'0000-00-00 00:00:00'),
(365,	'a79012ec-e83a-11e8-b856-0242ac120005',	'bebfd8c26c778f362d3cc7a3fb7665a06351363c4f601cdfa74bef7bef5ccf87558170bb9c55b18878bf61a57783c7c4508bd9af68b3f324f64897a482c018c3',	'josh',	'',	'0',	'2018-12-16 21:01:37',	'2018-12-16 21:01:37',	'0000-00-00 00:00:00'),
(366,	'a79012ec-e83a-11e8-b856-0242ac120005',	'c1b5f3325b7797c96b89256ea402c60cc601b5b3a65497c93cb4cee092774082c2910fbb1c68c4a92e82eaa822646bca1cd450f11523d90676ee1d913f78fcea',	'josh',	'',	'0',	'2018-12-16 21:03:20',	'2018-12-16 21:03:20',	'0000-00-00 00:00:00'),
(367,	'a79012ec-e83a-11e8-b856-0242ac120005',	'75841854585a97ba743e83eea20c48d16b98d4d9bb67f9daa0e3c641026d39838ff1508fc13d2f38f24e58cedcaaf01eef8ad6a34c0d66d9042d8f2dc4f7e7ab',	'josh',	'',	'0',	'2018-12-16 21:06:00',	'2018-12-16 21:06:00',	'0000-00-00 00:00:00'),
(368,	'a79012ec-e83a-11e8-b856-0242ac120005',	'5cb36e1cc0815d332a8feb1ca24da6a3c55f7a85043a5bcc06ae6896705e995382403d5600a1be98f10743375b180a1a217eeffe62af2bede96adc44cefd07a5',	'josh',	'',	'0',	'2018-12-16 21:06:16',	'2018-12-16 21:06:16',	'0000-00-00 00:00:00'),
(369,	'a79012ec-e83a-11e8-b856-0242ac120005',	'06479cbc013624dc32e9f4a32d8fd1cb9ea06e736b58066ddf639406ea0d10aadf0f219f2e97950dd53e23c2f4cae5df1b72fb27a5cf530742365dab42c54f84',	'josh',	'',	'0',	'2018-12-16 21:18:21',	'2018-12-16 21:18:21',	'0000-00-00 00:00:00'),
(370,	'ac424fec-e82f-11e8-b856-0242ac120005',	'10a34fea5b4fb76100f2fc5754e464e2c1f008651bbf765a3874c4709cb2e8d0b7aa7f02b7c23107e5715254f98dc23f25f2ab41898f46ab9acd6dc7dfc69a87',	'josh',	'',	'0',	'2019-03-26 21:22:15',	'2019-03-26 21:22:15',	'0000-00-00 00:00:00'),
(371,	'ac424fec-e82f-11e8-b856-0242ac120005',	'a3a8dd27635e9346a689629dfcdf1c5242736451d194d74080bd16cdaeb17874b682ad0de56fbaf00ecc5be570979c6f1961e3f1d9f8d8f4751aa0dd0c2d75a6',	'josh',	'',	'0',	'2019-05-12 14:01:51',	'2019-05-12 14:01:51',	'0000-00-00 00:00:00'),
(372,	'fddd2cc2-74cc-11e9-8141-0242ac120005',	'12f399564d3635c7b447387e12aae1195ec73dd56e14731bd73d6fc724bd4071a8d48f2d7b6a05abe07686c1a9a41d06be3940d2aa3c6c763c52c997c9716948',	'josh',	'',	'0',	'2019-05-12 15:45:39',	'2019-05-12 15:45:39',	'0000-00-00 00:00:00'),
(373,	'fddd2cc2-74cc-11e9-8141-0242ac120005',	'6a4ad0173a12a575f34e88e2ed2e96c7981625182648b6a0979293771d88644534e3b6dcdffc28a818b19228de5bb3ed86f8f8cedc8936c53564dfecf37459cb',	'josh',	'',	'0',	'2019-05-12 16:06:37',	'2019-05-12 16:06:37',	'0000-00-00 00:00:00'),
(374,	'ac424fec-e82f-11e8-b856-0242ac120005',	'e849d1af003e47dbb627d992852acab14be70d7dc40bb4982476eb8088f4789ca372a2c6420f383097bcf746b8935825cd48948eeb5298109728b60b89ed1b48',	'josh',	'',	'0',	'2019-05-18 17:45:14',	'2019-05-18 17:45:14',	'0000-00-00 00:00:00'),
(375,	'fddd2cc2-74cc-11e9-8141-0242ac120005',	'2cd505955bb2aa41a64f12c5f249aaea9c9317fc407f450a16a4f42d840a4f46321348278aa8a536010684c73c5e5d181934923b2c1b512567afa1a620e6197f',	'josh',	'',	'0',	'2019-05-18 17:46:03',	'2019-05-18 17:46:03',	'0000-00-00 00:00:00'),
(376,	'fddd2cc2-74cc-11e9-8141-0242ac120005',	'c8107aba662196fa875eff43f97bf94b89d4609d874443524c9d54db09ba287d43ab9385bbc2a70cd000ec220f81c2122fbfbce42e6125ca2b788e0cc8b28c44',	'josh',	'',	'0',	'2019-05-18 17:53:33',	'2019-05-18 17:53:33',	'0000-00-00 00:00:00'),
(377,	'fddd2cc2-74cc-11e9-8141-0242ac120005',	'3e6d3767ef0ea4929f159f1b629086ce7aaa16b6623695555a56c51eb3b2b0183cbd7d7e014aa37f06064eacfd0b0b10177add33a93ac5aaf5a2c0cc5f50f3c9',	'josh',	'',	'0',	'2019-05-18 17:56:48',	'2019-05-18 17:56:48',	'0000-00-00 00:00:00'),
(378,	'fddd2cc2-74cc-11e9-8141-0242ac120005',	'44b72f6ff41efe0ddc71e5ba624c5d8e1348d85d62250f473e086f234a0c18ffa89c1a511d29266d435ab637ada1c0b718e483c90d635358f132b05c158060d3',	'josh',	'',	'0',	'2019-05-18 18:18:16',	'2019-05-18 18:18:16',	'0000-00-00 00:00:00'),
(379,	'c083a12c-e830-11e8-b856-0242ac120005',	'c1f753601dd24169f5320a95549d1eb8a96b4b75f1af78cb67314b054c10887cfbf671f1f67da00c6e23e7ea98d5bd1575984d2eb5fd49d61c46e04960e33baf',	'josh',	'',	'0',	'2019-05-19 22:35:27',	'2019-05-19 22:35:27',	'0000-00-00 00:00:00');

CREATE TABLE `page_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_iteration_uid` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `role_name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `archived` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `archived_datetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `page_iteration_uid_role_name_archived_datetime` (`page_iteration_uid`,`role_name`,`archived_datetime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE `property` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `property` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `archived` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `archived_datetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `property` (`property`,`archived_datetime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `property` (`id`, `property`, `description`, `archived`, `created`, `modified`, `archived_datetime`) VALUES
(1,	'boolean',	'True or false',	'0',	'2018-06-21 04:49:00',	'2018-06-21 04:49:00',	'0000-00-00 00:00:00'),
(2,	'ckeditor',	'Uses CK Editor to manage value',	'0',	'2018-06-21 04:49:19',	'2018-06-24 22:25:57',	'0000-00-00 00:00:00'),
(4,	'codemirror',	'Uses CodeMirror to manage value',	'0',	'2018-06-24 22:26:09',	'2018-06-24 22:26:09',	'0000-00-00 00:00:00');

CREATE TABLE `role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `archived` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `archived_datetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `role_name_archived_datetime` (`role_name`,`archived_datetime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `role` (`id`, `role_name`, `description`, `archived`, `created`, `modified`, `archived_datetime`) VALUES
(1,	'admin',	'Administrator',	'0',	'2018-06-15 19:57:29',	'2018-11-03 16:10:35',	'0000-00-00 00:00:00'),
(33,	'developer',	'Developer access',	'0',	'2018-06-16 03:30:14',	'2018-06-18 19:11:32',	'0000-00-00 00:00:00'),
(35,	'basic',	'Basic user',	'0',	'2018-06-16 04:48:49',	'2018-06-16 04:48:49',	'0000-00-00 00:00:00');

CREATE TABLE `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `display` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `category_key` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `role_based` enum('false','true') COLLATE utf8_unicode_ci DEFAULT 'false',
  `description` text COLLATE utf8_unicode_ci,
  `archived` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `archived_datetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `key` (`key`,`archived_datetime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `settings` (`id`, `key`, `display`, `category_key`, `role_based`, `description`, `archived`, `created`, `modified`, `archived_datetime`) VALUES
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
(38,	'robots_txt_value',	'robots.txt value',	'administrative',	'false',	'The site\'s top level /robots.txt output',	'0',	'2018-11-03 19:16:41',	'2018-11-03 19:23:02',	'0000-00-00 00:00:00'),
(39,	'manage_menu',	'Manage menu links',	'administrative',	'true',	'Manage menu link output',	'0',	'2018-11-03 19:45:15',	'2018-11-03 19:45:15',	'0000-00-00 00:00:00'),
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
  `settings_key` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `property` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `archived` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `archived_datetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `config_key__config_property` (`settings_key`,`property`,`archived_datetime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `settings_properties` (`id`, `settings_key`, `property`, `archived`, `created`, `modified`, `archived_datetime`) VALUES
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
(22,	'manage_menu',	'boolean',	'0',	'2018-11-03 19:45:39',	'2018-11-03 19:45:39',	'0000-00-00 00:00:00'),
(23,	'nav_template',	'codemirror',	'0',	'2018-11-06 15:49:30',	'2018-11-06 15:49:48',	'0000-00-00 00:00:00'),
(24,	'footer_template',	'codemirror',	'0',	'2018-11-06 15:49:30',	'2018-11-06 15:49:48',	'0000-00-00 00:00:00'),
(25,	'add_redirects',	'boolean',	'0',	'2019-05-18 18:41:09',	'2019-05-18 18:41:09',	'0000-00-00 00:00:00'),
(26,	'edit_redirects',	'boolean',	'0',	'2019-05-18 18:43:10',	'2019-05-18 18:43:10',	'0000-00-00 00:00:00'),
(27,	'archive_redirects',	'boolean',	'0',	'2019-05-18 18:43:20',	'2019-05-18 18:43:20',	'0000-00-00 00:00:00'),
(28,	'add_routes',	'boolean',	'0',	'2019-05-19 22:50:33',	'2019-05-19 22:50:33',	'0000-00-00 00:00:00'),
(29,	'edit_routes',	'boolean',	'0',	'2019-05-19 22:50:39',	'2019-05-19 22:50:39',	'0000-00-00 00:00:00'),
(30,	'archive_routes',	'boolean',	'0',	'2019-05-19 22:50:46',	'2019-05-19 22:50:46',	'0000-00-00 00:00:00');

CREATE TABLE `settings_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `settings_key` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `role_name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `archived` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `archived_datetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `config_key_role_name_archived_datetime` (`settings_key`,`role_name`,`archived_datetime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `settings_roles` (`id`, `settings_key`, `role_name`, `archived`, `created`, `modified`, `archived_datetime`) VALUES
(1,	'show_debug',	'admin',	'1',	'2018-07-20 22:14:57',	'2018-07-21 00:30:08',	'2018-07-21 00:30:08'),
(2,	'show_debug',	'developer',	'1',	'2018-07-20 22:14:57',	'2018-07-21 00:30:08',	'2018-07-21 00:30:08'),
(3,	'archive_users',	'admin',	'0',	'2018-07-20 22:16:46',	'2018-07-20 22:16:46',	'0000-00-00 00:00:00'),
(5,	'add_roles',	'admin',	'0',	'2018-07-20 22:16:48',	'2018-07-20 22:16:48',	'0000-00-00 00:00:00'),
(6,	'archive_roles',	'admin',	'0',	'2018-07-20 22:16:49',	'2018-07-20 22:16:49',	'0000-00-00 00:00:00'),
(7,	'edit_roles',	'admin',	'0',	'2018-07-20 22:16:51',	'2018-07-20 22:16:51',	'0000-00-00 00:00:00'),
(8,	'edit_users',	'admin',	'1',	'2018-07-20 22:16:52',	'2018-11-15 17:50:44',	'2018-11-15 17:50:44'),
(11,	'edit_settings',	'admin',	'0',	'2018-07-20 23:12:16',	'2018-07-20 23:51:01',	'2018-07-20 23:38:10'),
(14,	'show_debug',	'admin',	'1',	'2018-07-21 00:30:21',	'2018-10-20 21:25:02',	'2018-10-20 21:25:02'),
(15,	'show_debug',	'developer',	'1',	'2018-07-21 00:30:21',	'2018-10-20 21:25:02',	'2018-10-20 21:25:02'),
(16,	'add_pages',	'admin',	'1',	'2018-08-19 19:29:07',	'2018-10-23 23:59:52',	'2018-10-23 23:59:52'),
(17,	'edit_pages',	'admin',	'1',	'2018-08-19 19:29:16',	'2018-10-24 00:02:12',	'2018-10-24 00:02:12'),
(18,	'archive_pages',	'admin',	'1',	'2018-08-19 19:29:21',	'2018-08-19 19:31:47',	'2018-08-19 19:31:47'),
(19,	'archive_pages',	'admin',	'1',	'2018-08-19 19:31:47',	'2018-10-23 23:59:05',	'2018-10-23 23:59:05'),
(20,	'show_debug',	'admin',	'1',	'2018-10-20 21:25:17',	'2018-10-20 21:25:27',	'2018-10-20 21:25:27'),
(21,	'show_debug',	'developer',	'1',	'2018-10-20 21:25:17',	'2018-10-20 21:25:27',	'2018-10-20 21:25:27'),
(22,	'show_debug',	'admin',	'1',	'2018-10-20 21:25:27',	'2018-10-21 19:31:12',	'2018-10-21 19:31:12'),
(23,	'show_debug',	'developer',	'1',	'2018-10-20 21:25:27',	'2018-10-21 19:31:12',	'2018-10-21 19:31:12'),
(24,	'manage_css',	'admin',	'1',	'2018-10-21 19:24:30',	'2018-10-21 19:24:48',	'2018-10-21 19:24:48'),
(25,	'manage_css',	'developer',	'1',	'2018-10-21 19:24:30',	'2018-10-21 19:24:48',	'2018-10-21 19:24:48'),
(26,	'manage_css',	'basic',	'1',	'2018-10-21 19:24:30',	'2018-10-21 19:24:48',	'2018-10-21 19:24:48'),
(27,	'manage_css',	'admin',	'1',	'2018-10-21 19:24:48',	'2018-10-21 19:24:54',	'2018-10-21 19:24:54'),
(28,	'manage_css',	'developer',	'1',	'2018-10-21 19:24:48',	'2018-10-21 19:24:54',	'2018-10-21 19:24:54'),
(29,	'manage_css',	'developer',	'1',	'2018-10-21 19:24:54',	'2018-10-21 19:24:59',	'2018-10-21 19:24:59'),
(30,	'manage_css',	'admin',	'1',	'2018-10-21 19:25:07',	'2018-10-21 19:25:17',	'2018-10-21 19:25:17'),
(31,	'manage_css',	'developer',	'1',	'2018-10-21 19:25:07',	'2018-10-21 19:25:17',	'2018-10-21 19:25:17'),
(32,	'manage_css',	'basic',	'1',	'2018-10-21 19:25:07',	'2018-10-21 19:25:17',	'2018-10-21 19:25:17'),
(33,	'manage_css',	'admin',	'1',	'2018-10-21 19:25:17',	'2018-11-03 19:21:24',	'2018-11-03 19:21:24'),
(34,	'manage_css',	'developer',	'1',	'2018-10-21 19:25:17',	'2018-11-03 19:21:24',	'2018-11-03 19:21:24'),
(35,	'manage_css',	'basic',	'1',	'2018-10-21 19:25:17',	'2018-11-03 19:21:24',	'2018-11-03 19:21:24'),
(36,	'show_debug',	'admin',	'1',	'2018-10-21 19:31:12',	'2018-11-04 19:10:05',	'2018-11-04 19:10:05'),
(37,	'show_debug',	'developer',	'1',	'2018-10-21 19:31:12',	'2018-11-04 19:10:05',	'2018-11-04 19:10:05'),
(38,	'add_pages',	'admin',	'1',	'2018-10-24 00:02:02',	'2018-10-24 00:05:45',	'2018-10-24 00:05:45'),
(39,	'edit_pages',	'admin',	'1',	'2018-10-24 00:06:01',	'2018-10-24 00:11:10',	'2018-10-24 00:11:10'),
(40,	'add_pages',	'admin',	'1',	'2018-10-24 00:07:05',	'2018-10-24 00:07:18',	'2018-10-24 00:07:18'),
(41,	'add_pages',	'developer',	'1',	'2018-10-24 00:07:05',	'2018-10-24 00:07:18',	'2018-10-24 00:07:18'),
(42,	'add_pages',	'admin',	'1',	'2018-10-24 00:07:18',	'2018-10-24 00:11:07',	'2018-10-24 00:11:07'),
(43,	'add_pages',	'developer',	'1',	'2018-10-24 00:07:18',	'2018-10-24 00:11:07',	'2018-10-24 00:11:07'),
(44,	'add_pages',	'admin',	'0',	'2018-10-24 00:11:07',	'2018-10-24 00:11:07',	'0000-00-00 00:00:00'),
(45,	'add_pages',	'developer',	'0',	'2018-10-24 00:11:07',	'2018-10-24 00:11:07',	'0000-00-00 00:00:00'),
(46,	'edit_pages',	'admin',	'0',	'2018-10-24 00:11:10',	'2018-10-24 00:11:10',	'0000-00-00 00:00:00'),
(47,	'edit_pages',	'developer',	'0',	'2018-10-24 00:11:10',	'2018-10-24 00:11:10',	'0000-00-00 00:00:00'),
(48,	'archive_pages',	'admin',	'1',	'2018-10-24 00:11:15',	'2018-10-27 18:28:11',	'2018-10-27 18:28:11'),
(49,	'archive_pages',	'admin',	'0',	'2018-10-27 18:28:11',	'2018-10-27 18:28:11',	'0000-00-00 00:00:00'),
(50,	'manage_css',	'admin',	'1',	'2018-11-03 19:21:24',	'2018-11-03 19:21:29',	'2018-11-03 19:21:29'),
(51,	'manage_css',	'developer',	'1',	'2018-11-03 19:21:24',	'2018-11-03 19:21:29',	'2018-11-03 19:21:29'),
(52,	'manage_css',	'admin',	'0',	'2018-11-03 19:21:29',	'2018-11-03 19:21:29',	'0000-00-00 00:00:00'),
(53,	'manage_css',	'developer',	'0',	'2018-11-03 19:21:29',	'2018-11-03 19:21:29',	'0000-00-00 00:00:00'),
(54,	'manage_css',	'basic',	'0',	'2018-11-03 19:21:29',	'2018-11-03 19:21:29',	'0000-00-00 00:00:00'),
(55,	'manage_menu',	'admin',	'1',	'2018-11-03 19:46:42',	'2018-11-14 22:25:05',	'2018-11-14 22:25:05'),
(56,	'manage_menu',	'developer',	'1',	'2018-11-03 19:46:42',	'2018-11-14 22:25:05',	'2018-11-14 22:25:05'),
(57,	'show_debug',	'admin',	'0',	'2018-11-04 19:10:05',	'2018-11-04 19:10:05',	'0000-00-00 00:00:00'),
(58,	'show_debug',	'developer',	'0',	'2018-11-04 19:10:05',	'2018-11-04 19:10:05',	'0000-00-00 00:00:00'),
(59,	'show_debug',	'basic',	'0',	'2018-11-04 19:10:05',	'2018-11-04 19:10:05',	'0000-00-00 00:00:00'),
(60,	'manage_menu',	'admin',	'1',	'2018-11-14 22:25:05',	'2018-11-14 22:25:25',	'2018-11-14 22:25:25'),
(61,	'manage_menu',	'developer',	'1',	'2018-11-14 22:25:05',	'2018-11-14 22:25:25',	'2018-11-14 22:25:25'),
(62,	'manage_menu',	'admin',	'1',	'2018-11-14 22:25:25',	'2018-11-14 22:38:42',	'2018-11-14 22:38:42'),
(63,	'manage_menu',	'developer',	'1',	'2018-11-14 22:25:25',	'2018-11-14 22:38:42',	'2018-11-14 22:38:42'),
(64,	'manage_menu',	'admin',	'0',	'2018-11-14 22:38:42',	'2018-11-14 22:38:42',	'0000-00-00 00:00:00'),
(65,	'manage_menu',	'developer',	'0',	'2018-11-14 22:38:42',	'2018-11-14 22:38:42',	'0000-00-00 00:00:00'),
(66,	'edit_users',	'basic',	'1',	'2018-11-15 17:54:58',	'2018-11-15 17:55:17',	'2018-11-15 17:55:17'),
(67,	'edit_users',	'admin',	'1',	'2018-11-15 17:55:17',	'2018-11-15 17:55:28',	'2018-11-15 17:55:28'),
(68,	'edit_users',	'developer',	'1',	'2018-11-15 17:55:17',	'2018-11-15 17:55:28',	'2018-11-15 17:55:28'),
(69,	'edit_users',	'basic',	'1',	'2018-11-15 17:55:17',	'2018-11-15 17:55:28',	'2018-11-15 17:55:28'),
(70,	'edit_users',	'admin',	'1',	'2018-11-15 17:55:28',	'2018-11-15 17:55:35',	'2018-11-15 17:55:35'),
(71,	'edit_users',	'developer',	'1',	'2018-11-15 17:55:28',	'2018-11-15 17:55:35',	'2018-11-15 17:55:35'),
(72,	'edit_users',	'basic',	'1',	'2018-11-15 17:55:35',	'2018-11-15 17:56:05',	'2018-11-15 17:56:05'),
(73,	'edit_users',	'developer',	'1',	'2018-11-15 17:56:05',	'2018-11-15 17:56:24',	'2018-11-15 17:56:24'),
(74,	'edit_users',	'basic',	'1',	'2018-11-15 17:56:05',	'2018-11-15 17:56:24',	'2018-11-15 17:56:24'),
(75,	'edit_users',	'basic',	'1',	'2018-11-15 17:56:24',	'2018-11-15 17:58:24',	'2018-11-15 17:58:24'),
(76,	'edit_users',	'admin',	'0',	'2018-11-15 17:58:24',	'2018-11-15 17:58:24',	'0000-00-00 00:00:00'),
(77,	'edit_users',	'developer',	'0',	'2018-11-15 17:58:24',	'2018-11-15 17:58:24',	'0000-00-00 00:00:00'),
(78,	'add_redirects',	'admin',	'1',	'2019-05-18 18:43:35',	'2019-05-19 16:05:28',	'2019-05-19 16:05:28'),
(79,	'add_redirects',	'developer',	'1',	'2019-05-18 18:43:35',	'2019-05-19 16:05:28',	'2019-05-19 16:05:28'),
(80,	'edit_redirects',	'admin',	'1',	'2019-05-18 18:43:36',	'2019-05-19 16:05:03',	'2019-05-19 16:05:03'),
(81,	'edit_redirects',	'developer',	'1',	'2019-05-18 18:43:36',	'2019-05-19 16:05:03',	'2019-05-19 16:05:03'),
(82,	'archive_redirects',	'admin',	'1',	'2019-05-18 18:43:37',	'2019-05-19 16:05:15',	'2019-05-19 16:05:15'),
(83,	'archive_redirects',	'developer',	'1',	'2019-05-18 18:43:37',	'2019-05-19 16:05:15',	'2019-05-19 16:05:15'),
(84,	'edit_redirects',	'admin',	'1',	'2019-05-19 16:05:41',	'2019-05-19 16:05:57',	'2019-05-19 16:05:57'),
(85,	'edit_redirects',	'developer',	'1',	'2019-05-19 16:05:41',	'2019-05-19 16:05:57',	'2019-05-19 16:05:57'),
(86,	'edit_redirects',	'basic',	'1',	'2019-05-19 16:05:41',	'2019-05-19 16:05:57',	'2019-05-19 16:05:57'),
(87,	'add_redirects',	'admin',	'0',	'2019-05-19 16:05:57',	'2019-05-19 16:05:57',	'0000-00-00 00:00:00'),
(88,	'add_redirects',	'developer',	'0',	'2019-05-19 16:05:57',	'2019-05-19 16:05:57',	'0000-00-00 00:00:00'),
(89,	'add_redirects',	'basic',	'0',	'2019-05-19 16:05:57',	'2019-05-19 16:05:57',	'0000-00-00 00:00:00'),
(90,	'edit_redirects',	'admin',	'0',	'2019-05-19 16:05:57',	'2019-05-19 16:05:57',	'0000-00-00 00:00:00'),
(91,	'edit_redirects',	'developer',	'0',	'2019-05-19 16:05:57',	'2019-05-19 16:05:57',	'0000-00-00 00:00:00'),
(92,	'edit_redirects',	'basic',	'0',	'2019-05-19 16:05:57',	'2019-05-19 16:05:57',	'0000-00-00 00:00:00'),
(93,	'archive_redirects',	'admin',	'0',	'2019-05-19 16:05:58',	'2019-05-19 16:05:58',	'0000-00-00 00:00:00'),
(94,	'archive_redirects',	'developer',	'0',	'2019-05-19 16:05:58',	'2019-05-19 16:05:58',	'0000-00-00 00:00:00'),
(95,	'archive_redirects',	'basic',	'0',	'2019-05-19 16:05:58',	'2019-05-19 16:05:58',	'0000-00-00 00:00:00'),
(96,	'archive_routes',	'admin',	'1',	'2019-05-19 22:51:38',	'2019-05-19 22:51:38',	'2019-05-19 22:51:38'),
(97,	'archive_routes',	'developer',	'1',	'2019-05-19 22:51:38',	'2019-05-19 22:51:38',	'2019-05-19 22:51:38'),
(98,	'archive_routes',	'admin',	'1',	'2019-05-19 22:51:39',	'2019-05-19 22:51:39',	'2019-05-19 22:51:39'),
(99,	'archive_routes',	'developer',	'1',	'2019-05-19 22:51:39',	'2019-05-19 22:51:39',	'2019-05-19 22:51:39'),
(100,	'archive_routes',	'admin',	'0',	'2019-05-19 22:51:39',	'2019-05-19 22:51:39',	'0000-00-00 00:00:00'),
(101,	'archive_routes',	'developer',	'0',	'2019-05-19 22:51:39',	'2019-05-19 22:51:39',	'0000-00-00 00:00:00'),
(102,	'edit_routes',	'admin',	'1',	'2019-05-19 22:51:39',	'2019-05-19 22:51:39',	'2019-05-19 22:51:39'),
(103,	'edit_routes',	'developer',	'1',	'2019-05-19 22:51:39',	'2019-05-19 22:51:39',	'2019-05-19 22:51:39'),
(104,	'edit_routes',	'admin',	'1',	'2019-05-19 22:51:39',	'2019-05-19 22:51:40',	'2019-05-19 22:51:40'),
(105,	'edit_routes',	'developer',	'1',	'2019-05-19 22:51:39',	'2019-05-19 22:51:40',	'2019-05-19 22:51:40'),
(106,	'edit_routes',	'admin',	'0',	'2019-05-19 22:51:40',	'2019-05-19 22:51:40',	'0000-00-00 00:00:00'),
(107,	'edit_routes',	'developer',	'0',	'2019-05-19 22:51:40',	'2019-05-19 22:51:40',	'0000-00-00 00:00:00'),
(108,	'add_routes',	'admin',	'1',	'2019-05-19 22:51:40',	'2019-05-19 22:51:40',	'2019-05-19 22:51:40'),
(109,	'add_routes',	'developer',	'1',	'2019-05-19 22:51:40',	'2019-05-19 22:51:40',	'2019-05-19 22:51:40'),
(110,	'add_routes',	'admin',	'1',	'2019-05-19 22:51:40',	'2019-05-19 22:51:41',	'2019-05-19 22:51:41'),
(111,	'add_routes',	'developer',	'1',	'2019-05-19 22:51:40',	'2019-05-19 22:51:41',	'2019-05-19 22:51:41'),
(112,	'add_routes',	'admin',	'0',	'2019-05-19 22:51:41',	'2019-05-19 22:51:41',	'0000-00-00 00:00:00'),
(113,	'add_routes',	'developer',	'0',	'2019-05-19 22:51:41',	'2019-05-19 22:51:41',	'0000-00-00 00:00:00');

CREATE TABLE `settings_values` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `settings_key` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `value` text COLLATE utf8_unicode_ci NOT NULL,
  `configuration_scheme` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `archived` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `archived_datetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_key_configuration_scheme_archived_datetime` (`settings_key`,`configuration_scheme`,`archived_datetime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `settings_values` (`id`, `settings_key`, `value`, `configuration_scheme`, `archived`, `created`, `modified`, `archived_datetime`) VALUES
(1,	'web_url',	'//gaseous.local',	'dev',	'0',	'2017-10-21 03:43:54',	'2018-06-09 17:40:26',	'2018-04-14 05:05:35'),
(2,	'web_url',	'//stage.gaseo.us',	'stage',	'0',	'2017-10-21 03:43:54',	'2018-06-09 17:42:38',	'2018-04-14 05:05:35'),
(3,	'web_url',	'//gaseo.us',	'prod',	'0',	'2017-10-21 03:43:54',	'2018-06-09 17:42:16',	'2018-04-14 05:05:35'),
(4,	'cookie_domain',	'.gaseous.local',	'dev',	'0',	'2017-10-21 03:43:54',	'2018-06-09 17:36:44',	'2018-04-14 05:05:35'),
(5,	'cookie_domain',	'.stage.gaseo.us',	'stage',	'0',	'2017-10-21 03:43:54',	'2018-06-09 17:43:02',	'2018-04-14 05:05:35'),
(6,	'cookie_domain',	'.gaseo.us',	'prod',	'0',	'2017-10-21 03:43:54',	'2018-06-09 17:43:17',	'2018-04-14 05:05:35'),
(7,	'enable_template_caching',	'false',	'dev',	'0',	'2017-10-21 03:43:54',	'2018-04-15 08:47:08',	'2018-04-14 05:05:35'),
(8,	'enable_template_caching',	'true',	'stage',	'0',	'2017-10-21 03:43:54',	'2018-04-15 08:47:08',	'2018-04-14 05:05:35'),
(9,	'enable_template_caching',	'true',	'prod',	'0',	'2017-10-21 03:43:54',	'2018-04-15 08:47:08',	'2018-04-14 05:05:35'),
(10,	'enable_ssl',	'false',	'dev',	'0',	'2017-10-21 03:43:54',	'2018-04-15 08:47:08',	'2018-04-14 05:05:35'),
(11,	'enable_ssl',	'false',	'stage',	'0',	'2017-10-21 03:43:54',	'2018-04-15 08:47:08',	'2018-04-14 05:05:35'),
(12,	'enable_ssl',	'true',	'prod',	'0',	'2017-10-21 03:43:54',	'2018-04-15 08:47:08',	'2018-04-14 05:05:35'),
(13,	'recaptcha_public_key',	'6LflD3QUAAAAAO_W89Q8bzlg4s0panQNKU_0qNnr',	'dev',	'0',	'2017-10-21 03:43:54',	'2018-10-08 17:48:04',	'2018-04-14 05:05:35'),
(14,	'recaptcha_public_key',	'false',	'stage',	'0',	'2017-10-21 03:43:54',	'2018-04-15 08:47:08',	'2018-04-14 05:05:35'),
(15,	'recaptcha_public_key',	'false',	'prod',	'0',	'2017-10-21 03:43:54',	'2018-04-15 08:47:08',	'2018-04-14 05:05:35'),
(16,	'recaptcha_private_key',	'6LflD3QUAAAAAEXbDnLaEDJCFec0HN77WCVyKexk',	'dev',	'0',	'2017-10-21 03:43:54',	'2018-10-08 17:48:06',	'2018-04-14 05:05:35'),
(17,	'recaptcha_private_key',	'false',	'stage',	'0',	'2017-10-21 03:43:54',	'2018-04-15 08:47:08',	'2018-04-14 05:05:35'),
(18,	'recaptcha_private_key',	'false',	'prod',	'0',	'2017-10-21 03:43:54',	'2018-04-15 08:47:08',	'2018-04-14 05:05:35'),
(19,	'show_debug',	'1',	'dev',	'0',	'2017-10-21 03:43:54',	'2018-10-20 21:25:27',	'2018-04-14 05:05:35'),
(20,	'show_debug',	'false',	'stage',	'0',	'2017-10-21 03:43:54',	'2018-06-10 06:51:38',	'2018-04-14 05:05:35'),
(21,	'show_debug',	'false',	'prod',	'0',	'2017-10-21 03:43:54',	'2018-06-10 06:52:25',	'2018-04-14 05:05:35'),
(22,	'maintenance_mode',	'0',	'dev',	'0',	'2017-10-21 03:43:54',	'2018-07-20 22:29:19',	'2018-04-14 05:05:35'),
(23,	'maintenance_mode',	'false',	'stage',	'0',	'2017-10-21 03:43:54',	'2018-04-15 08:47:08',	'2018-04-14 05:05:35'),
(24,	'maintenance_mode',	'false',	'prod',	'0',	'2017-10-21 03:43:54',	'2018-04-15 08:47:08',	'2018-04-14 05:05:35'),
(25,	'require_recaptcha',	'1',	'dev',	'0',	'2017-10-21 03:43:54',	'2018-10-21 19:28:41',	'2018-04-14 05:05:35'),
(26,	'require_recaptcha',	'false',	'stage',	'0',	'2017-10-21 03:43:54',	'2018-04-15 08:47:08',	'2018-04-14 05:05:35'),
(27,	'require_recaptcha',	'false',	'prod',	'0',	'2017-10-21 03:43:54',	'2018-04-15 08:47:08',	'2018-04-14 05:05:35'),
(28,	'login_cookie_expire_days',	'7',	'dev',	'0',	'2017-10-21 03:43:54',	'2018-04-15 08:47:08',	'2018-04-14 05:05:35'),
(29,	'login_cookie_expire_days',	'7',	'stage',	'0',	'2017-10-21 03:43:54',	'2018-04-15 08:47:08',	'2018-04-14 05:05:35'),
(30,	'login_cookie_expire_days',	'7',	'prod',	'0',	'2017-10-21 03:43:54',	'2018-04-15 08:47:08',	'2018-04-14 05:05:35'),
(31,	'dev_alert_email',	'mail@joshlrogers.com',	'dev',	'0',	'2017-10-21 03:43:54',	'2018-04-15 08:47:08',	'2018-04-14 05:05:35'),
(32,	'dev_alert_email',	'mail@joshlrogers.com',	'stage',	'0',	'2017-10-21 03:43:54',	'2018-04-15 08:47:08',	'2018-04-14 05:05:35'),
(33,	'dev_alert_email',	'mail@joshlrogers.com',	'prod',	'0',	'2017-10-21 03:43:54',	'2018-04-15 08:47:08',	'2018-04-14 05:05:35'),
(34,	'smtp_host',	'172.18.0.6',	'dev',	'0',	'2017-10-21 03:43:54',	'2018-11-18 22:18:09',	'2018-04-14 05:05:35'),
(35,	'smtp_host',	'172.31.4.210',	'stage',	'0',	'2017-10-21 03:43:54',	'2018-04-15 08:47:08',	'2018-04-14 05:05:35'),
(36,	'smtp_host',	'localhost',	'prod',	'0',	'2017-10-21 03:43:54',	'2018-04-15 08:47:08',	'2018-04-14 05:05:35'),
(37,	'smtp_port',	'25',	'dev',	'0',	'2017-10-21 03:43:54',	'2018-11-17 22:39:40',	'2018-04-14 05:05:35'),
(38,	'smtp_port',	'25',	'stage',	'0',	'2017-10-21 03:43:54',	'2018-04-15 08:47:08',	'2018-04-14 05:05:35'),
(39,	'smtp_port',	'25',	'prod',	'0',	'2017-10-21 03:43:54',	'2018-04-15 08:47:08',	'2018-04-14 05:05:35'),
(40,	'smtp_user',	'root',	'dev',	'0',	'2017-10-21 03:43:54',	'2018-11-18 22:12:31',	'2018-04-14 05:05:35'),
(41,	'smtp_user',	'mail_buddy',	'stage',	'0',	'2017-10-21 03:43:54',	'2018-04-15 08:47:08',	'2018-04-14 05:05:35'),
(42,	'smtp_user',	'root',	'prod',	'0',	'2017-10-21 03:43:54',	'2018-04-15 08:47:08',	'2018-04-14 05:05:35'),
(43,	'smtp_password',	'',	'dev',	'0',	'2017-10-21 03:43:54',	'2018-11-18 22:12:32',	'2018-04-14 05:05:35'),
(44,	'smtp_password',	'M@iL__BuDdY',	'stage',	'0',	'2017-10-21 03:43:54',	'2018-04-15 08:47:08',	'2018-04-14 05:05:35'),
(45,	'smtp_password',	'localhost',	'prod',	'0',	'2017-10-21 03:43:54',	'2018-04-15 08:47:08',	'2018-04-14 05:05:35'),
(46,	'php_error_reporting_level',	'-1',	'dev',	'0',	'2017-11-12 21:39:44',	'2018-04-15 08:47:08',	'2018-04-14 05:05:35'),
(47,	'php_error_reporting_level',	'0',	'stage',	'0',	'2017-11-12 21:40:32',	'2018-04-15 08:47:08',	'2018-04-14 05:05:35'),
(48,	'php_error_reporting_level',	'0',	'prod',	'0',	'2017-11-12 21:41:41',	'2018-04-15 08:47:08',	'2018-04-14 05:05:35'),
(49,	'registration_access_code',	'123456',	'dev',	'0',	'2018-06-09 17:25:05',	'2018-06-10 05:01:09',	'0000-00-00 00:00:00'),
(50,	'main_template',	'&lt;!DOCTYPE html&gt;&lt;html xmlns=&quot;http://www.w3.org/1999/xhtml&quot;&gt;\r\n&lt;head&gt;\r\n    &lt;meta name=&quot;description&quot; content=&quot;{$meta_description}&quot; /&gt;\r\n    &lt;meta charset=&quot;UTF-8&quot; /&gt;\r\n    &lt;meta name=&quot;viewport&quot; content=&quot;width=device-width, initial-scale=1.0&quot; /&gt;\r\n    &lt;meta name=&quot;robots&quot; content=&quot;{$meta_robots}&quot; /&gt;\r\n    &lt;title&gt;{$page_title_seo}&lt;/title&gt;\r\n    &lt;style&gt;{$css}&lt;/style&gt;\r\n    &lt;link href=&quot;https://fonts.googleapis.com/css?family=Open+Sans&quot; rel=&quot;stylesheet&quot;&gt; \r\n    &lt;!--&lt;link href=&quot;//fonts.googleapis.com/css?family=Titillium+Web&quot; rel=&quot;stylesheet&quot; type=&quot;text/css&quot; /&gt;--&gt;\r\n    &lt;link href=&quot;/styles.gz.css&quot; rel=&quot;stylesheet&quot; /&gt;\r\n    &lt;link rel=&quot;shortcut icon&quot; href=&quot;https://www.joshlrogers.com/assets/img/favicon.ico&quot;&gt;\r\n&lt;/head&gt;\r\n&lt;body itemscope=&quot;itemscope&quot; itemtype=&quot;http://schema.org/WebPage&quot;&gt;\r\n&lt;nav&gt;\r\n	{$nav}\r\n&lt;/nav&gt;\r\n&lt;main&gt;\r\n    &lt;div class=&quot;page&quot; id=&quot;container&quot;&gt;\r\n        &lt;div class=&quot;page&quot; id=&quot;content&quot;&gt;\r\n            &lt;div&gt;\r\n            	{if !empty({$page_title_h1})}\r\n	                &lt;h1&gt;{$page_title_h1}&lt;/h1&gt;\r\n                {/if}\r\n                {$breadcrumbs}\r\n            &lt;/div&gt;\r\n            &lt;div&gt;\r\n                {$body}\r\n            &lt;/div&gt;\r\n            &lt;div style=&quot;clear:both;&quot;&gt;\r\n                \r\n            &lt;/div&gt;\r\n        &lt;/div&gt;\r\n    &lt;/div&gt;\r\n&lt;/main&gt;\r\n&lt;footer&gt;\r\n	{$footer}\r\n&lt;/footer&gt;\r\n{$debug_footer}\r\n&lt;/body&gt;\r\n&lt;!--&lt;link rel=&quot;stylesheet&quot; href=&quot;https://use.fontawesome.com/releases/v5.4.1/css/all.css&quot; integrity=&quot;sha384-5sAR7xN1Nv6T6+dT2mhtzEpVJvfS3NScPQTrOxhwjIuvcA67KV2R5Jz6kr4abQsz&quot; crossorigin=&quot;anonymous&quot;&gt;--&gt;\r\n&lt;script async=&quot;async&quot; defer=&quot;defer&quot;&gt;\r\n    {$js}\r\n&lt;/script&gt;\r\n&lt;/html&gt;',	NULL,	'0',	'2018-06-10 17:58:41',	'2018-11-15 19:27:36',	'0000-00-00 00:00:00'),
(52,	'http_error_template',	'&lt;div class=&quot;margin_on_top&quot;&gt;\r\n    &lt;p&gt;\r\n        {$error_code} {$error_name}. Please click &lt;a href=&quot;{$full_web_url}&quot;&gt;here&lt;/a&gt; to return home.\r\n    &lt;/p&gt;\r\n&lt;/div&gt;',	NULL,	'0',	'2018-06-10 18:30:53',	'2018-08-26 20:42:45',	'0000-00-00 00:00:00'),
(53,	'test_config',	'123',	NULL,	'0',	'2018-06-13 04:40:08',	'2018-06-13 04:40:20',	'0000-00-00 00:00:00'),
(54,	'another_test',	'987654321',	NULL,	'0',	'2018-06-13 05:08:02',	'2018-06-13 05:08:02',	'0000-00-00 00:00:00'),
(55,	'edit_users',	'1',	NULL,	'0',	'2018-06-18 05:11:22',	'2018-07-08 01:02:16',	'0000-00-00 00:00:00'),
(56,	'edit_roles',	'1',	NULL,	'0',	'2018-06-18 06:01:35',	'2018-07-20 22:16:51',	'0000-00-00 00:00:00'),
(57,	'archive_roles',	'1',	NULL,	'0',	'2018-06-18 06:10:42',	'2018-07-20 22:16:49',	'0000-00-00 00:00:00'),
(58,	'add_roles',	'1',	NULL,	'0',	'2018-06-18 06:14:45',	'2018-07-20 22:16:48',	'0000-00-00 00:00:00'),
(59,	'edit_settings',	'1',	NULL,	'0',	'2018-06-18 19:18:43',	'2018-07-20 23:08:43',	'0000-00-00 00:00:00'),
(60,	'archive_users',	'1',	NULL,	'0',	'2018-06-20 05:19:15',	'2018-07-20 22:16:46',	'0000-00-00 00:00:00'),
(61,	'pdo_debug',	'1',	'dev',	'0',	'2018-07-06 22:54:25',	'2018-07-06 22:54:25',	'0000-00-00 00:00:00'),
(62,	'add_pages',	'1',	NULL,	'0',	'2018-08-19 19:20:58',	'2018-10-24 00:07:18',	'0000-00-00 00:00:00'),
(63,	'edit_pages',	'1',	NULL,	'0',	'2018-08-19 19:21:10',	'2018-10-24 00:06:01',	'0000-00-00 00:00:00'),
(64,	'archive_pages',	'1',	NULL,	'0',	'2018-08-19 19:21:17',	'2018-10-27 18:28:11',	'0000-00-00 00:00:00'),
(65,	'upload_root',	'/usr/local/apache2/htdocs/gaseous-images',	NULL,	'0',	'2018-09-21 01:48:14',	'2018-09-21 01:59:20',	'0000-00-00 00:00:00'),
(66,	'upload_url_relative',	'/img',	NULL,	'0',	'2018-09-21 01:49:19',	'2018-09-21 01:59:28',	'0000-00-00 00:00:00'),
(67,	'manage_css',	'1',	NULL,	'0',	'2018-10-21 19:22:57',	'2018-10-21 19:22:57',	'0000-00-00 00:00:00'),
(68,	'robots_txt_value',	'User-agent: *\r\nDisallow:',	'dev',	'0',	'2018-11-03 19:17:16',	'2018-11-03 19:23:43',	'0000-00-00 00:00:00'),
(69,	'manage_menu',	'1',	NULL,	'0',	'2018-11-03 19:46:20',	'2018-11-03 19:46:42',	'0000-00-00 00:00:00'),
(70,	'nav_template',	'    &lt;div class=&quot;page&quot; id=&quot;banner&quot;&gt;\r\n        &lt;script&gt;\r\n            {literal}(function(i,s,o,g,r,a,m){i[\'GoogleAnalyticsObject\']=r;i[r]=i[r]||function(){\r\n                (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),\r\n                m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)\r\n            })(window,document,\'script\',\'//www.google-analytics.com/analytics.js\',\'ga\');\r\n\r\n            ga(\'create\', \'UA-73211801-1\', \'auto\');\r\n            ga(\'set\', \'dimension1\', \'bar3\');\r\n            ga(\'send\', \'pageview\');{/literal}\r\n        &lt;/script&gt;\r\n        &lt;div class=&quot;page&quot; id=&quot;menu_container&quot;&gt;\r\n            &lt;ul id=&quot;menu&quot; class=&quot;page no_bullets inline&quot;&gt;\r\n                &lt;li class=&quot;menu_button&quot;&gt;\r\n                    &lt;a href=&quot;/&quot; target=&quot;_self&quot; title=&quot;Home&quot;&gt;\r\n                        Home\r\n                    &lt;/a&gt;\r\n                &lt;/li&gt;\r\n                &lt;li class=&quot;menu_button&quot;&gt;\r\n                    &lt;a href=&quot;/blog/&quot; target=&quot;_self&quot; title=&quot;Josh\'s Blog&quot;&gt;\r\n                        Blog\r\n                    &lt;/a&gt;\r\n                &lt;/li&gt;\r\n                &lt;li class=&quot;menu_button&quot;&gt;\r\n                    &lt;a href=&quot;/portfolio/&quot; target=&quot;_self&quot; title=&quot;Josh\'s Portfolio&quot;&gt;\r\n                        Portfolio\r\n                    &lt;/a&gt;\r\n                &lt;/li&gt;\r\n                &lt;li class=&quot;menu_button&quot;&gt;\r\n                    &lt;a href=&quot;/about/&quot; target=&quot;_self&quot; title=&quot;Josh\'s Profile&quot;&gt;\r\n                        About Josh\r\n                    &lt;/a&gt;\r\n                &lt;/li&gt;\r\n            &lt;/ul&gt;\r\n        &lt;/div&gt;\r\n        &lt;h2 class=&quot;page&quot; id=&quot;static_title&quot;&gt;\r\n            &lt;a href=&quot;/&quot;&gt;Josh L. Rogers&lt;/a&gt;\r\n        &lt;/h2&gt;\r\n        &lt;div class=&quot;page&quot; id=&quot;menu_icon&quot;&gt;\r\n            &lt;img id=&quot;hamburger_icon&quot; src=&quot;&quot; /&gt;\r\n        &lt;/div&gt;\r\n        &lt;div class=&quot;page&quot; id=&quot;menu_container_mobile&quot; style=&quot;display: none;&quot;&gt;\r\n            &lt;ul id=&quot;menu&quot; class=&quot;page no_bullets inline&quot;&gt;\r\n                &lt;li class=&quot;menu_button&quot;&gt;\r\n                    &lt;a class=&quot;menu_selected&quot; href=&quot;https://www.joshlrogers.com/&quot; target=&quot;_self&quot; title=&quot;Home&quot;&gt;Home&lt;/a&gt;\r\n                &lt;/li&gt;\r\n                &lt;li class=&quot;menu_button&quot;&gt;\r\n                    &lt;a href=&quot;https://www.joshlrogers.com/blog.html&quot; target=&quot;_self&quot; title=&quot;Josh\'s Blog&quot;&gt;Blog&lt;/a&gt;\r\n                &lt;/li&gt;\r\n                &lt;li class=&quot;menu_button&quot;&gt;\r\n                    &lt;a href=&quot;https://www.joshlrogers.com/portfolio/josh.html&quot; target=&quot;_self&quot; title=&quot;Josh\'s Portfolio&quot;&gt;Portfolio&lt;/a&gt;\r\n                &lt;/li&gt;\r\n                &lt;li class=&quot;menu_button&quot;&gt;\r\n                    &lt;a href=&quot;https://www.joshlrogers.com/user/josh.html&quot; target=&quot;_self&quot; title=&quot;Josh\'s Profile&quot;&gt;About Josh&lt;/a&gt;\r\n                &lt;/li&gt;\r\n            &lt;/ul&gt;\r\n        &lt;/div&gt;\r\n    &lt;/div&gt;',	NULL,	'0',	'2018-11-06 15:50:31',	'2018-11-06 15:51:26',	'0000-00-00 00:00:00'),
(71,	'footer_template',	'    &lt;div&gt;\r\n        &lt;div class=&quot;page&quot; id=&quot;footer&quot;&gt;\r\n            Footer goes here...\r\n        &lt;/div&gt;\r\n    &lt;/div&gt;',	NULL,	'0',	'2018-11-06 15:50:38',	'2018-11-06 15:51:47',	'0000-00-00 00:00:00'),
(72,	'add_redirects',	'1',	NULL,	'0',	'2019-05-18 18:39:39',	'2019-05-19 16:05:57',	'0000-00-00 00:00:00'),
(73,	'edit_redirects',	'1',	NULL,	'0',	'2019-05-18 18:39:50',	'2019-05-19 16:05:41',	'0000-00-00 00:00:00'),
(74,	'archive_redirects',	'1',	NULL,	'0',	'2019-05-18 18:39:56',	'2019-05-19 16:05:58',	'0000-00-00 00:00:00'),
(75,	'log_file',	'/usr/local/apache2/logs/app-log-{{today}}.log',	NULL,	'0',	'2019-05-19 22:14:15',	'2019-05-19 22:14:15',	'0000-00-00 00:00:00'),
(76,	'add_routes',	'1',	NULL,	'0',	'2019-05-19 22:51:03',	'2019-05-19 22:51:03',	'0000-00-00 00:00:00'),
(77,	'edit_routes',	'1',	NULL,	'0',	'2019-05-19 22:51:06',	'2019-05-19 22:51:06',	'0000-00-00 00:00:00'),
(78,	'archive_routes',	'1',	NULL,	'0',	'2019-05-19 22:51:12',	'2019-05-19 22:51:12',	'0000-00-00 00:00:00');

CREATE TABLE `token_email` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `token` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `enable_safe_mode` enum('false','true') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'false',
  `archived` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `archived_datetime` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE `uri` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` varchar(36) COLLATE utf8_unicode_ci NOT NULL,
  `uri` text COLLATE utf8_unicode_ci NOT NULL,
  `archived` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `archived_datetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uid_archived_datetime` (`uid`,`archived_datetime`),
  UNIQUE KEY `uri_archived_datetime` (`uri`(1000),`archived_datetime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `uri` (`id`, `uid`, `uri`, `archived`, `created`, `modified`, `archived_datetime`) VALUES
(210,	'ac41cf58-e82f-11e8-b856-0242ac120005',	'/home',	'0',	'2018-11-14 17:06:48',	'2019-05-12 15:47:48',	'0000-00-00 00:00:00'),
(211,	'c082ee45-e830-11e8-b856-0242ac120005',	'/test',	'0',	'2018-11-14 17:14:32',	'2019-05-12 15:51:46',	'0000-00-00 00:00:00'),
(212,	'a78f3c9c-e83a-11e8-b856-0242ac120005',	'/test-email',	'0',	'2018-11-14 18:25:25',	'2019-05-12 15:51:59',	'0000-00-00 00:00:00'),
(213,	'fddbe9ec-74cc-11e9-8141-0242ac120005',	'/testing-uri-changes',	'1',	'2019-05-12 15:45:39',	'2019-05-12 16:06:37',	'2019-05-12 16:06:37'),
(214,	'eb93e248-74cf-11e9-8141-0242ac120005',	'/testing-uri-changes2',	'0',	'2019-05-12 16:06:37',	'2019-05-18 17:54:24',	'0000-00-00 00:00:00'),
(215,	'da446166-7995-11e9-8141-0242ac120005',	'/testing-uri-changes3',	'0',	'2019-05-18 17:53:33',	'2019-05-18 17:53:33',	'0000-00-00 00:00:00'),
(216,	'4ef4b718-7996-11e9-8141-0242ac120005',	'/testing-uri-changes4',	'0',	'2019-05-18 17:56:48',	'2019-05-18 17:56:48',	'0000-00-00 00:00:00'),
(217,	'4e8107e7-7999-11e9-8141-0242ac120005',	'/testing-uri-changes5',	'0',	'2019-05-18 18:18:16',	'2019-05-18 18:18:16',	'0000-00-00 00:00:00');

DELIMITER ;;

CREATE TRIGGER `before_insert_uri` BEFORE INSERT ON `uri` FOR EACH ROW
IF new.uid IS NULL THEN
    SET new.uid = UUID();
  END IF;;

DELIMITER ;

CREATE TABLE `uri_redirects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uri_uid` varchar(36) COLLATE utf8_unicode_ci NOT NULL,
  `destination_url` text COLLATE utf8_unicode_ci NOT NULL,
  `http_status_code` int(3) NOT NULL DEFAULT '301',
  `description` text COLLATE utf8_unicode_ci,
  `archived` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `archived_datetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uri_uid` (`uri_uid`,`archived_datetime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `uri_redirects` (`id`, `uri_uid`, `destination_url`, `http_status_code`, `description`, `archived`, `created`, `modified`, `archived_datetime`) VALUES
(11,	'eb93e248-74cf-11e9-8141-0242ac120005',	'/testing-uri-changes3/',	300,	'hoo ha',	'0',	'2019-05-19 16:04:33',	'2019-05-19 16:04:33',	'0000-00-00 00:00:00'),
(15,	'c082ee45-e830-11e8-b856-0242ac120005',	'test2',	410,	'',	'1',	'2019-05-19 16:22:23',	'2019-05-19 16:37:34',	'2019-05-19 16:37:34'),
(16,	'da446166-7995-11e9-8141-0242ac120005',	'/testing-uri-changes4',	0,	'',	'1',	'2019-05-19 16:36:48',	'2019-05-19 16:36:54',	'2019-05-19 16:36:54'),
(17,	'da446166-7995-11e9-8141-0242ac120005',	'/testing-uri-changes4',	410,	'',	'1',	'2019-05-19 16:36:54',	'2019-05-19 16:36:59',	'2019-05-19 16:36:59'),
(18,	'da446166-7995-11e9-8141-0242ac120005',	'/testing-uri-changes4',	302,	'',	'0',	'2019-05-19 16:36:59',	'2019-05-19 16:36:59',	'0000-00-00 00:00:00'),
(19,	'4ef4b718-7996-11e9-8141-0242ac120005',	'/testing-uri-changes5',	302,	'',	'0',	'2019-05-19 16:38:32',	'2019-05-19 16:38:32',	'0000-00-00 00:00:00');

CREATE TABLE `uri_routes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` varchar(36) COLLATE utf8_unicode_ci DEFAULT NULL,
  `regex_pattern` text COLLATE utf8_unicode_ci NOT NULL,
  `destination_controller` text COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `priority_order` int(11) NOT NULL,
  `archived` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `archived_datetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `regex_pattern_archived_datetime` (`regex_pattern`(100),`archived_datetime`),
  UNIQUE KEY `uid_archived_datetime` (`uid`,`archived_datetime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `uri_routes` (`id`, `uid`, `regex_pattern`, `destination_controller`, `description`, `priority_order`, `archived`, `created`, `modified`, `archived_datetime`) VALUES
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
(704,	'01d0dfd7-7a8e-11e9-8141-0242ac120005',	'/admin/menu/?',	'controllers/admin/menu.php',	'',	15,	'0',	'2019-05-26 14:12:13',	'2019-05-26 14:12:13',	'0000-00-00 00:00:00'),
(705,	'0ac885ff-7a8e-11e9-8141-0242ac120005',	'/admin/routes/?',	'controllers/admin/routes.php',	'',	16,	'0',	'2019-05-26 14:12:13',	'2019-05-26 14:12:13',	'0000-00-00 00:00:00'),
(706,	'000f4947-7a8e-11e9-8141-0242ac120005',	'/admin/pages/?',	'controllers/admin/pages.php',	'',	17,	'0',	'2019-05-26 14:12:13',	'2019-05-26 14:12:13',	'0000-00-00 00:00:00'),
(707,	'fef61358-7a8d-11e9-8141-0242ac120005',	'/admin/users/?',	'controllers/admin/users.php',	'',	18,	'0',	'2019-05-26 14:12:13',	'2019-05-26 14:12:13',	'0000-00-00 00:00:00'),
(708,	'0106c795-7a8e-11e9-8141-0242ac120005',	'/admin/js/?',	'controllers/admin/js.php',	'',	19,	'0',	'2019-05-26 14:12:13',	'2019-05-26 14:12:13',	'0000-00-00 00:00:00'),
(709,	'dc66798a-7a8d-11e9-8141-0242ac120005',	'/admin/redirects/?',	'controllers/admin/redirects.php',	'',	20,	'0',	'2019-05-26 14:12:13',	'2019-05-26 14:12:13',	'0000-00-00 00:00:00'),
(710,	'da9605dc-7c44-11e9-8141-0242ac120005',	'/([\\w\\/\\-]+(\\.html)?)?',	'controllers/cms/index.php?page=$1',	'CMS pages',	21,	'0',	'2019-05-26 14:12:13',	'2019-05-26 14:12:13',	'0000-00-00 00:00:00');

DELIMITER ;;

CREATE TRIGGER `before_insert_uri_routes` BEFORE INSERT ON `uri_routes` FOR EACH ROW
IF new.uid IS NULL THEN
    SET new.uid = UUID();
  END IF;;

DELIMITER ;
