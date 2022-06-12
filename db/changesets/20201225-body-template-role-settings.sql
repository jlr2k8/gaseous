INSERT INTO `settings` (`key`, `display`, `category_key`, `role_based`, `description`)
VALUES ('add_edit_templates', 'Template Admin Panel - Add and Edit', 'administrative', 'true', 'Permissions to create and modify templates');

INSERT INTO `settings_properties` (`settings_key`, `property`)
VALUES ('add_edit_templates', 'boolean');

INSERT INTO `settings_values` (`settings_key`, `value`)
VALUES ('add_edit_templates', '0');


INSERT INTO `settings` (`key`, `display`, `category_key`, `role_based`, `description`)
VALUES ('archive_templates', 'Template Admin Panel - Archive', 'administrative', 'true', 'Permissions to archive templates');

INSERT INTO `settings_properties` (`settings_key`, `property`)
VALUES ('archive_templates', 'boolean');

INSERT INTO `settings_values` (`settings_key`, `value`)
VALUES ('archive_templates', '0');



INSERT INTO `settings` (`key`, `display`, `category_key`, `role_based`, `description`)
VALUES ('custom_template_root', 'Root folder for custom field type, field, and content body override templates', 'filesystem', 'false', 'Specified root directory that contains potential template override directories and files.');

INSERT INTO `settings_values` (`settings_key`, `value`)
VALUES ('custom_template_root', '');



INSERT INTO `uri_routes` (`uid`, `regex_pattern`, `destination_controller`, `description`, `priority_order`)
VALUES ('92a4153d-43f5-11eb-ad2e-0242ac120004', '/admin/template/?', 'controllers/admin/template.php', NULL, '3');