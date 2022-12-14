CREATE TABLE `evt_def` ( `id` INT NOT NULL AUTO_INCREMENT , `user_id` INT NOT NULL , `event_id` VARCHAR(255) NOT NULL , `activity_id` INT NOT NULL , `event_start` INT NOT NULL , `event_end` INT NOT NULL , `attendees` INT NOT NULL , `max_attendees` INT NOT NULL , `joined` INT NOT NULL , `deleted` INT NOT NULL , `cancelled` INT NOT NULL , `bookable_from_timestamp` INT NOT NULL , PRIMARY KEY (`id`), INDEX (`user_id`), INDEX (`event_id`), INDEX (`activity_id`)) ENGINE = InnoDB;

ALTER TABLE `evt_def` CHANGE `bookable_from_timestamp` `bookable_from` INT NOT NULL;

ALTER TABLE `evt_def` DROP `user_id`;