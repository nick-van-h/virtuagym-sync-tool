CREATE TABLE `u93257p88111_vst`.`act_def` ( `id` INT NOT NULL AUTO_INCREMENT , `activity_id` INT NOT NULL , `name` VARCHAR(255) NOT NULL , `deleted` INT NOT NULL , `club_id` INT NOT NULL , `duration` INT NOT NULL , PRIMARY KEY (`id`), INDEX (`activity_id`)) ENGINE = InnoDB;

ALTER TABLE `act_def` ADD `user_id` INT NOT NULL AFTER `id`, ADD INDEX (`user_id`);