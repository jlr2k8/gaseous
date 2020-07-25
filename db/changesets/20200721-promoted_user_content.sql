ALTER TABLE `content_body_types`
ADD `promoted_user_content` enum('0','1') COLLATE 'utf8mb4_unicode_ci' NOT NULL DEFAULT '0' AFTER `description`;

UPDATE content_body_types SET promoted_user_content = '1' WHERE type_id = 'blog_article';