# Requirements

Functionality of the app is designed according the following use cases.

General requirement: During any of the test cases the code shall not throw any errors.

| Topics | Description |
|--------|-------------|
| Category | Happy flow / unhappy flow |
| Scenario | The action that the user does |
| Back-end logic | What is expected to happen in the back-end |
| Expected behavior | What the expected result is that the end user sees |
| Status | Status of this test case [not yet implemented/to be validated/validated] |

## 1. Event-appointment sync functionality

### Case 1.1

Happy flow: Book a new event in VirtuaGym, then sync.

**Expected behavior**

-   On 1st sync new appointment added in calendar
-   After 1nd sync no duplicate appointments are added to the calendar

**Status**
Validated
### Case 1.2

Happy flow: Cancelled an event in VirtuaGym, then sync.

**Expected behavior**

On 1st sync appointment is removed from calendar

**Status**
Validated

### Case 1.3

Appointment is removed from calendar by user, then sync.

**Expected behavior**

On 1st sync new appointment is created

**Status**
Validated

### Case 1.4

Event is cancelled via VirguaGym and appointment is removed from agenda by user, then sync.

**Expected behavior**

No effect on end user

**Status**
Validated

## 2. Account set-up (settings)

### Case 2.1

Happy flow: User creates a new account

**Status**
Not yet implemented

### Case 2.2

Happy flow: User inputs VirtuaGym credentials and tests connection 

**Expected behavior**

- When testing connection: Display status message if credentials are valid
- When saving credentials: 
    - Valid credentials: Display status message that credentials are saved. If calendar connection can also be made then enable AutoSync.
    - Invalid credentials: display status message that credentials are not saved and not valid.

**Status**
To be validated
### Case 2.3

Happy flow: User inputs Google calendar details and tests connection

**Expected behavior**

    - Valid credentials: Display status message that credentials are saved. If VirtuaGym connection can also be made then enable AutoSync.
    - Invalid credentials: display status message that credentials are not saved and not valid.

**Status**
To be validated

### Case 2.4

Happy flow:
User enables master switch

**Expected behavior**
Test both VG connection & calendar connection
- If connection can be made: Enable AutoSync
- If connection can't be made: Inform user and disable AutoSync

## 3. Sync flow

### Case 3.x

Unhappy flow: 
During auto sync a connection can't be made to either VirtuaGym or calendar provider

**Expected behavior**

AutoSync is disabled by the script.

**Status**
To be validated
