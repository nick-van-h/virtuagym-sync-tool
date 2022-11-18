# VirtuaGym API

## Official documentation

[VirtuaGym API readme](https://github.com/virtuagym/Virtuagym-Public-API/blob/master/README.md)

[VirtuaGym API Wiki](https://github.com/virtuagym/Virtuagym-Public-API/wiki)

## API call results

### User profile

```
stdClass Object
(
    [id] => 6208394
    [email] => john.doe@example.com
    [username] => John-doe
    [username_url] => john-doe
    [length_unit] => cm
    [weight_unit] => kg
    [user_avatar] =>
    [pro] => 1
    [language] => nl
    [activated] => 1
    [timestamp_edit] => 1666813653
    [cover_photo] => /images/cover_photos/cover-photo-4.png
    [total_kcal] => 81892
    [total_min] => 8010
    [total_km] => 0
    [fitness_points] => 104875
    [nr_likes] => 0
    [has_coach] => 1
    [nr_followers] => 0
    [nr_following] => 0
    [member_ids] => Array
        (
            [0] => stdClass Object
                (
                    [club_id] => xxx
                    [member_id] => yyyy
                    [member_pro] => 0
                )

            [1] => stdClass Object
                (
                    [club_id] => xxx
                    [member_id] => yyyy
                    [member_pro] => 1
                )

        )

    [content_language] => nl
    [birthday] => dd-mm-yyyy
    [length] => xxx
    [weight] => xx
    [country] => NL
    [city] => Knowhere
    [timezone] => Europe/Amsterdam
    [firstname] => John
    [lastname] => Doe
    [name] => John Doe
    [gender] => m
    [club_ids] => Array
        (
            [0] => xxx
            [1] => xxx
        )

    [member_id] => yyyy
    [selected_bodymetrics] => Array
        (
            [0] => weight
            [1] => waist
            [2] => fat
            [3] => visceral
            [4] => fat_free_mass
            [5] => bmi
        )

)
```

### User activities

```
Array
(
    [0] => stdClass Object
        (
            [act_inst_id] => xxxxxx (bigint)
            [order] => 1
            [done] => 1
            [deleted] => 0
            [timestamp_edit] => 1663693299
            [rest_after_exercise] => 30
            [act_id] => xxx
            [kcal] => 511
            [user_id] => xxx
            [rpe] => 0
            [event_id] => xx-yy-zz
            [timestamp] => 1663668000
            [distance] => 0
            [speed] => 0
            [duration] => 3600
            [superset_with_next_act] =>
        )
)
```

### Gym events

```
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
                    [event_id] => xx-yy-zz
                    [activity_id] => xxx
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
                    [club_id] => xxx
                    [instructors] => Array
                        (
                        )

                    [is_instructor_pic] =>
                    [service_cost] => 1
                    [service_id] => strip
                )
        )
)
```

# Gym activity definitions

```
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
                    [id] => xxx
                    [name] => str
                    [url_id] => str
                    [searchfield] => str
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
                    [club_id] => xxx
                    [category] => strength-dynamic
                    [content_type] => 0
                    [has_distance] => 0
                    [duration] => 1800
                )
        )
)
```
