-- Adding "IGNORE" for the span of time between the versions where these were set at installation but not yet in
-- changesets. So if these values are already set from a prior installation, then this changeset will have no effect.

INSERT IGNORE INTO `settings` (`key`, `display`, `category_key`, `role_based`, `description`)
VALUES ('perform_updates', 'Perform Updates', 'administrative', 'true', 'Allow users to run an update script, which updates the code and database with the latest stable version of Gaseous');

INSERT IGNORE INTO `settings_properties` (`settings_key`, `property`)
VALUES ('perform_updates', 'boolean');

INSERT IGNORE INTO `settings_values` (`settings_key`, `value`)
VALUES ('perform_updates', '0');