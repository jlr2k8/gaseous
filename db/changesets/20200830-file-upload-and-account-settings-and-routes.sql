INSERT INTO `settings` (`key`, `display`, `category_key`, `role_based`, `description`)
VALUES ('allowed_file_upload_extensions', 'Allowed File Upload Extensions', 'filesystem', 'false', 'Comma-separated list of allowed file uploads in CK Editor and the File Uploader admin panel.');

INSERT INTO `settings_values` (`settings_key`, `value`)
VALUES ('allowed_file_upload_extensions', 'doc,docx,pdf,txt');

INSERT INTO `settings` (`key`, `display`, `category_key`, `role_based`, `description`)
VALUES ('file_uploader', 'File Uploader Admin Panel', 'administrative', 'true', 'Permissions to use general file uploader in admin panel');

INSERT INTO `settings_properties` (`settings_key`, `property`)
VALUES ('file_uploader', 'boolean');

INSERT INTO `settings_values` (`settings_key`, `value`)
VALUES ('file_uploader', '0');

INSERT INTO `uri_routes` (`uid`, `regex_pattern`, `destination_controller`, `description`, `priority_order`)
VALUES ('8b818f0a-eb1e-11ea-9532-0242ac190003', '/admin/upload/?', 'controllers/admin/file-uploader.php', NULL, '3');

INSERT INTO `uri_routes` (`uid`, `regex_pattern`, `destination_controller`, `description`, `priority_order`)
VALUES ('9e792b52-ebc2-11ea-9532-0242ac190003', '/account/?', 'controllers/user/account.php', NULL, '3');