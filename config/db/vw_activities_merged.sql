CREATE
 ALGORITHM = UNDEFINED
 VIEW `vw_activities_merged`
 AS SELECT DISTINCT 
                    a.`user_id`,
                    a.`act_inst_id`,
                    a.`done`,
                    a.`deleted`,
                    a.`act_id`,
                    a.`event_id`,
                    a.`timestamp`,
                    ad.`activity_id`,
                    ad.`name`,
                    ad.`deleted` as actdef_deleted,
                    ad.`club_id`,
                    ad.`duration`,
                    ed.`event_start`,
                    ed.`event_end`,
                    ed.`attendees`,
                    ed.`max_attendees`,
                    ed.`joined`,
                    ed.`deleted` as evtdef_deleted,
                    ed.`cancelled`,
                    ed.`bookable_from`
                FROM `activities` a
                LEFT JOIN `act_def` ad ON a.act_id = activity_id
                LEFT JOIN `evt_def` ed on a.event_id = ed.event_id
                ORDER BY ed.event_start DESC;