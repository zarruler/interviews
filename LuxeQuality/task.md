Task 1: (PHP)
============

Write a php script that reports if a separate process is already running on the same machine.

Do not use any persistent storage to communicate between the processes. (DB, FileSystem etc)

PHP - Using one commend-line php script (no web server involved) create two execution paths based on the input arguments

If argument 1 = “thread" - execute a loop five times that sleeps 60 second between each loop

If argument 1 = "MCP" - print the status of the first process (running or NOT running)



Run the scripts in two shell windows on the same machine.

host% php php_script.php thread argN <--- start a process that loops 5 times and sleeps for 60 seconds between each loop

host% php php_script.php MCP argN <- check to see if the first process (thread argN) is running and report the status

if argN is running OR argN is NOT running <- output

Document your design and code (don’t use a framework for this task)



Task 2: (Javascript)
====================

Using client-side JavaScript (no http server required)

Create one Json array to represent the following two sets of data

Array = Set 1 age = 25, speed = 55, color = red, fruit = apple(checked), orange, watermelon(checked), peach

Set 2 age = 35, speed = 45, color = green, fruit = apple, banana(checked), blueberry, strawberry(checked), kiwi

Create form elements: input (age), input(speed), radio (color), multi-select (fruit), button1 and button2



Using a data driven approach connect the Array Data and the Form Elements. 

Do not hardcode the values/tags of the Array in your JavaScript, the Array can change but your code should not have to. 

On page load - load and assign the items in Set 1 to the form elements.

Press button2 load and assign the items in Set 2 to the form elements

Press button1 load and assign the items in Set 1 to the form elements

Repeat button2 and button1

Document your design and code (don’t assume any framework for this task)



Please let me know if you have any questions with these tasks.



Please submit both tasks in a single zip file named task-yourname.zip



Could you please complete the tasks and submit your completed code within two days?