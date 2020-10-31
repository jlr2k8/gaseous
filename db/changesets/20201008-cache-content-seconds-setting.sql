INSERT INTO `settings` (`key`, `display`, `category_key`, `role_based`, `description`)
VALUES ('cache_content_seconds', 'Content Cache Time (sec)', 'administrative', 'false', 'The amount of time for CMS content to be cached for anonymous users (in seconds).');

INSERT INTO `settings_values` (`settings_key`, `value`)
VALUES ('cache_content_seconds', '3600');