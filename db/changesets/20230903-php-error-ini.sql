-- Adding "IGNORE" for the span of time between the versions where these were set at installation but not yet in
-- changesets. So if these values are already set from a prior installation, then this changeset will have no effect.

INSERT IGNORE INTO `settings` (`key`, `display`, `category_key`, `role_based`, `description`)
VALUES ('error_reporting', 'PHP Error Reporting', 'administrative', 'false', 'PHP configuration for error_reporting');

INSERT IGNORE INTO `settings_values` (`settings_key`, `value`)
VALUES ('error_reporting', '-1');


INSERT IGNORE INTO `settings` (`key`, `display`, `category_key`, `role_based`, `description`)
VALUES ('display_errors', 'PHP Display Errors', 'administrative', 'false', 'PHP configuration for display_errors');

INSERT IGNORE INTO `settings_properties` (`settings_key`, `property`)
VALUES ('display_errors', 'boolean');

INSERT IGNORE INTO `settings_values` (`settings_key`, `value`)
VALUES ('display_errors', '0');


INSERT IGNORE INTO `settings` (`key`, `display`, `category_key`, `role_based`, `description`)
VALUES ('log_errors', 'PHP Log Errors', 'administrative', 'false', 'PHP configuration for log_errors');

INSERT IGNORE INTO `settings_properties` (`settings_key`, `property`)
VALUES ('log_errors', 'boolean');

INSERT IGNORE INTO `settings_values` (`settings_key`, `value`)
VALUES ('log_errors', '1');