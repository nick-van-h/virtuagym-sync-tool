DELETE FROM `settings` WHERE `id` IN
(
    SELECT `id` FROM 
 		(
            SELECT 
     			`id`, 
     			ROW_NUMBER() OVER (PARTITION BY `user_id`,`setting_name`) AS `rownr` 
     		FROM `settings`
    	) s
    WHERE s.`rownr` > 1
)