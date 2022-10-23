# virtuagym-sync-tool
Server side tool to enable one way sync of VirtuaGym appointments to personal calendar

# Requirements

Create file *web/modules/config.php* with content:
```
const CONFIG_PATH = '/xxx/xxx/xxx'
const CONFIG_FILE = CONFIG_PATH . 'yyy.ini';
```
Create file */xxx/xxx/xxx/yyy.ini* with content:
```
host = "localhost"
username = "xxx"
password = "yyy"
database = "zzz"
encryption_iv = "aaa"
virtuagym_api_key = "bbb"
```
Update according set-up

# Resources & references

Logo generated with [Textcraft](https://textcraft.net/)


# API call results

### User activities

Array
(
    [0] => stdClass Object
        (
            [act_inst_id] => 2164905328
            [order] => 1
            [done] => 1
            [deleted] => 0
            [timestamp_edit] => 1663693299
            [rest_after_exercise] => 30
            [act_id] => 141108
            [kcal] => 511
            [user_id] => 6208394
            [rpe] => 0
            [event_id] => 1780549267-62b589fe00cda6-30585645
            [timestamp] => 1663668000
            [distance] => 0
            [speed] => 0
            [duration] => 3600
            [superset_with_next_act] => 
        )
)

### Gym events

stdClass Object
(
    [statuscode] => 200
    [statusmessage] => Everything OK
    [result_count] => 464
    [timestamp] => 1666264071
    [result] => Array
        (
            [0] => stdClass Object
                (
                    [event_id] => 1754855709-62b5924b036943-67919712
                    [activity_id] => 99644
                    [schedule_id] => 1
                    [event_start] => 1662012000
                    [event_end] => 1662015600
                    [attendees] => 0
                    [max_attendees] => 4
                    [joined] => 0
                    [joinable] => 1
                    [deleted] => 0
                    [canceled] => 0
                    [cancel_before_duration] => 0
                    [waiting_list_enabled] => 1
                    [can_join_waiting_list] => 1
                    [hide_participants_amount] => 0
                    [only_managers_book_members] => 0
                    [bookable_from_timestamp] => 1661148000
                    [club_id] => 16389
                    [instructors] => Array
                        (
                        )

                    [is_instructor_pic] => 
                    [service_cost] => 1
                    [service_id] => strip
                )
        )
)

# Gym activity definitions

stdClass Object
(
    [statuscode] => 200
    [statusmessage] => Everything OK
    [result_count] => 51
    [timestamp] => 1666291377
    [result] => Array
        (
            [0] => stdClass Object
                (
                    [id] => 49990
                    [name] => WOD
                    [url_id] => groepstraining-wod
                    [searchfield] => WOD    Groepstraining (WOD)   
                    [type] => 0
                    [difficulty] => 2
                    [video] => 
                    [video_female] => 
                    [img] => e06b2796a66eb89bda581660bd49077eb76e.jpg
                    [img_female] => 
                    [thumb] => 9f93ce086c1cfa4927bfb5c8e72d3e25b4c5.jpg
                    [thumb_female] => 
                    [icon] => 50394f91480d528de18ef4dc78c3871a9edc.jpg
                    [order] => 0
                    [pro] => 0
                    [gps_trackable] => 0
                    [met] => 7
                    [is_class] => 1
                    [read_only] => 0
                    [rest_after_exercise] => 30
                    [addable] => 1
                    [available_on_kiosk] => 0
                    [kiosk_rotation] => 20
                    [yoga_exercise] => 0
                    [special_female_animation] => 0
                    [standing_animation] => 0
                    [avatar_scale] => 1
                    [deleted] => 0
                    [club_id] => 16389
                    [category] => strength-dynamic
                    [content_type] => 0
                    [has_distance] => 0
                    [duration] => 1800
                )
        )
)