CREATE TABLE `act_to_apt` ( `id` INT NOT NULL AUTO_INCREMENT , `user_id` INT(11) NOT NULL , `act_inst_id` INT(32) NOT NULL , `appointment_id` VARCHAR(255) NOT NULL, PRIMARY KEY (`id`) , INDEX (`user_id`), INDEX (`act_inst_id`), INDEX (`appointment_id`)) ENGINE = InnoDB;

ALTER TABLE `act_to_apt` CHANGE `act_inst_id` `act_inst_id` BIGINT NOT NULL;