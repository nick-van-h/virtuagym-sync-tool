CREATE TABLE `u93257p88111_vst`.`users` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `username` VARCHAR(255) NOT NULL , `password_hash` VARCHAR(255) NOT NULL , PRIMARY KEY (`id`), INDEX (`username`)) ENGINE = InnoDB;


ALTER TABLE `settings` CHANGE `value_str` `value_str` VARCHAR(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL;