CREATE TABLE `clubs` ( `id` INT NOT NULL AUTO_INCREMENT , `user_id` INT NOT NULL , `club_id` INT NOT NULL , PRIMARY KEY (`id`), INDEX (`user_id`)) ENGINE = InnoDB;

ALTER TABLE `clubs` ADD `club_name` TEXT NULL ;