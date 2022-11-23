CREATE TABLE `clubs` ( `id` INT NOT NULL AUTO_INCREMENT , `user_id` INT NOT NULL , `club_id` INT NOT NULL , PRIMARY KEY (`id`), INDEX (`user_id`)) ENGINE = InnoDB;

ALTER TABLE `clubs` ADD `club_name` TEXT NULL ;

ALTER TABLE `clubs` ADD `address` VARCHAR(255) NOT NULL AFTER `club_name`, ADD `street` VARCHAR(255) NOT NULL AFTER `address`, ADD `zip_code` VARCHAR(255) NOT NULL AFTER `street`, ADD `city` VARCHAR(255) NOT NULL AFTER `zip_code`, ADD `club_description` TEXT NOT NULL AFTER `city`;

ALTER TABLE `clubs` CHANGE `club_name` `name` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;

ALTER TABLE `clubs` CHANGE `club_id` `club_id` INT NOT NULL;

ALTER TABLE `clubs` CHANGE `address` `full_address` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;