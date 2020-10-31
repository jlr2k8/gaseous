INSERT INTO `settings` (`key`, `display`, `category_key`, `role_based`, `description`)
VALUES ('delete_archived_records_days', 'Delete Archived Records after x Days', 'administrative', 'false', 'The amount of days old an archived record is until it is deleted systematically.');

INSERT INTO `settings_values` (`settings_key`, `value`)
VALUES ('delete_archived_records_days', '180');