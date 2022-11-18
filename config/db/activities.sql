CREATE TABLE `activities` ( `id` INT(32) NOT NULL AUTO_INCREMENT , `user_id` INT(11) NOT NULL , `act_inst_id` INT(32) NOT NULL , `done` INT(1) NOT NULL , `deleted` INT(1) NOT NULL , `act_id` INT(32) NOT NULL , `event_id` VARCHAR(64) NOT NULL , PRIMARY KEY (`id`), INDEX (`user_id`), INDEX (`act_id`), INDEX (`event_id`)) ENGINE = InnoDB;

ALTER TABLE `activities` CHANGE `id` `id` INT NOT NULL AUTO_INCREMENT, CHANGE `act_inst_id` `act_inst_id` BIGINT NOT NULL, CHANGE `act_id` `act_id` BIGINT NOT NULL;

ALTER TABLE `activities` ADD `timestamp` INT NOT NULL AFTER `event_id`;