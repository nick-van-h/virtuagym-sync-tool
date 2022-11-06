
# API call results

### Reference documentation

https://developers.google.com/identity/protocols/oauth2/web-server#creatingclient

https://developers.google.com/identity/protocols/oauth2/web-server#httprest_3

https://developers.google.com/identity/gsi/web/guides/overview

https://developers.google.com/identity/protocols/oauth2/native-app#offline

https://github.com/googleapis/google-api-php-client

https://console.cloud.google.com/apis/credentials?project=virtuagym-sync-tool

https://developers.google.com/identity/protocols/oauth2/web-server#authorization-errors-redirect-uri-mismatch

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