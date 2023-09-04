INSERT INTO `settings` (`key`, `display`, `category_key`, `role_based`, `description`)
VALUES ('perform_updates', 'Perform Updates', 'administrative', 'true', 'Allow users to run an update script, which updates the code and database with the latest stable version of Gaseous');

INSERT INTO `settings_properties` (`settings_key`, `property`)
VALUES ('perform_updates', 'boolean');

INSERT INTO `settings_values` (`settings_key`, `value`)
VALUES ('perform_updates', '0');