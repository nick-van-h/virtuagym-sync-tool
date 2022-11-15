CREATE TABLE `required_settings` ( `id` INT(255) NOT NULL AUTO_INCREMENT , `setting_name` VARCHAR(255) NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;

ALTER TABLE `required_settings` ADD UNIQUE (`setting_name`);

INSERT INTO `required_settings`(`setting_name`) VALUES ('user_role'),('key_enc'),('virtuagym_username_enc'),('virtuagym_password_enc')

