installation and some description goes here.


### Senior Backend Engineer Test Task


**Requirements**

1. User is able to specify price depending on the dates. i.e. set the price $x for the
dates range $startDate - $endDate. Let’s call such ranges as intervals.
1. User can add as many intervals as he wants (using any start/end date).
2. System can’t have crossing intervals.
3. New interval price have higher priority over existing ones.
4. New interval can’t lead to changes in dates not belonged to its dates range.
5. If user tries to save interval that interferes with existing ones in DB, system has to apply last user changes and modify other intervals in order to apply requirement 3.
6. Any intervals with the same price that can be merged (without gaps between)
should be merged.


DB
You need to develop a system for saving prices in intervals. Each interval should have
such fields:
* date_start (date)
* date_end (date)
* price (float)




**What you need to do**

* Develop DB structure (add MySQL dump file to the code)
* Implement 3 API methods to insert, update and delete intervals
* Provide a simple interface which will show all intervals sorted by date_start and
that will allow performing all CRUD operations
* Add a feature to clear all DB to start testing from the scratch
* Unit tests for what you consider to be necessary
* As minimum queries to DB as possible
* Don’t use any frameworks
* Well documented code
* Code must be allocated on Github (please provide the link)
* Application should be deployed to some server, ready for manual testing, URL
should be provided.



**Examples**

For intervals in examples used format (date_start-date_end:price), for example
(1-10:15), that means that date_start = 1, date_end = 10, price = 15


**Example 1**


| Step | Operation     | Result                          |
|------|---------------|---------------------------------|
| 1    | Add (1-10:15) | (1-10:15)                       |
| 2    | Add (5-20:15) | (1-20:15)                       |
| 3    | Add (2-8:45)  | (1-1:15), (2-8:45), (9-20:15)   |
| 4    | Add (9-10:45) | (1-1:15), (2-10:45), (11-20:15) |



**Example 2**


| Step | Operation      | Result                          |
|------|----------------|---------------------------------|
| 1    | Add (1-5:15)   | (1-5:15)                        |
| 2    | Add (20-25:15) | (1-5:15), (20-25:15)            |
| 3    | Add (4-21:45)  | (1-3:15), (4-21:45), (22-25:15) |
| 4    | Add (3-21:15)  | (1-25:15)                       |