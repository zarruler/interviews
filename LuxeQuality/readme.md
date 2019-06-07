[SENIOR PHP DEVELOPER TASK DESCRIPTION](./task.md)


Solution:

PHP task
=======
### 3'd Party tools
1. used `Composer` for auto loading classes
2. used `Docker` for the php-cli container

### Application Structure
1. ####Folder structure
 * **app** - main directory with the application sources
     * **Classes** - directory with the application classes
         * **Commands** - directory with the classes related to the `Command Pattern`
         * **Workers** - directory with the classes which do some "job" 
     * **Interfaces** - directory with the interfaces
     * **Resources** - directory with the language/message files 
 * **public**  
    directory for the user/client related files
 * **vendor**  
    composer directory to store packages
2. ####Description
    **Architectural solutions** 
    * Command design pattern
    * Factory method pattern
    * mix of Worker and Value object patterns (workers made immutable)
    * was thinking about Registry pattern, but Factory method finally 
      fit this task perfectly
    * was following SOLID principles
    * was used Dependency Injection technique
    * added 2 resource/message files and simple function to work with 
      them inside Helper (yep helper :) ) just to not hard code messages among the code.  
 
    **How it works**
    
    `public/index.php` accept 2 cli parameters:   
     a) command - available 2 commands: "thread" and "MCP"   
     b) process name - any varchar name  
        
     these 2 parameters transferred to the factory and depending on the command will be 
     executed one of workers:  
     
     `thread` - execute **WorkerLoop** which will do 5 loops with the 60 secs pause.  
     When WorkerLoop start doing his job it sets the process title with the help of 
     `cli_set_process_title` php function.
     
     `MCP` - execute **WorkerCheckStatus** which looking for the process by process title
     with the help of system command `pidof` and according to the search result shows related message.
     if task would ask to determine process in different ways i would use Strategy design pattern, but
     this wasn't required so lets follow YAGNI principle.
     
     **How to test**
     
     * **using docker**
        * start php_cli container by executing `start.sh` from the project root   
        * then from the `public` folder execute `process` script which run `index.php` though docker container
          and give it two params - _command_ and _process name_ described above  
          examples:  
           `# ./process thread hello`  
           `# ./process MCP hello` 
     * **using pure php**  
         all you need just run `index.php` with php cli.            
          examples:  
           `# php index.php thread hello`  
           `# php index.php MCP hello` 
   
JS task
=======
1. ####How to test
open in the browser `js.html` located in the `public` folder.

2. ####Description
was used JQuery to speed up work with the DOM.  

* I decided to create a class for each form element
* used kind of Value object for the form elements' attributes 
to keep persistence of received data so objects can rely on the data. 
* Factory return form elements depending on the JSON `t` key.
* depending on the JSON number of data sets will be drown appropriate number of buttons
* each dynamically created button assigned action to dynamically generate form elements 
  and fill them with the data depending on JSON data format and values.
* JSON format:  
  `t` - type  
  `v` - value  
  if `t` = `radio`button or `check`box then `v`alue not a single string but set of `name:checked_flag`  
  `0` = not checked  
  `1` = checked
* if JSON is reliable i suppose that if `v`alue is set of data then it is `radio`button or `check`box
   so im just checking it for the `instanceof Object`.  
   if JSON not reliable i would check every key and value if needed.
   in the task description not clarified if data is reliable or not.    

 
