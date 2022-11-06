# Datamodel

Under construction

## Log
| ID | Date/Time        | User ID | Trigger         | Category | Message             | Ref. log ID |
|----|------------------|---------|-----------------|----------|---------------------|-------------|
|  1 | 31-10-2021 18:33 |       1 | Scheduled sync  | Event    | Sync start          | NULL        |
|  2 | 31-10-2021 18:33 |       1 | Get data        | API-call | Requested {api1}    | 1           |
|  3 | 31-10-2021 18:33 |       1 | Get data        | API-call | Requested {api2}    | 1           |
|  4 | 31-10-2021 18:33 |       1 | Scheduled sync  | Event    | Sync complete       | 1           |
|  5 | 31-10-2021 18:33 |       1 | Update settings | Event    | Password updated    | NULL        |
|  6 | 31-10-2021 18:33 |       1 | Login           | Warning  | Invalid credentials | NULL        |
|  7 | 31-10-2021 18:33 |       1 | Login           | Event    | New login           | NULL        |
|  8 | 31-10-2021 18:33 |       1 | Login           | Event    | Logn with cookies   | NULL        |
|  9 | 31-10-2021 18:33 |       1 | Manual sync     | Error    | Unauthorized        | NULL        |

**Categories**
Event
Warning
Error
API-call