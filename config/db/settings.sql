CREATE TABLE `settings` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `user_id` INT(11) NOT NULL , `variable` VARCHAR(255) NOT NULL , `value_str` VARCHAR(255) NULL , `value_int` INT(11) NULL , `type` VARCHAR(3) NOT NULL COMMENT 'str/int' , PRIMARY KEY (`id`), INDEX (`user_id`)) ENGINE = InnoDB;

ALTER TABLE `settings` CHANGE `variable` `setting_name` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;

INSERT INTO `settings`(`user_id`, `setting_name`, `value_str`, `type`) VALUES (1,"user_role","admin","str");

INSERT INTO `settings`(`user_id`, `setting_name`, `value_str`, `type`) VALUES ('1','password_reset_token','CorrectHorseBatteryStaple','str');