UPDATE settings
SET description = 'The amount of time for CMS content to be cached for anonymous users (in seconds). To disable caching, set this value to -1.'
WHERE `settings`.`key` = 'cache_content_seconds';