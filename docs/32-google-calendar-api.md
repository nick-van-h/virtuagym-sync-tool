# API call results

### Reference documentation

https://developers.google.com/identity/gsi/web/guides/overview

https://developers.google.com/identity/protocols/oauth2/web-server

https://github.com/googleapis/google-api-php-client

https://developers.google.com/calendar/api/v3/reference

### listCalendarList

```
Google\Service\Calendar\CalendarList Object
(
    [internal_gapi_mappings:protected] => Array ()
    [modelData:protected] => Array ()
    [processed:protected] => Array ()
    [collection_key:protected] => items
    [etag] => "xxx"
    [itemsType:protected] => Google\Service\Calendar\CalendarListEntry
    [itemsDataType:protected] => array
    [kind] => calendar#calendarList
    [nextPageToken] =>
    [nextSyncToken] => xxx
    [items] => Array
        (
            [0] => Google\Service\Calendar\CalendarListEntry Object
                (
                    [internal_gapi_mappings:protected] => Array ()
                    [modelData:protected] => Array ()
                    [processed:protected] => Array ()
                    [collection_key:protected] => defaultReminders
                    [accessRole] => owner
                    [backgroundColor] => #16a765
                    [colorId] => 8
                    [conferencePropertiesType:protected] => Google\Service\Calendar\ConferenceProperties
                    [conferencePropertiesDataType:protected] =>
                    [defaultRemindersType:protected] => Google\Service\Calendar\EventReminder
                    [defaultRemindersDataType:protected] => array
                    [deleted] =>
                    [description] =>
                    [etag] => "xxx"
                    [foregroundColor] => #000000
                    [hidden] =>
                    [id] => xxx
                    [kind] => calendar#calendarListEntry
                    [location] =>
                    [notificationSettingsType:protected] => Google\Service\Calendar\CalendarListEntryNotificationSettings
                    [notificationSettingsDataType:protected] =>
                    [primary] => 1
                    [selected] => 1
                    [summary] => xxx
                    [summaryOverride] =>
                    [timeZone] => Europe/Amsterdam
                    [notificationSettings] => Google\Service\Calendar\CalendarListEntryNotificationSettings Object
                        (
                            ...
                        )
                )
        )
)
```

Events

```
Array
(
    [0] => Google\Service\Calendar\Event Object
        (
            [colorId] =>
            [created] => 2022-10-13T13:38:06.000Z
            [description] =>
            [etag] => "xxx"
            [eventType] => default
            [iCalUID] => 123xy@google.com
            [id] => 123xy
            [kind] => calendar#event
            [location] => xxx
            [recurrence] =>
            [recurringEventId] =>
            [sequence] => 1
            [status] => confirmed
            [summary] => xxx
            [transparency] => transparent
            [updated] => 2022-11-07T11:29:05.624Z
            [visibility] =>
            [creator] => Google\Service\Calendar\EventCreator Object=
                    [displayName] =>
                    [email] => xxx
            [organizer] => Google\Service\Calendar\EventOrganizer Object
                    [displayName] =>
                    [email] => xxx
            [start] => Google\Service\Calendar\EventDateTime Object
                    [date] =>
                    [dateTime] => 2022-11-07T15:00:00+01:00
                    [timeZone] => Europe/Amsterdam
            [end] => Google\Service\Calendar\EventDateTime Object
                    [date] =>
                    [dateTime] => 2022-11-07T15:30:00+01:00
                    [timeZone] => Europe/Amsterdam
            [reminders] => Google\Service\Calendar\EventReminders Object
        )
)
```
