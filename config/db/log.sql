CREATE TABLE `u93257p88111_vst`.`log` ( `id` INT NOT NULL AUTO_INCREMENT , `timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , `user_id` INT NOT NULL , `activity` VARCHAR(255) NOT NULL , `category` VARCHAR(255) NOT NULL , `message` VARCHAR(255) NOT NULL , `ref_log_id` INT NULL , PRIMARY KEY (`id`), INDEX (`user_id`)) ENGINE = InnoDB;
