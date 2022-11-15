CREATE TABLE `appointments` ( `id` INT NOT NULL AUTO_INCREMENT , `user_id` INT(11) NOT NULL , `appointment_id` VARCHAR(255) NOT NULL, `agenda_id` VARCHAR(255) NOT NULL , PRIMARY KEY (`id`) , INDEX (`user_id`), INDEX (`appointment_id`)) ENGINE = InnoDB;

--ALTER TABLE `appointments` ADD `summary` VARCHAR(255) NOT NULL AFTER `agenda_id`, ADD `date` VARCHAR(255) NOT NULL AFTER `summary`, ADD `start_time` VARCHAR(255) NOT NULL AFTER `date`, ADD `end_time` VARCHAR(255) NOT NULL AFTER `start_time`;