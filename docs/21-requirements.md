# Requirements

Functionality of the app is designed according the following use cases.

General requirement: During any of the test cases the code shall not throw any errors.

## 1. Event-appointment sync functionality

### Case 1.1

Happy flow: Book a new event in VirtuaGym, then sync.

**Expected behavior**

-   On 1st sync new appointment added in calendar
-   After 1nd sync no duplicate appointments are added to the calendar

### Case 1.2

Happy flow: Cancelled an event in VirtuaGym, then sync.

**Expected behavior**

On 1st sync appointment is removed from calendar

### Case 1.3

Appointment is removed from calendar by user, then sync.

**Expected behavior**

On 1st sync new appointment is created

### Case 1.4

Event is cancelled via VirguaGym and appointment is removed from agenda by user, then sync.

**Expected behavior**

No effect on end user

## Account set-up

TBD
