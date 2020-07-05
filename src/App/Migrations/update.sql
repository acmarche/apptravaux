ALTER TABLE `intervention` CHANGE `created` `created_at` DATETIME NOT NULL;

ALTER TABLE `intervention` CHANGE `updated` `updated_at` DATETIME NOT NULL;

ALTER TABLE `suivis` CHANGE `created` `created_at` DATETIME NOT NULL;

ALTER TABLE `suivis` CHANGE `updated` `updated_at` DATETIME NOT NULL;

ALTER TABLE `avaloir` CHANGE `created` `created_at` DATETIME NOT NULL;

ALTER TABLE `avaloir` CHANGE `updated` `updated_at` DATETIME NOT NULL;

UPDATE`intervention` SET current_place = 'published' WHERE current_place = '{"published":1}';
UPDATE`intervention` SET current_place = 'auteur_checking' WHERE current_place = '{"auteur_checking":1}';
UPDATE`intervention` SET current_place = 'admin_checking' WHERE current_place = '{"admin_checking":1}';